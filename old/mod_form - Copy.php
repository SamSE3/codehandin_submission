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
 * The main codehandin configuration form
 *
 * It uses the standard core Moodle formslib. For more info about them, please
 * visit: http://docs.moodle.org/en/Development:lib/formslib.php
 *
 * @package    assignsubmission_codehandin
 * @copyright  2014 Jonathan Mackenzie & Samuel Deane
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot . '/course/moodleform_mod.php');
require_once($CFG->dirroot . '/mod/codehandin/locallib.php');

/**
 * Module instance settings form
 */
class assignsubmission_codehandin_mod_form extends moodleform_mod {

    public static $datefieldoptions = array('optional' => true, 'step' => 1);

    /**
     * Defines forms elements
     */
    public function definition() {
        global $COURSE, $CFG, $DB, $PAGE;
        $mform = $this->_form;

        //-------------------------------------------------------------------------------
        // Adding the "general" fieldset, where all the common settings are showed
        $mform->addElement('header', 'general', get_string('general', 'form'));

        // Adding the standard "name" field
        $mform->addElement('text', 'name', get_string('name'), array('size' => '64'));
        if (!empty($CFG->formatstringstriptags)) {
            $mform->setType('name', PARAM_TEXT);
        } else {
            $mform->setType('name', PARAM_CLEANHTML);
        }
        $mform->addRule('name', null, 'required', null, 'client');
        $mform->addRule('name', get_string('maximumchars', '', 255), 'maxlength', 255, 'client');

        // Adding the standard "intro" and "introformat" fields
        $this->add_intro_editor(false, get_string('introduction', 'codehandin'));

        // -------------------------------------------------------------------------------
        $mform->addElement('header', 'timing', get_string('timing', 'quiz'));

        // Open and close dates.
        $mform->addElement('date_time_selector', 'timeopen', get_string('quizopen', 'quiz'), self::$datefieldoptions);
        $mform->addHelpButton('timeopen', 'quizopenclose', 'quiz');

        $mform->addElement('date_time_selector', 'timeclose', get_string('quizclose', 'quiz'), self::$datefieldoptions);

        // What to do with overdue attempts.
        $mform->addElement('select', 'overduehandling', get_string('overduehandling', 'quiz'), array(
            'autosubmit' => get_string('overduehandlingautosubmit', 'quiz'),
            'graceperiod' => get_string('overduehandlinggraceperiod', 'quiz'),
            'autoabandon' => get_string('overduehandlingautoabandon', 'quiz'),
        ));
        $mform->addHelpButton('overduehandling', 'overduehandling', 'quiz');
//        $mform->setAdvanced('overduehandling', $quizconfig->overduehandling_adv);
//        $mform->setDefault('overduehandling', $quizconfig->overduehandling);        
        // Grace period time.
        $mform->addElement('duration', 'graceperiod', get_string('graceperiod', 'quiz'), array('optional' => true));
        $mform->addHelpButton('graceperiod', 'graceperiod', 'quiz');
//        $mform->setAdvanced('graceperiod', $quizconfig->graceperiod_adv);
//        $mform->setDefault('graceperiod', $quizconfig->graceperiod);
//        $mform->disabledIf('graceperiod', 'overduehandling', 'neq', 'graceperiod');        
        // -------------------------------------------------------------------------------
        // Grade settings.
        $this->standard_grading_coursemodule_elements();

        //own grading scale?
        //$mform->removeElement('grade');
        //$mform->addElement('hidden', 'grade', 10, PARAM_INT); //$quizconfig->maximumgrade
        //$mform->setType('grade', PARAM_FLOAT);
        // Number of gradings?.
        $gradingsLimit = 10;
        $attemptoptions = array('0' => get_string('unlimited'));
        for ($i = 1; $i <= $gradingsLimit; $i++) {
            $attemptoptions[$i] = $i;
        }
        $mform->addElement('select', 'attempts', get_string('attemptsallowed', 'quiz'), $attemptoptions);
//        $mform->setAdvanced('attempts', $quizconfig->attempts_adv);
//        $mform->setDefault('attempts', $quizconfig->attempts); 
//               
        // Grading method.
        $mform->addElement('select', 'grademethod', get_string('grademethod', 'quiz'), array(
            QUIZ_GRADEHIGHEST => get_string('gradehighest', 'quiz'),
            QUIZ_GRADEAVERAGE => get_string('gradeaverage', 'quiz'),
            QUIZ_ATTEMPTFIRST => get_string('attemptfirst', 'quiz'),
            QUIZ_ATTEMPTLAST => get_string('attemptlast', 'quiz')
        )); //TO-DO move the array to the locallib.php file

        $mform->addHelpButton('grademethod', 'grademethod', 'quiz');
//        $mform->setAdvanced('grademethod', $quizconfig->grademethod_adv);
//        $mform->setDefault('grademethod', $quizconfig->grademethod);
        $mform->disabledIf('grademethod', 'attempts', 'eq', 1);

//submission types        
        $mform->addElement('header', 'submissiontypes', get_string('submissiontypes', 'assign'));

        $submissionpluginsenabled = array();
        $group = $mform->addGroup(array(), 'submissionplugins', get_string('submissiontypes', 'assign'), array(' '), false);
        foreach ($this->submissionplugins as $plugin) {
            $this->add_plugin_settings($plugin, $mform, $submissionpluginsenabled);
        }
        $group->setElements($submissionpluginsenabled);

        $mform->addElement('header', 'feedbacktypes', get_string('feedbacktypes', 'assign'));
        $feedbackpluginsenabled = array();
        $group = $mform->addGroup(array(), 'feedbackplugins', get_string('feedbacktypes', 'assign'), array(' '), false);
        foreach ($this->feedbackplugins as $plugin) {
            $this->add_plugin_settings($plugin, $mform, $feedbackpluginsenabled);
        }
        $group->setElements($feedbackpluginsenabled);
        $mform->setExpanded('submissiontypes');        
        
        // -------------------------------------------------------------------------------
        $mform->addElement('header', 'layouthdr', get_string('layout', 'quiz'));

        // Languages and their runtimes should really be in the database
        // and shown here
        $mform->addElement('select', 'language', 'Language', array("java" => "java",
            "c" => "c", "c++" => "c++",
            "octave" => "octave",
            "matlab" => "matlab",
            "python2" => "python2.7",
            "python3" => "python3",
            "prolog" => "prolog",
            "javascript" => "javascript"));

        // -------------------------------------------------------------------------------
        $mform->addElement('header', 'security', get_string('extraattemptrestrictions', 'quiz'));

        // Require password to begin quiz attempt.
        $mform->addElement('passwordunmask', 'quizpassword', get_string('requirepassword', 'quiz'));
        $mform->setType('quizpassword', PARAM_TEXT);
        $mform->addHelpButton('quizpassword', 'requirepassword', 'quiz');
//        $mform->setAdvanced('quizpassword', $quizconfig->password_adv);
//        $mform->setDefault('quizpassword', $quizconfig->password);
        // -------------------------------------------------------------------------------
        // feedback delivered for grade ranges
        $mform->addElement('header', 'overallfeedbackhdr', get_string('overallfeedback', 'quiz'));
        $mform->addHelpButton('overallfeedbackhdr', 'overallfeedback', 'quiz');


        //-------------------------------------------------------------------------------
        // Adding the rest of codehandin settings, spreeading all them into this fieldset
        // or adding more fieldsets ('header' elements) if needed for better logic
        // Should be populated from the db actually, with a button to repeat elements
        /*

          $sql = "SELECT *
          FROM {codehandin},{codehandin_checkpoint},{codehandin_test}
          WHERE {codehandin}.id = ?
          AND {codehandin_checkpoint}.assign_id = {codehandin}.id
          AND {codehandin_test}.checkpoint_id = {codehandin_checkpoint}.id";
          $checkpoints =  $DB->get_records_sql($sql,array(0));

          // Template for js'ing in more checkpoints or tests
          $mform->addElement('html','<div class="codehandin_template" style="display:none">');
          $mform->addElement('html','<div class="codehandin_checkpoint" id="checkpoint_template">');
          $mform->addElement('text', 'checkpoint_name','Checkpoint Name');
          $mform->addElement('textarea', 'checkpoint_description', 'Description', 'wrap="virtual" rows="5" cols="50"');
          $mform->addElement('static', 'tests','Tests');
          $mform->addElement('html','<hr/>');
          // Show the test
          $mform->addElement('html','<div class="codehandin_test" id="test_template">');
          $mform->addElement('selectyesno', 'private', 'Private');
          // Use a file instead? Provide a script to generate such a file? Possibly json? {'input':'123','output':'456'}
          $mform->addElement('text', 'test_args','Runtime Arguments');
          $mform->addElement('textarea', 'input', 'Input', 'wrap="virtual" rows="5" cols="50"');
          $mform->addElement('textarea', 'Output', 'Output', 'wrap="virtual" rows="5" cols="50"');
          $mform->addElement('button','addTestButton','Add Another Test');
          $mform->addElement('html','</div>');
          $mform->addElement('html','<hr/>');
          $mform->addElement('html','</div>');

          foreach($checkpoints as $k=>$i) {
          // Show the checkpoint input

          $mform->addElement('header','Checkpoints','Checkpoints', 'form');
          $mform->addElement('html','<div class="codehandin_checkpoint" id="checkpoint_'.$k.'">');
          $mform->addElement('text', 'checkpoint_name','Checkpoint Name');
          $mform->addElement('textarea', 'checkpoint_description', 'Description', 'wrap="virtual" rows="5" cols="50"');
          $mform->addElement('static', 'tests','Tests');
          $mform->addElement('html','<hr/>');
          foreach($tests as $v=>$j) {
          // Show the test
          $mform->addElement('html','<div class="codehandin_test" id="test_'.$v.'">');
          $mform->addElement('selectyesno', 'private', 'Private');
          // Use a file instead? Provide a script to generate such a file? Possibly json? {'input':'123','output':'456'}
          $mform->addElement('text', 'test_args','Runtime Arguments');
          $mform->addElement('textarea', 'input', 'Input', 'wrap="virtual" rows="5" cols="50"');
          $mform->addElement('textarea', 'Output', 'Output', 'wrap="virtual" rows="5" cols="50"');
          $mform->addElement('button','addTestButton','Add Another Test');
          $mform->addElement('html','</div>');
          $mform->addElement('html','<hr/>');
          }
          $mform->addElement('html','</div>');
          }
          $mform->addElement('button','addCheckpointButton','Add Another Checkpoint');

          //Load in the data from db
          //$mform->set_data();

         */
        //-------------------------------------------------------------------------------
        // add standard elements, common to all modules
        $this->standard_coursemodule_elements();
        //-------------------------------------------------------------------------------
        // add standard buttons, common to all modules
        $this->add_action_buttons();
    }

    // Add a checkpoint, optionally leave the fields blank
    private function add_checkpoint(&$form, $data = []) {
        
    }

    // Add a test, optionally leave the fields blanks
    private function add_test(&$form, $data = []) {
        
    }

}
