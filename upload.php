<?php

// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.


/**
 * Accept uploading files by web service token
 * uploads directly into the file submission plugin
 * 
 * POST params:
 *  token => the web service user token (needed for authentication)
 *  assign_id => the id of the codehandin assignment we are going to use
 *  test => 1 if this upload is for a test, 0 for a submission
 *  filepath => the private file area path (where files will be stored)
 *  [_FILES] => for example you can send the files with <input type=file>,
 *              or with curl magic: 'file_1' => '@/path/to/file', or ...
 *
 * @package    core_webservice
 * @copyright  2011 Dongsheng Cai <dongsheng@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
/**
 * AJAX_SCRIPT - exception will be converted into JSON
 */
define('AJAX_SCRIPT', true);

/**
 * NO_MOODLE_COOKIES - we don't want any cookie
 */
define('NO_MOODLE_COOKIES', true);

require_once(dirname(dirname(dirname(dirname(dirname(__FILE__))))) . '\config.php');
require_once($CFG->dirroot . '\webservice\lib.php');
require_once($CFG->libdir . 'filelib.php');
require_once($CFG->dirroot . 'mod\assign\externallib.php');
//require_once($CFG->dirroot . 'mod\assign\locallib.php');//contained in above

// params
$filepath = optional_param('filepath', '/', PARAM_PATH);
$reallysubmit = optional_param('reallysubmit', false, PARAM_BOOL);
$token = required_param('token', PARAM_ALPHANUM);
$assignmentid = required_param('assignmentid', PARAM_INT);
$draftid = optional_param('draftid', file_get_unused_draft_itemid(), PARAM_INT);
// the [_FILES] param is not mentioned?

// authenticate the user
$webservicelib = new webservice();
$authenticationinfo = $webservicelib->authenticate_user($token);

// check the user can submit files
$context = context_user::instance($USER->id);
require_capability('mod/assign:submit', $context);




//check if the assignment is still open to submissions
// uses mod\assign\locallib.php
submissions_open($userid = 0, $skipenrolled = false, $submission = false, $flags = false, $gradinginfo = false);

$results = ["error" => "You have already submitted this assignment"];


// If the student has submitted this assignment before
// Don't let them overwrite the file in use, instead make a new file
if ($DB->record_exists('codehandin_submission', array('userid' => $USER->id, 'aid' => $assign_id))
) {
    echo json_encode(["error" => "You have already submitted this assignment"]);
    return;
}
#$fs = get_file_storage();

$totalsize = 0;
$files = array();
$results = [];
foreach ($_FILES as $fieldname => $uploaded_file) {
    // check upload errors
    if (!empty($_FILES[$fieldname]['error'])) {
        switch ($_FILES[$fieldname]['error']) {
            case UPLOAD_ERR_INI_SIZE:
                throw new moodle_exception('upload_error_ini_size', 'repository_upload');
            case UPLOAD_ERR_FORM_SIZE:
                throw new moodle_exception('upload_error_form_size', 'repository_upload');
            case UPLOAD_ERR_PARTIAL:
                throw new moodle_exception('upload_error_partial', 'repository_upload');
            case UPLOAD_ERR_NO_FILE:
                throw new moodle_exception('upload_error_no_file', 'repository_upload');
            case UPLOAD_ERR_NO_TMP_DIR:
                throw new moodle_exception('upload_error_no_tmp_dir', 'repository_upload');
            case UPLOAD_ERR_CANT_WRITE:
                throw new moodle_exception('upload_error_cant_write', 'repository_upload');
            case UPLOAD_ERR_EXTENSION:
                throw new moodle_exception('upload_error_extension', 'repository_upload');
            default:
                throw new moodle_exception('nofile');
        }
    }
    $file = new stdClass();
    $file->filename = clean_param($_FILES[$fieldname]['name'], PARAM_FILE);
    // check system maxbytes setting
    if (($_FILES[$fieldname]['size'] > get_max_upload_file_size($CFG->maxbytes))) {
        // oversize file will be ignored, error added to array to notify
        // web service client
        $file->errortype = 'fileoversized';
        $file->error = get_string('maxbytes', 'error');
    } else {
        $file->filepath = $_FILES[$fieldname]['tmp_name'];
        // calculate total size of upload
        $totalsize += $_FILES[$fieldname]['size'];
    }
    $files[] = $file;
}

$fs = get_file_storage();

$usedspace = 0;
$privatefiles = $fs->get_area_files($context->id, 'assignsubmission_codehandin', 'private', false, 'id', false);
foreach ($privatefiles as $file) {
    $usedspace += $file->get_filesize();
}

if ($totalsize > ($CFG->userquota - $usedspace)) {
    throw new file_exception('userquotalimit');
}

foreach ($files as $file) {
    if (!empty($file->error)) {
        // including error and filename
        $results[] = $file;
        continue;
    }
    $file_record = new stdClass;
    $file_record->component = 'user';
    $file_record->contextid = 5;
    $file_record->userid = $USER->id;
    $file_record->filearea = 'draft';
    $file_record->filename = $file->filename;
    $file_record->filepath = $filepath;
    $file_record->itemid = $draftid;
    $file_record->license = $CFG->sitedefaultlicense;
    $file_record->author = fullname($authenticationinfo['user']);
    $file_record->source = '';

    //Check if the file already exist
    /*
     * the externallib can handle updating the files, since we don't want to let the student override
     * any existing submission files.
     * If they attempt to upload a file after a submission has been made, the old submission will
     * be retained and the new upload deleted
     */
    $existingfile = $fs->file_exists($file_record->contextid, $file_record->component, $file_record->filearea, $file_record->itemid, $file_record->filepath, $file_record->filename);
    if ($existingfile) {
        // Delete the old file and put the new one in.
        $oldFile = $fs->get_file($file_record->contextid, $file_record->component, $file_record->filearea, $file_record->itemid, $file_record->filepath, $file_record->filename);
        if ($oldFile) {
            $oldFile = $oldFile->delete();
        } else {
            $results['error'] = "Could not delete old file";
            continue;
        }
    }

    try {
        $stored_file = $fs->create_file_from_pathname($file_record, $file->filepath);
    } catch (Exception $e) {
        // If the file cannot be uploaded because it's already in the db
        // we give return the id of the existing file
        echo json_encode(['error' => $e->getMessage()]);
        die();
    }
    // Return the id of the stored file for later use
    $results['id'] = $stored_file->get_id();


    $plugindata = array('files_filemanager' => $draftid);

    // save the submission
    $warnings = mod_assign_external::save_submission($assignmentid, $plugindata);
    if ($warnings) {
        // check for failure warning and delete files?
        //$DB->delete_records('assignsubmission_file', array('assignment' => $this->assignment->get_instance()->id));
    }

    // submit for marking
    if ($reallysubmit) {
        submit_for_grading($assignmentid, true); //in mod/assign/externallib
    }
}
echo json_encode($results);
