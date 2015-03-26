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
 * Internal library of functions for module codehandin
 *
 * All the codehandin specific functions, needed to implement the module
 * logic, should go here. Never include this file from your lib.php!
 *
 * @package    assignsubmission_codehandin
 * @copyright  2014 Jonathan Mackenzie & Samuel Deane
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
defined('MOODLE_INTERNAL') || die();

// File areas for file submission assignment.
define('ASSIGNSUBMISSION_CODEHANDIN_MAXFILES', 1);
define('ASSIGNSUBMISSION_CODEHANDIN_MAXSUMMARYFILES', 1);
define('ASSIGNSUBMISSION_CODEHANDIN_INPUTFILES_START', 0);
define('ASSIGNSUBMISSION_CODEHANDIN_OUTPUTFILES_START', 300000000);
define('ASSIGNSUBMISSION_CODEHANDIN_OUTPUTERRFILES_START', 600000000);
define('ASSIGNSUBMISSION_CODEHANDIN_IDINCRAMENT', 1000000);
define('ASSIGNSUBMISSION_CODEHANDIN_FILEAREA', 'codehandin_files');

/**
 * Does something really useful with the passed things
 *
 * @param array $things
 * @return object
 */
//function codehandin_do_something_useful(array $things) {
//    return new stdClass();
//}

class assign_submission_codehandin extends assign_submission_plugin {

    private $mydata;
    private $thismform;

    /**
     * Get the name of the codehandin plugin
     * @return string
     */
    public function get_name() {
        return get_string('pluginname', 'assignsubmission_codehandin');
    }

    /**
     * Get the codehandin submission information from the database
     *
     * @param int $submissionid
     * @return mixed
     */
    private function get_codehandin_submission($assignmentid, $userid) {
        global $DB;
        return $DB->get_record('codehandin_submission', array('assignmentid' => $assignmentid, 'userid' => $userid));
    }

    /**
     * Get the codehandin submission information from the database
     *
     * @param int $submissionid
     * @return mixed a codehandin object containing its checkpoints and their tasks
     */
    private function get_codehandin($assignmentid) {
        global $DB;
        $codehandin = $DB->get_record('codehandin', array('id' => $assignmentid));
        $codehandin->checkpoints = $DB->get_record('codehandin_checkpoint', array('assignmentid' => $assignmentid));
        foreach ($codehandin->checkpoints as $cp) {
            $codehandin->checkpoints->tests = $DB->get_record('codehandin_test', array('checkpointid' => $cp->id));
        }
        return $codehandin;
    }

    /**
     * File format options
     *
     * @return array
     */
    private function get_file_options() {
        global $CFG;
        $fileoptions = array('subdirs' => 0,
            'maxbytes' => $CFG->maxbytes,
            'maxfiles' => 1,
            'accepted_types' => '*',
            'return_types' => FILE_INTERNAL);
        return $fileoptions;
    }

    /**
     * Set up the draft file areas before displaying the settings form
     * @param array $default_values the values to be passed in to the form
     */
    public function data_preprocessing(&$default_values) {
        global $DB;
        $context = $this->assignment->get_context();
        $course = $this->assignment->get_course();

        $drafttestinputid = file_get_submitted_draft_itemid('assignsubmission_codehandin_testinput');
        $drafttestoutputid = file_get_submitted_draft_itemid('assignsubmission_codehandin_testoutput');
        $draftteststderr = file_get_submitted_draft_itemid('assignsubmission_codehandin_teststderr');

        if ($context) {
            // existing submission

            $assignmentid = $this->assignment->get_instance()->id;

            file_prepare_draft_area($drafttestinputid, $context->id, 'assignsubmission_codehandin', ASSIGNSUBMISSION_CODEHANDIN_FILEAREA, (ASSIGNSUBMISSION_CODEHANDIN_INPUTFILES_START + $assignmentid), array(
                'subdirs' => 0,
                'maxbytes' => $course->maxbytes,
                'maxfiles' => 1,
                'accepted_types' => '*'
            ));
            file_prepare_draft_area($drafttestoutputid, $context->id, 'assignsubmission_codehandin', ASSIGNSUBMISSION_CODEHANDIN_FILEAREA, (ASSIGNSUBMISSION_CODEHANDIN_OUTPUTFILES_START + $assignmentid), array(
                'subdirs' => 0,
                'maxbytes' => $course->maxbytes,
                'maxfiles' => 1
            ));
            file_prepare_draft_area($draftteststderr, $context->id, 'assignsubmission_codehandin', ASSIGNSUBMISSION_CODEHANDIN_FILEAREA, (ASSIGNSUBMISSION_CODEHANDIN_OUTPUTERRFILES_START + $assignmentid), array(
                'subdirs' => 0,
                'maxbytes' => $course->maxbytes,
                'maxfiles' => 1
            ));


            //$assignment = $DB->get_records('codehandin', array('id' => $assignmentid));
            $checkpoints = $DB->get_records('codehandin_checkpoint', array('assignmentid' => $assignmentid), $sort = 'ordering ASC');

            echo '<pre>';
            print_r($checkpoints);
            echo '</pre>';

//            echo '<pre>';
//            echo $assignmentid;
//            echo '</pre>';
//
//            echo '<pre>';
//            print_r($checkpoints);
//            echo '</pre>';
//            $assignment = (array) $assignment;
//            //$assignment->checkpoints = $checkpoints;
//            $assignment['checkpoints'] = $checkpoints;
//            $assignment = (object) $assignment;

            foreach ($checkpoints as $cp) {

                $tests = $DB->get_records('codehandin_test', array('checkpointid' => $cp->id));
//                $cp = (array) $cp;
//                $cp['tests'] = $tests;
//                $cp = (object) $cp;
//                
//                echo '<pre>';
//                print_r($tests);
//                echo '</pre>';
            }

//            echo '<pre>';
//            print_r($checkpoints);
//            echo '</pre>';
//            echo '<pre>';
//            print_r($cpids);
//            echo '</pre>';
//            echo '<pre>';
//            print_r($tests);
//            echo '</pre>';
            // assignment is now fancy packed



            foreach ($checkpoints as $cp) {
                $default_values['assignsubmission_codehandin_cpname'] = $cp->name;
                $default_values['assignsubmission_codehandin_cpdescription'] = $cp->description;
                $default_values['assignsubmission_codehandin_cpruntimeargs'] = $cp->runtimeargs;
                $default_values['assignsubmission_codehandin_cpordering'] = $cp->ordering;
                foreach ($tests as $test) {
                    $default_values['assignsubmission_codehandin_testassessment'] = $test->assessment;
                    $default_values['assignsubmission_codehandin_testdescription'] = $test->description;
                    $default_values['assignsubmission_codehandin_testretval'] = $test->retval;
                }
            }
        } else {
            file_prepare_draft_area($drafttestinputid, null, 'assignsubmission_codehandin', ASSIGNSUBMISSION_CODEHANDIN_FILEAREA, 0, array(
                'subdirs' => 0,
                'maxbytes' => $course->maxbytes,
                'maxfiles' => 1,
                'accepted_types' => '*'
            ));
        }

        // $DB->update_record($table, $dataobject, $bulk=false)

        $default_values['assignsubmission_codehandin_testinput'] = $drafttestinputid;
        $default_values['assignsubmission_codehandin_testoutput'] = $drafttestoutputid;
        $default_values['assignsubmission_codehandin_teststderr'] = $draftteststderr;
    }

    /**
     * Get the default setting for the codehandin plugin
     *
     * @param MoodleQuickForm $mform The form to add elements to
     * @return void
     */
    public function get_settings(MoodleQuickForm $mform) {
        global $DB, $COURSE, $CFG;
        //ref the checkpoint form
        //require_once('checkpoint_form.php');

        $firstset = true;

        //$assignmentid = $this->assignment->get_instance()->id;
        //echo var_dump();
        $defaultproglang = get_config('assignsubmission_codehandin', 'defaultproglang');
        $mustattemptcompile = get_config('assignsubmission_codehandin', 'mustattemptcompile');

        // get default
// set the programming language
        $languages = $DB->get_records_select_menu('codehandin_proglang', null, null, 'id', 'id, name');
        $mform->addElement('select', 'assignsubmission_codehandin_proglang', get_string('proglang', 'assignsubmission_codehandin'), $languages);
        $mform->addHelpButton('assignsubmission_codehandin_proglang', 'assignsubmission_codehandin_proglang', 'assignsubmission_codehandin');
        $mform->setDefault('assignsubmission_codehandin_proglang', $defaultproglang);
        $mform->disabledIf('assignsubmission_codehandin_proglang', 'assignsubmission_codehandin_enabled', 'notchecked');

// select if submissions must have been attemptedly compiled
        $mform->addElement('checkbox', 'assignsubmission_codehandin_mustattemptcompile', get_string('mustattemptcompile', 'assignsubmission_codehandin'));
        $mform->addHelpButton('assignsubmission_codehandin_mustattemptcompile', 'assignsubmission_codehandin_mustattemptcompile', 'assignsubmission_codehandin');
        $mform->setDefault('assignsubmission_codehandin_mustattemptcompile', $mustattemptcompile);
        $mform->disabledIf('assignsubmission_codehandin_mustattemptcompile', 'assignsubmission_codehandin_enabled', 'notchecked');


//        // add checkpoint ... not enabled yet 
//        $edittemplateurl = new moodle_url('/mod/assign/submission/codehandin/addcp.php', array('courseid' => $COURSE->id));
//        $edittemplatelink = html_writer::link($edittemplateurl, get_string('addcp', 'assignsubmission_codehandin'),
//                                              array('target' => '_blank'));
//        $mform->addElement('static', 'assignsubmission_codehandin_addcp', '', $edittemplatelink);
//        // show individual descriptions ... not enabled at the mo
//        $mform->addElement('checkbox', 'showcpdescription', get_string('showcpdescription'));
//        $mform->addHelpButton('showcpdescription', 'showcpdescription');


        $mform->addElement('text', 'assignsubmission_codehandin_cpname', get_string('cpname', 'assignsubmission_codehandin'), array('size' => '64'));
//$mform->addHelpButton('cpname', 'cpname', 'assignsubmission_codehandin');
        if (!empty($CFG->formatstringstriptags)) {
            $mform->setType('assignsubmission_codehandin_cpname', PARAM_TEXT);
        } else {
            $mform->setType('assignsubmission_codehandin_cpname', PARAM_CLEANHTML);
        }
        $mform->addRule('assignsubmission_codehandin_cpname', null, 'required', null, 'client');
        $mform->addRule('assignsubmission_codehandin_cpname', get_string('maximumchars', '', 255), 'maxlength', 255, 'client');
        $mform->disabledIf('assignsubmission_codehandin_cpname', 'assignsubmission_codehandin_enabled', 'notchecked');

//checkpoint ordering value or quasi checkpoint id ... may change to hidden later and handle by dynamic order
        $mform->addElement('text', 'assignsubmission_codehandin_cpordering', get_string('cpordering', 'assignsubmission_codehandin'));
        $mform->addHelpButton('assignsubmission_codehandin_cpordering', 'cpordering', 'assignsubmission_codehandin');
        $mform->setType('assignsubmission_codehandin_cpordering', PARAM_INT);
        $mform->setDefault('assignsubmission_codehandin_cpordering', 1);
        $mform->addRule('assignsubmission_codehandin_cpordering', null, 'required', null, 'client');
        $mform->disabledIf('assignsubmission_codehandin_cpordering', 'assignsubmission_codehandin_enabled', 'notchecked');

        //checkpoint description
        $label = get_string('cpdescription', 'assignsubmission_codehandin');
        $mform->addElement('textarea', 'assignsubmission_codehandin_cpdescription', get_string('cpdecription', 'assignsubmission_codehandin'));
        ;
        //$mform->addHelpButton('cpdescription', 'cpdescription', 'assignsubmission_codehandin');
        $mform->setType('assignsubmission_codehandin_cpdescription', PARAM_RAW); // no XSS prevention here, users must be trusted
        $mform->addRule('assignsubmission_codehandin_cpdescription', get_string('required'), 'required', null, 'client');
        $mform->disabledIf('assignsubmission_codehandin_cpdescription', 'assignsubmission_codehandin_enabled', 'notchecked');

        //checkpoint run time args
        $mform->addElement('text', 'assignsubmission_codehandin_cpruntimeargs', get_string('cpruntimeargs', 'assignsubmission_codehandin'), array('size' => '64'));
        $mform->addHelpButton('assignsubmission_codehandin_cpruntimeargs', 'assignsubmission_codehandin_cpruntimeargs', 'assignsubmission_codehandin');
        $mform->setType('assignsubmission_codehandin_cpruntimeargs', PARAM_TEXT); // text only do not want to display anything
        $mform->disabledIf('assignsubmission_codehandin_cpruntimeargs', 'assignsubmission_codehandin_enabled', 'notchecked');

        // add a task

        $mform->addElement('selectyesno', 'assignsubmission_codehandin_testassessment', get_string('testassessment', 'assignsubmission_codehandin'));
        $mform->addHelpButton('assignsubmission_codehandin_testassessment', 'assignsubmission_codehandin_testassessment', 'assignsubmission_codehandin');
        $mform->setDefault('assignsubmission_codehandin_testassessment', 1);
        $mform->disabledIf('assignsubmission_codehandin_testassessment', 'assignsubmission_codehandin_enabled', 'notchecked');

        $mform->addElement('textarea', 'assignsubmission_codehandin_testdescription', get_string('taskdecription', 'assignsubmission_codehandin'));
        //$mform->addHelpButton('taskdescription', 'taskdescription', 'assignsubmission_codehandin');        
        $mform->setType('assignsubmission_codehandin_testdescription', PARAM_TEXT);
        $mform->disabledIf('assignsubmission_codehandin_testdescription', 'assignsubmission_codehandin_enabled', 'notchecked');

//        $mform->addElement('text', 'taskruntimeargs', get_string('taskruntimeargs', 'assignsubmission_codehandin'), array('size' => '64'));
//        $mform->addHelpButton('taskruntimeargs', 'taskruntimeargs', 'assignsubmission_codehandin');
//        $mform->setType('taskruntimeargs', PARAM_TEXT);
//        $mform->disabledIf('taskruntimeargs', 'assignsubmission_codehandin_enabled', 'notchecked');

        $mform->addElement('text', 'assignsubmission_codehandin_testretval', get_string('testretval', 'assignsubmission_codehandin'));
        $mform->addHelpButton('assignsubmission_codehandin_testretval', 'testretval', 'assignsubmission_codehandin');
        $mform->setType('assignsubmission_codehandin_testretval', PARAM_INT);
        $mform->disabledIf('assignsubmission_codehandin_testretval', 'assignsubmission_codehandin_enabled', 'notchecked');

        //prepre some file managers ... piggy back on the mform rather than data
        //$fileoptions = $this->get_file_options();
        //echo var_dump();
        //echo "bbbbbbbsfsds ".$assignmentid = $this->assignment->get_instance()->id;    
        //echo "bdbff ".var_dump($this);
        // returns mform + an extra field
        //$mydata = new stdClass();

        $maxbytes = $COURSE->maxbytes;
        $mform->addElement('filemanager', 'assignsubmission_codehandin_testinput', get_string('testinput', 'assignsubmission_codehandin'), null, array('subdirs' => 0, 'maxbytes' => $maxbytes,
            'maxfiles' => 1, 'accepted_types' => '*'));
        //$mform->addElement('filepicker', 'taskinput_filemanager', get_string('taskinput', 'assignsubmission_codehandin'), null, array('subdirs' => 0, 'maxbytes' => $maxbytes, 'maxfiles' => 1, 'accepted_types' => '*'));
        $mform->addHelpButton('assignsubmission_codehandin_testinput', 'assignsubmission_codehandin_testinput', 'assignsubmission_codehandin');
        $mform->addRule('assignsubmission_codehandin_testinput', get_string('required'), 'required', null, 'client');
        $mform->disabledIf('assignsubmission_codehandin_testinput', 'assignsubmission_codehandin_enabled', 'notchecked');

        $mform->addElement('filemanager', 'assignsubmission_codehandin_testoutput', get_string('testoutput', 'assignsubmission_codehandin'), null, array('subdirs' => 0, 'maxbytes' => $maxbytes, 'maxfiles' => 1, 'accepted_types' => '*'));
        //$mform->addElement('filepicker', 'taskoutput_filemanager', get_string('taskoutput', 'assignsubmission_codehandin'), null, array('subdirs' => 0, 'maxbytes' => $maxbytes, 'maxfiles' => 1, 'accepted_types' => '*'));
        $mform->addHelpButton('assignsubmission_codehandin_testoutput', 'assignsubmission_codehandin_testoutput', 'assignsubmission_codehandin');
        $mform->addRule('assignsubmission_codehandin_testoutput', get_string('required'), 'required', null, 'client');
        $mform->disabledIf('assignsubmission_codehandin_testoutput', 'assignsubmission_codehandin_enabled', 'notchecked');

        $mform->addElement('filemanager', 'assignsubmission_codehandin_teststderr', get_string('teststderr', 'assignsubmission_codehandin'), null, array('subdirs' => 0, 'maxbytes' => $maxbytes, 'maxfiles' => 1, 'accepted_types' => '*'));
        //$mform->addElement('filepicker', 'taskstderr_filemanager', get_string('taskstderr', 'assignsubmission_codehandin'), null, array('subdirs' => 0, 'maxbytes' => $maxbytes, 'maxfiles' => 1, 'accepted_types' => '*'));
        $mform->addHelpButton('assignsubmission_codehandin_teststderr', 'assignsubmission_codehandin_teststderr', 'assignsubmission_codehandin');
        $mform->disabledIf('assignsubmission_codehandin_teststderr', 'assignsubmission_codehandin_enabled', 'notchecked');
    }

    /**
     * Save the settings for codehandin plugin (if setting does not exists creates it)
     *
     * @param stdClass $data
     * @return bool
     */
    public function save_settings(stdClass $data) {
        global $USER, $DB;

        //$data = (object) array_merge((array) $data, (array) $mydata);        

        $this->set_config('defaultproglang', $data->assignsubmission_codehandin_proglang);
        $this->set_config('mustattemptcompile', $data->assignsubmission_codehandin_mustattemptcompile);
        $assignmentid = $this->assignment->get_instance()->id; //should exists by now
        //$assignmentid = 0;
        //echo var_dump($data);

        $context = $this->assignment->get_context();
        $course = $this->assignment->get_course();

        file_save_draft_area_files($data->assignsubmission_codehandin_testinput, $context->id, 'assignsubmission_codehandin', ASSIGNSUBMISSION_CODEHANDIN_FILEAREA, (7000000000 + $assignmentid), array(
            'subdirs' => 0,
            'maxbytes' => $course->maxbytes,
            'maxfiles' => 1,
            'accepted_types' => '*'
        ));

        file_save_draft_area_files($data->assignsubmission_codehandin_testoutput, $context->id, 'assignsubmission_codehandin', ASSIGNSUBMISSION_CODEHANDIN_FILEAREA, (8000000000 + $assignmentid), array(
            'subdirs' => 0,
            'maxbytes' => $course->maxbytes,
            'maxfiles' => 1,
            'accepted_types' => '*'
        ));

        file_save_draft_area_files($data->assignsubmission_codehandin_teststderr, $context->id, 'assignsubmission_codehandin', ASSIGNSUBMISSION_CODEHANDIN_FILEAREA, (9000000000 + $assignmentid), array(
            'subdirs' => 0,
            'maxbytes' => $course->maxbytes,
            'maxfiles' => 1,
            'accepted_types' => '*'
        ));

//        $codehandin = new stdClass();
//        $codehandin->id = $assignmentid;
//        $codehandin->mustattemptcompile = $data->assignsubmission_codehandin_mustattemptcompile;
//        $codehandin->proglang = $data->assignsubmission_codehandin_proglang;
        //$DB->insert_record("codehandin", $codehandin);

        $codehandin_cp = new stdClass();
        $codehandin_cp->assignmentid = $assignmentid;
        $codehandin_cp->name = $data->assignsubmission_codehandin_cpname;
        $codehandin_cp->description = $data->assignsubmission_codehandin_cpdescription;
        $codehandin_cp->runtimeargs = $data->assignsubmission_codehandin_cpruntimeargs;
        $codehandin_cp->ordering = $data->assignsubmission_codehandin_cpordering;
        $cpid = $DB->insert_record("codehandin_checkpoint", $codehandin_cp, true, false);

        $this->get_codehandins_assignfiles($assignmentid);
        $this->get_codehandins_assignfiles(0);

        $codehandin_test = new stdClass();
        $codehandin_test->checkpointid = $cpid;
        $codehandin_test->description = $data->assignsubmission_codehandin_testdescription;
        $codehandin_test->assessment = $data->assignsubmission_codehandin_testassessment;
        $codehandin_test->input = 0; //$data->assignsubmission_codehandin_testinput;
        $codehandin_test->output = 0; //$data->assignsubmission_codehandin_testoutput;
        $codehandin_test->stderr = 0; //$data->assignsubmission_codehandin_teststderr;
        $codehandin_test->retval = $data->assignsubmission_codehandin_testretval;
        $DB->insert_record("codehandin_test", $codehandin_test, false, false);
        //$DB->update_record('glossary_entries', $entry);

        return true;
    }

    /**
     * Save the files and trigger plagiarism plugin, if enabled,
     * to scan the uploaded files via events trigger
     *
     * @param stdClass $submission
     * @param stdClass $data
     * @return bool
     */
    public function save(stdClass $submission, stdClass $data) {
//        $fileoptions = array('subdirs' => 0,
//            'maxbytes' => $COURSE->maxbytes,
//            'accepted_types' => '*',
//            'return_types' => FILE_INTERNAL);
//
//// The element name may have been for a different user.
//        foreach ($data as $key => $value) {
//            if (strpos($key, 'files_') === 0 && strpos($key, '_filemanager')) {
//                $elementname = substr($key, 0, strpos($key, '_filemanager'));
//            }
//        }
//
//        $data = file_postupdate_standard_filemanager($data, $elementname, $fileoptions, $this->assignment->get_context(), 'assignsubmission_codehandin', 'submission_codehandin', $submission->id);
//
//        return $this->update_file_count($grade);
    }

    /**
     * Add elements to submission form
     *
     * @param mixed $submission stdClass|null
     * @param MoodleQuickForm $mform
     * @param stdClass $data
     * @return bool
     */
    public function get_form_elements($submission, MoodleQuickForm $mform, stdClass $data) {
        global $DB;

        if (isset($data->codehandinS)) {
            $data->codehandinS = '';
        }


//$fileinfo = array(
//    'contextid' => $context->id, // ID of context
//    'component' => 'mod_mymodule',     // usually = table name
//    'filearea' => 'myarea',     // usually = table name
//    'itemid' => 0,               // usually = ID of row in table
//    'filepath' => '/',           // any path beginning and ending in /
//    'filename' => 'myfile.txt'); // any filename        

        if ($submission) {
            $fs = get_file_storage();
            $contextid = $this->assignment->get_context()->id;
            $files = $fs->get_area_files($contextid, 'assignsubmission_file', ASSIGNSUBMISSION_FILE_FILEAREA, $submission->id, "filepath, filename", false);
//if ($files) {
            $filelist = array();
            $c = 0;
            foreach ($files as $f) {
//if (isset($f->itemid)) {
                $filelist[] = $f->get_filename(); // $f is an instance of stored_file
                $c++;
//}
            }
            if ($c > 0) {
                $mform->addElement('select', 'filelist', get_string('filelist', 'assignsubmission_codehandin'), $filelist);
            }
        } else {
            
        }

//        foreach ($files as $file) {
//            $fieldupdates = array('itemid' => $destsubmission->id);
//            $fs->create_file_from_storedfile($fieldupdates, $file);
//        }        

        $mform->addElement('hidden', 'enabletext', 0);
        $mform->setType('enabletext', PARAM_INT);

// select a checkpoint        
        $assignments = $DB->get_records('codehandin_checkpoint', array('assignmentid' => $this->assignment->get_instance()->id), 'ordering', '*');

        $checkpoints = array();
        for ($index = 1; $index < count($assignments); $index++) { // one for each checkpoint
            $checkpoints[$index] = 'Checkpoint ' . $index;
        }
        $mform->addElement('select', 'checkpoint', get_string('checkpoint', 'assignsubmission_codehandin'), $checkpoints);

// select respective file
//$attributest = ; // //settings for the text fieilds 'size' => '20' (48 default)
        $mform->addElement('text', 'cpcommandargs', get_string('cpcommandargs', 'assignsubmission_codehandin'), array('maxlength' => 255));
        $mform->setType('cpcommandargs', PARAM_TEXT);

// update a description
        $mform->addElement('textarea', 'cpdescription', get_string('cpdescription', 'assignsubmission_codehandin'), array('maxlength' => 255));
        if (isset($$data->checkpoint)) {
            $mform->setDefault('cpcommandargs', $assignments[$data->checkpoint]->runtimeargs);
            $mform->setDefault('cpcommandargs', $assignments[$data->checkpoint]->ordering);
        }
        $mform->disabledIf('cpdesc', 'enabletext');
        $mform->disabledIf('cpcommandargs', 'enabletext');

// click to run a test
        $mform->addElement('submit', 'testcodewargs', get_string('testcodewargs', 'assignsubmission_codehandin'));
        $mform->registerNoSubmitButton('testcodewargs');

// output some test results
        $attributesta = ''; //wrap="virtual" rows="20" cols="50"'
        $mform->addElement('textarea', 'cpinput', get_string('cpinput', 'assignsubmission_codehandin'), $attributesta);
        $groupout = array();
        $groupout[] = &$mform->createElement('textarea', 'cpstdoutput', get_string('cpstdoutput', 'assignsubmission_codehandin'), $attributesta);
        $groupout[] = &$mform->createElement('textarea', 'cpexpectedstdoutput', get_string('cpexpectedstdoutput', 'assignsubmission_codehandin'), $attributesta);
        $mform->addGroup($groupout, 'groupout', get_string('groupout', 'assignsubmission_codehandin'), array(' '), false);
        if (isset($data->expectederroutput)) {
            $groupouterr = array();
            $groupouterr[] = &$mform->createElement('textarea', 'cperroutput', get_string('cperroutput', 'assignsubmission_codehandin'), $attributesta);
            $groupouterr[] = &$mform->createElement('textarea', 'cpexpectederroutput', get_string('cpexpectederroutput', 'assignsubmission_codehandin'), $attributesta);
            $mform->addGroup($groupouterr, 'groupouterr', get_string('groupouterr', 'assignsubmission_codehandin'), array(' '), false);
        }

        return true;
    }

    /**
     * Count the number of user files (uses the 'assignsubmission_file' plugin)
     *
     * @param int $submissionid
     * @param string $area
     * @return int
     */
    private function count_files($submissionid, $area) {
        $fs = get_file_storage();
        $files = $fs->get_area_files($this->assignment->get_context()->id, 'assignsubmission_file', $area, $submissionid, 'id', false);

        return count($files);
    }

    /**
     * Produce a list of files suitable for export that represent this codehandin?
     *
     * @param stdClass $submission The submission
     * @param stdClass $user The user record - unused
     * @return array - return an array of files indexed by filename
     */
    public function get_codehandins_assignfiles($assignmentid) {
        $result = array();
        $fs = get_file_storage();

        $files = $fs->get_area_files(
                $this->assignment->get_context()->id, 'assignsubmission_codehandin', ASSIGNSUBMISSION_CODEHANDIN_FILEAREA, $assignmentid, 'timemodified', false);

        foreach ($files as $file) {
            $result[$file->get_filename()] = $file;
            echo $file->get_filename();
        }
        return $result;
    }

    /**
     * Display the list of files in the submission status table
     * (again uses the assignsubmission_file plugin)
     * 
     * @param stdClass $submission
     * @param bool $showviewlink Set this to true if the list of files is long
     * @return string
     */
    public function view_summary(stdClass $submission, & $showviewlink) {
        $count = $this->count_files($submission->id, ASSIGNSUBMISSION_FILE_FILEAREA);

// Show we show a link to view all files for this plugin?
        $showviewlink = $count > ASSIGNSUBMISSION_FILE_MAXSUMMARYFILES;
        if ($count <= ASSIGNSUBMISSION_FILE_MAXSUMMARYFILES) {
            return $this->assignment->render_area_files('assignsubmission_file', ASSIGNSUBMISSION_FILE_FILEAREA, $submission->id);
        } else {
            return get_string('countfiles', 'assignsubmission_file', $count);
        }
    }

    /**
     * No full submission view - the summary contains the list of files and that is the whole submission
     *
     * @param stdClass $submission
     * @return string
     */
    public function view(stdClass $submission) {
        return $this->assignment->render_area_files('assignsubmission_file', ASSIGNSUBMISSION_FILE_FILEAREA, $submission->id);
    }

    /**
     * Return true if this plugin can upgrade an old Moodle 2.2 assignment of this type
     * and version.
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
     *
     * @param context $oldcontext - the old assignment context
     * @param stdClass $oldassignment - the old assignment data record
     * @param string $log record log events here
     * @return bool Was it a success? (false will trigger rollback)
     */
    public function upgrade_settings(context $oldcontext, stdClass $oldassignment, & $log) {
//        global $DB;
//
//        if ($oldassignment->assignmenttype == 'uploadsingle') {
//            $this->set_config('programminglanguage', 1);
//            $this->set_config('maxsubmissionsizebytes', $oldassignment->maxbytes);
//            return true;
//        } else if ($oldassignment->assignmenttype == 'upload') {
//            $this->set_config('programminglanguage', $oldassignment->var1);
//            $this->set_config('maxsubmissionsizebytes', $oldassignment->maxbytes);
//
//            // Advanced file upload uses a different setting to do the same thing.
//            $DB->set_field('assign', 'submissiondrafts', $oldassignment->var4, array('id' => $this->assignment->get_instance()->id));
//
//            // Convert advanced file upload "hide description before due date" setting.
//            $alwaysshow = 0;
//            if (!$oldassignment->var3) {
//                $alwaysshow = 1;
//            }
//            $DB->set_field('assign', 'alwaysshowdescription', $alwaysshow, array('id' => $this->assignment->get_instance()->id));
//            return true;
//        }
        return false;
    }

    /**
     * Upgrade the submission from the old assignment to the new one
     *
     * @param context $oldcontext The context of the old assignment
     * @param stdClass $oldassignment The data record for the old oldassignment
     * @param stdClass $oldsubmission The data record for the old submission
     * @param stdClass $submission The data record for the new submission
     * @param string $log Record upgrade messages in the log
     * @return bool true or false - false will trigger a rollback
     */
    public function upgrade(context $oldcontext, stdClass $oldassignment, stdClass $oldsubmission, stdClass $submission, & $log) {
//        global $DB;
//
//        $codehandin = new stdClass();
//
//        $codehandin->numfiles = $oldsubmission->numfiles;
//        $codehandin->submission = $submission->id;
//        $codehandin->assignment = $this->assignment->get_instance()->id;
//
//        if (!$DB->insert_record('assignsubmission_codehandin', $codehandin) > 0) {
//            $log .= get_string('couldnotconvertsubmission', 'mod_assign', $submission->userid);
//            return false;
//        }
//
//        // Now copy the area files.
//        $this->assignment->copy_area_codehandins_for_upgrade($oldcontext->id, 'mod_assignment', 'submission', $oldsubmission->id, $this->assignment->get_context()->id, 'assignsubmission_codehandin', ASSIGNSUBMISSION_CODEHANDIN_FILEAREA, $submission->id);
//
//        return true;
        return false;
    }

    /**
     * Return true if there are no submission files
     * @param stdClass $submission
     */
    public function is_empty(stdClass $submission) { //called on save
        return $this->count_codehandins($submission->id, ASSIGNSUBMISSION_CODEHANDIN_FILEAREA) == 0;
    }

    /**
     * Get file areas returns a list of areas this plugin stores files
     * @return array - An array of fileareas (keys) and descriptions (values)
     */
    public function get_codehandin_areas() {// left in case i want to save a error log/output etc
        return array(ASSIGNSUBMISSION_CODEHANDIN_FILEAREA => $this->get_name());
    }

    /**
     * Copy the student's submission from a previous submission. Used when a student opts to base their resubmission
     * on the last submission.
     * this may be implemented at a later time as copy settings 
     * 
     * @param stdClass $sourcesubmission
     * @param stdClass $destsubmission
     */
    public function copy_submission(stdClass $sourcesubmission, stdClass $destsubmission) {

        //prehaps implement as copy assignment
//        global $DB;
// Copy the files across. ... there are no files stored ... handled by assignsubmission_file
//        $contextid = $this->assignment->get_context()->id;        
//        $fs = get_file_storage();
//        $files = $fs->get_area_files($contextid, 'assignsubmission_file', ASSIGNSUBMISSION_FILE_FILEAREA, $sourcesubmission->id, 'id', false);
//        foreach ($files as $file) {
//            $fieldupdates = array('itemid' => $destsubmission->id);
//            $fs->create_file_from_storedfile($fieldupdates, $file);
//        }
// Copy the assignsubmission_file record.                                                                                   
//        if ($filesubmission = $this->get_file_submission($sourcesubmission->id)) {
//            unset($filesubmission->id);
//            $filesubmission->submission = $destsubmission->id;
//            $DB->insert_record('assignsubmission_file', $filesubmission);
//        }
//        return true;
        return false; //
    }

    /**
     * Formatting for log info
     * again uses the assignsubmission plugin
     * 
     * @param stdClass $submission The submission
     * @return string
     */
    public function format_for_log(stdClass $submission) {
// Format the info for each submission plugin (will be added to log).
        $filecount = $this->count_files($submission->id, ASSIGNSUBMISSION_FILE_FILEAREA);

        return get_string('numfilesforlog', 'assignsubmission_codehandin', $filecount);
    }

    /**
     * The assignment has been deleted - cleanup
     *
     * @return bool
     */
    public function delete_instance() {
        global $DB;
// Will throw exception on failure.       
        if (!$assignmentid = $this->assignment->get_instance()->id) {
            return false;
        }
        // moodle does not allow cascading of db deletes os have to delete in parts
        $DB->delete_records('codehandin_submission', array('assignmentid' => $assignmentid)); // delete the submission

        $checkpoint_select = "SELECT name FROM {codehandin_checkpoint} WHERE assignmentid = ?";
        $DB->delete_records_select('codehandin_test', "id IN ($checkpoint_select)", array($assignmentid));
        //$DB->delete_records('codehandin_test', array('checkpointid' => $this->assignment->get_instance()->id));
        $DB->delete_records('codehandin_checkpoint', array('assignmentid' => $assignmentid)); // delete the checkpoints
        $DB->delete_records('codehandin', array('id' => $assignmentid)); // delete the codehandin
        // $DB->delete_records('assignsubmission_file', array('assignment' => $assignmentid));
        return true;
    }

    /**
     * Return a description of external params suitable for uploading a codehandin from a webservice.
     *
     * @return external_description|null
     */
    public function get_external_parameters() {
        return array(
            'files_filemanager' => new external_value(
                    PARAM_INT, 'The id of a draft area containing files for this submission.'
            )
        );
    }

}
