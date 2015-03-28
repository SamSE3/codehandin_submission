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
 * Internal library of functions for the CodeHandIn Package
 *
 * Never include this file from your lib.php!
 *
 * @package    assignsubmission_codehandin_submission
 * @copyright  2014 Jonathan Mackenzie & Samuel Deane
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot . '/local/codehandin_webservice/locallib.php'); // add the web service libary
//require_once($CFG->dirroot . "/mod/assign/submission/locallib.php");

/**
 * Does something really useful with the passed things
 *
 * @param array $things
 * @return object
 */
//function codehandin_do_something_useful(array $things) {
//    return new stdClass();
//}

class assign_submission_codehandin_submission extends assign_submission_plugin {

    private $grade_info;

    /**
     * Get the name of the codehandin plugin
     * @return string
     */
    public function get_name() {
        return get_string('pluginname', 'assignsubmission_codehandin_submission');
    }

    /**
     * Get the codehandin submission information from the database
     *
     * @param int $submissionid
     * @return mixed
     */
    private function get_codehandin_submission($assignmentid, $userid) {
        global $DB;
        // all other details for this submission should already be supplied by parent equivalent function (assign_submisison's get_submission)
        return $DB->get_record('codehandin_submission', array('assignmentid' => $assignmentid, 'userid' => $userid));
    }

    /**
     * Set up the draft file areas before displaying the settings form
     * @param array $default_values the values to be passed in to the form
     * @todo create file_prepare_draft_area for a single file within a draft 
     * area to get the single test files
     */
    public function data_preprocessing(&$default_values) {

        $context = $this->assignment->get_context();
        if ($context) {            // existing submission
            $assignmentid = $this->assignment->get_instance()->id;

            $contextids = array();
            $contextids[$assignmentid] = $context->id;
            $CHIData = local_codehandin_webservice::fetch_assignments_raw($assignmentid, $contextids); // get the first codehandin of the first course
            if (isset($CHIData->courses)) {
                $codehandin = $CHIData->courses[0]['codehandins'][0];
                $default_values['assignsubmission_codehandin_submission_proglang'] = $codehandin['proglangid'];
                $default_values['assignsubmission_codehandin_submission_mustattemptcompile'] = $codehandin['mustattemptcompile'];
//                $draftspectestid = file_get_submitted_draft_itemid('assignsubmission_codehandin_submission_spectest');
//                $draftspectestassessmentid = file_get_submitted_draft_itemid('assignsubmission_codehandin_submission_spectestassessment');
//                file_prepare_draft_area($draftspectestid, $context->id, 'assignsubmission_codehandin_submission', CODEHANDIN_FILEAREA, $assignmentid, $this->get_file_options());
//                file_prepare_draft_area($draftspectestassessmentid, $context->id, 'assignsubmission_codehandin_submission', CODEHANDIN_FILEAREA, $assignmentid, $this->get_file_options());
                $default_values['assignsubmission_codehandin_submission_spectestonly'] = $codehandin['spectestonly'];
            }
        }
    }

    /**
     * Get the default setting for the codehandin plugin
     *
     * @param MoodleQuickForm $mform The form to add elements to
     * @return void
     */
    public function get_settings(MoodleQuickForm $mform) {
        global $DB;
        //ref the checkpoint form
        //require_once('checkpoint_form.php');
        //$assignmentid = $this->assignment->get_instance()->id;
        //echo var_dump();
        $defaultproglang = get_config('assignsubmission_codehandin_submission', 'defaultproglang');
        $mustattemptcompile = get_config('assignsubmission_codehandin_submission', 'mustattemptcompile');

        // set the programming language
        $languages = $DB->get_records_select_menu('codehandin_proglang', null, null, 'id', 'id, name');
        $mform->addElement('select', 'assignsubmission_codehandin_submission_proglang', get_string('proglang', 'assignsubmission_codehandin_submission'), $languages);
        $mform->addHelpButton('assignsubmission_codehandin_submission_proglang', 'proglang', 'assignsubmission_codehandin_submission');
        $mform->setDefault('assignsubmission_codehandin_submission_proglang', $defaultproglang);
        $mform->disabledIf('assignsubmission_codehandin_submission_proglang', 'assignsubmission_codehandin_submission_enabled', 'notchecked');

        // select if submissions must have been attemptedly compiled
        $mform->addElement('checkbox', 'assignsubmission_codehandin_submission_mustattemptcompile', get_string('mustattemptcompile', 'assignsubmission_codehandin_submission'));
        $mform->addHelpButton('assignsubmission_codehandin_submission_mustattemptcompile', 'mustattemptcompile', 'assignsubmission_codehandin_submission');
        $mform->setDefault('assignsubmission_codehandin_submission_mustattemptcompile', $mustattemptcompile);
        $mform->disabledIf('assignsubmission_codehandin_submission_mustattemptcompile', 'assignsubmission_codehandin_submission_enabled', 'notchecked');

        $mform->addElement('checkbox', 'assignsubmission_codehandin_submission_spectestonly', get_string('spectestonly', 'assignsubmission_codehandin_submission'));
        $mform->addHelpButton('assignsubmission_codehandin_submission_spectestonly', 'spectestonly', 'assignsubmission_codehandin_submission');
        $mform->disabledIf('assignsubmission_codehandin_submission_spectestonly', 'assignsubmission_codehandin_submission_enabled', 'notchecked');

//        $mform->addElement('filepicker', 'assignsubmission_codehandin_submission_spectest', get_string('spectest', 'assignsubmission_codehandin_submission'), null, $this->get_file_options());
//        //$mform->addElement('filepicker', 'taskinput_filemanager', get_string('spectest', 'assignsubmission_codehandin_submission'), null,  $this->get_file_options());
//        $mform->addHelpButton('assignsubmission_codehandin_submission_spectest', 'spectest', 'assignsubmission_codehandin_submission');
//        //$mform->addRule('assignsubmission_codehandin_submission_spectest', get_string('required'), 'required', null, 'client');
//        $mform->disabledIf('assignsubmission_codehandin_submission_spectest', 'assignsubmission_codehandin_submission_enabled', 'notchecked');
//        $mform->disabledIf('assignsubmission_codehandin_submission_spectest', 'assignsubmission_codehandin_submission_spectestonly', 'notchecked');
//
//        $mform->addElement('filepicker', 'assignsubmission_codehandin_submission_spectestassessment', get_string('spectestassessment', 'assignsubmission_codehandin_submission'), null, $this->get_file_options());
//        //$mform->addElement('filepicker', 'taskoutput_filemanager', get_string('spectestassessment', 'assignsubmission_codehandin_submission'), null,  $this->get_file_options());
//        $mform->addHelpButton('assignsubmission_codehandin_submission_spectestassessment', 'spectestassessment', 'assignsubmission_codehandin_submission');
//        //$mform->addRule('assignsubmission_codehandin_submission_spectestassessment', get_string('required'), 'required', null, 'client');
//        $mform->disabledIf('assignsubmission_codehandin_submission_spectestassessment', 'assignsubmission_codehandin_submission_enabled', 'notchecked');
//        $mform->disabledIf('assignsubmission_codehandin_submission_spectestassessment', 'assignsubmission_codehandin_submission_spectestonly', 'notchecked');
    }

    /**
     * Save the settings for codehandin plugin (if setting does not exists creates it)
     * it is assumed that only cps to be updated or inserted are still on the form
     * @param stdClass $data
     * @todo save specfiles here
     * @return bool
     */
    public function save_settings(stdClass $data) {

        $codehandin = new stdClass();
        $codehandin->id = (int) $this->assignment->get_instance()->id;
        $codehandin->proglangid = (int) $data->assignsubmission_codehandin_submission_proglang;
        $codehandin->mustattemptcompile = isset($data->assignsubmission_codehandin_submission_mustattemptcompile) ? 1 : 0;
        $codehandin->spectestonly = isset($data->assignsubmission_codehandin_submission_spectestonly) ? 1 : 0;
        //$codehandin->funcpercent = (int) $data->funcpercent;
        // should save files here
//        if (isset($data->assignsubmission_codehandin_submission_spectestonly)) {
//            $codehandin->draftspectestid = $data->assignsubmission_codehandin_submission_spectest;
//            $codehandin->draftspectestassessmentid = $data->assignsubmission_codehandin_submission_spectestassessment;
//        }
        $data->assignfeedback_codehandin_enabled = 1;
        local_codehandin_webservice::insert_codehandin($codehandin);

        return true;
    }

//    /**
//     * Add elements to submission form
//     * 
//     * @param mixed $submission stdClass|null
//     * @param MoodleQuickForm $mform
//     * @param stdClass $data
//     * @return bool
//     */
//    public function get_form_elements($submission, MoodleQuickForm $mform, stdClass $data) {
//        // no elements are added to the submission form ... all is handled by the submission file plugin
//        return true;
//    }

    /**
     * Count the number of files
     * again no files are handled by this plugin 
     * 
     * @param int $submissionid
     * @param string $area
     * @return int
     */
    private function count_files($submissionid, $area) {
        $fs = get_file_storage();
        $files = $fs->get_area_files($this->assignment->get_context()->id, 'assignsubmission_codehandin_submission', $area, $submissionid, 'id', false);
        return count($files);
    }

    /**
     * 
     * @global type $USER
     * @global type $DB
     * @param type $submissionid
     * @return type
     */
    public function get_grade_info($submissionid = false) {
        global $USER, $DB;
        if (grade_info != null) {
            return grade_info;
        }
        if ($submissionid == false) {
            if ($this->assignment->get_instance()->teamsubmission) {
                $submission = $this->assignment->get_group_submission($USER->id, 0, false);
            } else {
                $submission = $this->assignment->get_user_submission($USER->id, false);
            }
            $submissionid = $submission->id;
        }
        
        $grade_info = $DB->get_record('codehandin_submission', "id = $submissionid");
        return $grade_info;
    }

    /**
     * Save the files and trigger plagiarism plugin, if enabled,
     * to scan the uploaded files via events trigger
     * @todo add autograde here (must be called post submssion plugin!)?
     * @param stdClass $submission
     * @param stdClass $data
     * @return bool
     */
    public function save(stdClass $submission, stdClass $data) {

        return true;
    }

//    /**
//     * Check if the submission plugin has all the required data to allow the work
//     * to be submitted for grading
//     * @todo if run post file_submssion check contents is ok or is a zip file
//     * @param stdClass $submission the assign_submission record being submitted.
//     * @return bool|string 'true' if OK to proceed with submission, otherwise a
//     *                        a message to display to the user
//     */
//    public function precheck_submission($submission) {
//        return true;
//    }
//    /**
//     * Carry out any extra processing required when the work is submitted for grading
//     * @param stdClass $submission the assign_submission record being submitted.
//     * @return void
//     */
//    public function submit_for_grading($submission) {
//        
//    }
//    /**
//     * Produce a list of files suitable for export that represent this feedback or submission
//     * (again uses the assignsubmission_file plugin)
//     * @param stdClass $submission The submission
//     * @param stdClass $user The user record - unused
//     * @return array - return an array of files indexed by filename
//     */
//    public function get_files(stdClass $submission, stdClass $user) {
//        $fs = get_file_storage();
//        $files = $fs->get_area_files($this->assignment->get_context()->id, 'assignsubmission_codehandin_submission', $area, $submissionid, 'id', false);
//        return count($files);
//        return array();
//    }

    /**
     * Get file areas returns a list of areas this plugin stores files
     * @return array - An array of fileareas (keys) and descriptions (values)
     */
    public function get_file_areas() {
        return local_codehandinws_webservice::getFileAreas();
    }

    /**
     * Display the details for a particular submission
     * 
     * @param stdClass $submission
     * @param bool $showviewlink Set this to true if the list of files is long
     * @return string
     */
    public function view_summary(stdClass $submission, & $showviewlink) {
        global $DB;
        return $DB->get_record('codehandin_submission', array('submissionid' => $submission->id));
    }

    /**
     * No full submission view - the summary contains the list of files and that is the whole submission
     * (again uses the assignsubmission_file plugin)
     * 
     * @param stdClass $submission
     * @return string
     */
    public function view(stdClass $submission) {
        global $DB;
        return $DB->get_record('codehandin_submission', array('submissionid' => $submission));
    }

    /**
     * Return true if this plugin can upgrade an old Moodle 2.2 assignment of this type
     * and version.
     * NOT required for the first version
     * 
     * @param string $type
     * @param int $version
     * @return bool True if upgrade is possible
     */
    public function can_upgrade($type, $version) {
        return false;
    }

    /**
     * Upgrade the settings from the old assignment
     * to the new plugin based one
     * NOT required for the first version
     * 
     * @param context $oldcontext - the old assignment context
     * @param stdClass $oldassignment - the old assignment data record
     * @param string $log record log events here
     * @return bool Was it a success? (false will trigger rollback)
     */
    public function upgrade_settings(context $oldcontext, stdClass $oldassignment, & $log) {
        return false;
    }

    /**
     * Upgrade the submission from the old assignment to the new one
     * NOT required for the first version
     * 
     * @param context $oldcontext The context of the old assignment
     * @param stdClass $oldassignment The data record for the old oldassignment
     * @param stdClass $oldsubmission The data record for the old submission
     * @param stdClass $submission The data record for the new submission
     * @param string $log Record upgrade messages in the log
     * @return bool true or false - false will trigger a rollback
     */
    public function upgrade(context $oldcontext, stdClass $oldassignment, stdClass $oldsubmission, stdClass $submission, & $log) {
        return false;
    }

    /**
     * Copy the student's submission from a previous submission. Used when a student opts to base their resubmission
     * on the last submission.
     * @todo this may be implemented at a later time as copy settings 
     * 
     * @param stdClass $sourcesubmission
     * @param stdClass $destsubmission
     */
    public function copy_submission(stdClass $sourcesubmission, stdClass $destsubmission) {
        return false;
    }

    /**
     * Formatting for log info
     * (again uses the assignsubmission_file plugin)
     * 
     * @param stdClass $submission The submission
     * @return string
     */
    public function format_for_log(stdClass $submission) {
        return "";
    }

    /**
     * The assignment has been deleted - cleanup
     * @todo call functions from ws locallib
     * @return bool
     */
    public function delete_instance() {
        return true;
    }

    /**
     * File format options
     *
     * @return array
     */
    private function get_file_options() {
        global $COURSE;
        //$course = $this->assignment->get_course();
        $fileoptions = array(
            'subdirs' => 100,
            'maxbytes' => $COURSE->maxbytes * 20,
            'maxfiles' => 3,
            'accepted_types' => '*',
            'return_types' => FILE_INTERNAL);
        return $fileoptions;
    }

}
