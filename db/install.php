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
 * Post-install code for the submission_onlinetext module.
 *
 * @package assignsubmission_codehandin
 * @copyright 2014 Samuel Deane
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
defined('MOODLE_INTERNAL') || die();
require_once($CFG->dirroot.'/mod/assign/adminlib.php');

function endc($array) {
    return end($array);
}

/**
 * Code run after the assignsubmission_codehandin module database tables have been created.
 * @return bool
 */
function xmldb_assignsubmission_codehandin_submission_install() {
    global $CFG, $DB, $OUTPUT;

    $pluginmanager = new assign_plugin_manager('assignsubmission');
    $pluginmanager->move_plugin('codehandin_submission', 'down');
    $pluginmanager->move_plugin('codehandin_submission', 'down');
    $pluginmanager->move_plugin('codehandin_submission', 'down');
    $pluginmanager->move_plugin('codehandin_submission', 'down');
    $pluginmanager->move_plugin('codehandin_submission', 'down');

    $record1 = new stdClass();
    //$record1->id = 1;    
    $record1->name = 'c';
    $record1->defaultscript = 'gcc -std=c99 -O2  ,*.c -o ,main,';
    $record2 = new stdClass();
    //$record2->id = 2;    
    $record2->name = 'c++';
    $record2->defaultscript = 'g++ -std=c++11 -O2 ,*.cpp -o ,main,';
    $record3 = new stdClass();
    //$record3->id = 3;
    $record3->name = 'java';
    $record3->defaultscript = 'javac , /*.java -d , ';
    $record4 = new stdClass();
    //$record4->id = 4;
    $record4->name = 'mathlab';
    // $record4->defaultscript = '';
    $record5 = new stdClass();
    //$record5->id = 5;
    $record5->name = 'octave';
    //$record5->defaultscript = '';
    $record6 = new stdClass();
    //$record6->id = 6;
    $record6->name = 'python2';
    //$record6->defaultscript = '';
    $record7 = new stdClass();
    //$record7->id = 7;
    $record7->name = 'python3';
    //$record7->defaultscript = '';
    $record8 = new stdClass();
    //$record8->id = 8;
    $record8->name = 'javascript';
    //$record8->defaultscript = '';
    $record9 = new stdClass();
    //$record9->id = 9;
    $record9->name = 'prolog';
    //$record9->defaultscript = '';
    $record10 = new stdClass();
    //$record10->id = 10;
    $record10->name = 'R';
    //$record10->defaultscript = '';
    $records = array($record1, $record2, $record3, $record4, $record5,
        $record6, $record7, $record8, $record9, $record10);
    foreach ($records as $record) {
        $lastinsertid = $DB->insert_record('codehandin_proglang', $record);
    }

    //create the data directory
    $userTempDir = $CFG->dataroot . '/codehandin/';
    // Make sure the user has a dir in the temp dir
    if (!file_exists($userTempDir)) {
        mkdir($userTempDir, 0777, true);
    }

    return true;
}
