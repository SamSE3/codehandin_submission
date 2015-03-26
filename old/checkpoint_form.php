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
 * This file contains the forms to create and edit an instance of this module
 *
 * @package   assignsubmission_codehandin
 * @copyright 2014 Samuel Deane
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die('Direct access to this script is forbidden.');

require_once("$CFG->libdir/formslib.php");
require_once("$CFG->dirroot/mod/assign/submission/codehandin/locallib.php");
require_once("$CFG->dirroot/mod/assign/locallib.php"); // assignment class

/**
 * Assignment settings form.
 *
 * @package   assignsubmission_codehandin
* @copyright 2014 Samuel Deane
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class checkpoint_form extends moodleform {

    
    private $assignment;
    function __construct(assign $assignment) {
        $this->assignment=$assignment;
    }
    
    /**
     * Called to define this moodle form
     *
     * @return void
     */
    public function definition() {
        global $CFG, $DB, $PAGE;
        
        
        $mform = $this->_form;

        //$mform->addElement('header', 'general', get_string('general', 'form'));
//checkpoint name
        $mform->addElement('text', 'cpname', get_string('cpname', 'assignsubmission_codehandin'), array('size' => '64'));
        //$mform->addHelpButton('cpname', 'cpname', 'assignsubmission_codehandin');
        if (!empty($CFG->formatstringstriptags)) {
            $mform->setType('cpname', PARAM_TEXT);
        } else {
            $mform->setType('cpname', PARAM_CLEANHTML);
        }
        $mform->addRule('cpname', null, 'required', null, 'client');
        $mform->addRule('cpname', get_string('maximumchars', '', 255), 'maxlength', 255, 'client');
        $mform->disabledIf('cpname', 'assignsubmission_codehandin_enabled', 'notchecked');

        //checkpoint ordering value or quasi checkpoint id ... may change to hidden later and handle by dynamic order
        $mform->addElement('text', 'cpordering', get_string('cpordering', 'assignsubmission_codehandin'));
        $mform->addHelpButton('cpordering', 'cpordering', 'assignsubmission_codehandin');
        $mform->disabledIf('cpordering', 'enabled', 'notchecked');
        $mform->setType('cpordering', PARAM_INT);
        $mform->setDefault('cpordering', 1);
        $mform->addRule('cpordering', null, 'required', null, 'client');
        $mform->disabledIf('cpordering', 'assignsubmission_codehandin_enabled', 'notchecked');        
        
        //checkpoint description
        $label = get_string('cpdescription', 'assignsubmission_codehandin');
        $mform->addElement('editor', 'cpdescription', $label, array('rows' => 10), array('maxfiles' => EDITOR_UNLIMITED_FILES,
            'noclean' => true, 'context' => $this->assignment->get_context(), 'subdirs' => true));                  
        //$mform->addHelpButton('cpdescription', 'cpruntimeargs', 'assignsubmission_codehandin');
        $mform->setType('cpdescription', PARAM_RAW); // no XSS prevention here, users must be trusted
        $mform->addRule('cpdescription', get_string('required'), 'required', null, 'client');
        $mform->disabledIf('cpdescription', 'assignsubmission_codehandin_enabled', 'notchecked');
        
        //checkpoint run time args
        $mform->addElement('text', 'cpruntimeargs', get_string('cpruntimeargs', 'assignsubmission_codehandin'), array('size' => '64'));
        $mform->addHelpButton('cpruntimeargs', 'cpruntimeargs', 'assignsubmission_codehandin');
        $mform->disabledIf('cpruntimeargs', 'enabled', 'notchecked');
        $mform->setType('cpruntimeargs', PARAM_TEXT); // text only do not want to display anything
        $mform->disabledIf('cpruntimeargs', 'assignsubmission_codehandin_enabled', 'notchecked');
        
        // add a task
        $mform->addElement('text', 'taskname', get_string('taskname', 'assignsubmission_codehandin'), array('size' => '64'));
        //$mform->addHelpButton('taskname', 'taskname', 'assignsubmission_codehandin');
        $mform->disabledIf('taskname', 'assignsubmission_codehandin_enabled', 'notchecked');
        $mform->setType('taskname', PARAM_TEXT);
        
        $mform->addElement('text', 'taskordering', get_string('taskordering', 'assignsubmission_codehandin'));
        $mform->addHelpButton('taskordering', 'taskordering', 'assignsubmission_codehandin');
        $mform->disabledIf('taskordering', 'assignsubmission_codehandin_enabled', 'notchecked');
        $mform->setType('taskordering', PARAM_INT);
        $mform->setDefault('taskordering', 1);        

        $mform->addElement('textarea', 'taskdescription', get_string('taskdecription', 'assignsubmission_codehandin'));
        //$mform->addHelpButton('taskdescription', 'taskdescription', 'assignsubmission_codehandin');
        $mform->disabledIf('taskdescription', 'assignsubmission_codehandin_enabled', 'notchecked');
        $mform->setType('taskdescription', PARAM_TEXT);

        $mform->addElement('text', 'taskruntimeargs', get_string('taskruntimeargs', 'assignsubmission_codehandin'), array('size' => '64'));
        $mform->addHelpButton('taskruntimeargs', 'taskruntimeargs', 'assignsubmission_codehandin');
        $mform->disabledIf('taskruntimeargs', 'assignsubmission_codehandin_enabled', 'notchecked');
        $mform->setType('taskruntimeargs', PARAM_TEXT);

        $mform->addElement('text', 'taskretval', get_string('taskretval', 'assignsubmission_codehandin'));
        $mform->addHelpButton('taskretval', 'taskretval', 'assignsubmission_codehandin');
        $mform->disabledIf('taskretval', 'assignsubmission_codehandin_enabled', 'notchecked');
        $mform->setType('taskretval', PARAM_INT);        
        
        $maxbytes = $CFG->maxbytes;
        $mform->addElement('filepicker', 'taskinput', get_string('taskinput', 'assignsubmission_codehandin'), null, array('subdirs' => 0, 'maxbytes' => $maxbytes, 'maxfiles' => 1, 'accepted_types' => '*'));
        $mform->addHelpButton('taskinput', 'taskinput', 'assignsubmission_codehandin');
        $mform->addRule('cpdescription', get_string('required'), 'required', null, 'client');
        $mform->disabledIf('taskinput', 'assignsubmission_codehandin_enabled', 'notchecked');        
        
        $mform->addElement('filepicker', 'taskoutput', get_string('taskoutput', 'assignsubmission_codehandin'), null, array('subdirs' => 0, 'maxbytes' => $maxbytes, 'maxfiles' => 1, 'accepted_types' => '*'));
        $mform->addHelpButton('taskoutput', 'taskoutput', 'assignsubmission_codehandin');
        $mform->addRule('taskoutput', get_string('required'), 'required', null, 'client');
        $mform->disabledIf('taskoutput', 'assignsubmission_codehandin_enabled', 'notchecked');
        
        $mform->addElement('filepicker', 'taskstderr', get_string('taskstderr', 'assignsubmission_codehandin'), null, array('subdirs' => 0, 'maxbytes' => $maxbytes, 'maxfiles' => 1, 'accepted_types' => '*'));
        $mform->addHelpButton('taskstderr', 'taskstderr', 'assignsubmission_codehandin');
        $mform->addRule('taskstderr', get_string('required'), 'required', null, 'client');
        $mform->disabledIf('taskstderr', 'assignsubmission_codehandin_enabled', 'notchecked');        
        
    }

    /**
     * Perform minimal validation on the settings form
     * @param array $data
     * @param array $files
     */
    public function validation($data, $files) {
        //$errors = parent::validation($data, $files);

//        if ($data['allowsubmissionsfromdate'] && $data['duedate']) {
//            if ($data['allowsubmissionsfromdate'] > $data['duedate']) {
//                $errors['duedate'] = get_string('duedatevalidation', 'assignsubmission_codehandin');
//            }
//        }
//        if ($data['duedate'] && $data['cutoffdate']) {
//            if ($data['duedate'] > $data['cutoffdate']) {
//                $errors['cutoffdate'] = get_string('cutoffdatevalidation', 'assignsubmission_codehandin');
//            }
//        }
//        if ($data['allowsubmissionsfromdate'] && $data['cutoffdate']) {
//            if ($data['allowsubmissionsfromdate'] > $data['cutoffdate']) {
//                $errors['cutoffdate'] = get_string('cutoffdatefromdatevalidation', 'assignsubmission_codehandin');
//            }
//        }

        return array();
    }

    /**
     * Any data processing needed before the form is displayed
     * (needed to set up draft areas for editor and filemanager elements)
     * @param array $defaultvalues
     */
    public function data_preprocessing(&$defaultvalues) {
        global $DB;

//        $ctx = null;
//        if ($this->current && $this->current->coursemodule) {
//            $cm = get_coursemodule_from_instance('assignsubmission_codehandin', $this->current->id, 0, false, MUST_EXIST);
//            $ctx = context_module::instance($cm->id);
//        }
//        $assignment = new assign($ctx, null, null);
//        if ($this->current && $this->current->course) {
//            if (!$ctx) {
//                $ctx = context_course::instance($this->current->course);
//            }
//            $course = $DB->get_record('course', array('id'=>$this->current->course), '*', MUST_EXIST);
//            $assignment->set_course($course);
//        }
//        $assignment->plugin_data_preprocessing($defaultvalues);
        return array();
    }

    /**
     * Add any custom completion rules to the form.
     *
     * @return array Contains the names of the added form elements
     */
    public function add_completion_rules() {
//        $mform =& $this->_form;
//        $mform->addElement('checkbox', 'completionsubmit', '', get_string('completionsubmit', 'assignsubmission_codehandin'));
//        return array('completionsubmit');
        return array();
    }

//    /**
//     * Determines if completion is enabled for this module.
//     *
//     * @param array $data
//     * @return bool
//     */
//    public function completion_rule_enabled($data) {
//        return !empty($data['completionsubmit']);
//    }

}
