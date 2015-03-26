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
 * Strings for activity plugin 'codehandin', language 'en'
 *
 * @package    assignsubmission_codehandin
 * @copyright  2014 Jonathan Mackenzie & Samuel Deane
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
defined('MOODLE_INTERNAL') || die();

$string['pluginname'] = 'Codehandin';
$string['pluginnameplural'] = 'Codehandins';
$string['pluginname_help'] = 'Use the codehandin plugin assignments that require code submission. Code is submitted through a webservice, or the web interface. Teacher defined tests allow a mapping from input->output with optional runtime arguments. It is recommended that each checkpoint have at least 1 test marked as assessment.';
$string['codehandinfieldset'] = 'Custom example fieldset';
$string['codehandinname'] = 'codehandin name';
$string['codehandinname_help'] = 'This is the content of the help tooltip associated with the codehandinname field. Markdown syntax is supported.';
$string['codehandin'] = 'codehandin';
$string['pluginadministration'] = 'codehandin administration';
$string['introduction'] = 'an introduction for the handin plugin ... etc.';
$string['enabled'] = 'enable the code handin plugin';
$string['enabled_help'] = '';
$string['proglang'] = 'the programming language';
$string['proglang_help'] = 'select the programming language the assignment is to be written in';
$string['defaultproglang'] = 'the default programming language';
$string['defaultproglang_help'] = 'Select the default programming language the assignments are to be written in';
$string['default'] = 'Enabled by default';
$string['default_help'] = 'If set, this submission method will be enabled by default for all new assignments.';
$string['maxbytes'] = 'Maximum file size';
$string['maxfilessubmission'] = 'Maximum number of uploaded files';
$string['maxfilessubmission_help'] = 'If file submissions are enabled, each student will be able to upload up to this number of files for their submission.';
$string['maximumsubmissionsize'] = 'Maximum submission size';
$string['maximumsubmissionsize_help'] = 'Files uploaded by students may be up to this size.';
$string['numfilesforlog'] = 'The number of file(s) : {$a} file(s).';
$string['siteuploadlimit'] = 'Site upload limit';
$string['submissionfilearea'] = 'Uploaded submission files';
$string['configmaxbytes'] = 'Maximum file size';
$string['countfiles'] = '{$a} files';
$string['codehandinOpt']= 'Codehandin Options';
$string['mustattemptcompile'] = 'Compile must be attempted?';
$string['mustattemptcompile_help']= 'Only allow the submission of code of which has been attemptedly compiled';
$string['spectestonly'] = 'Use specific test file(s) only';
$string['spectestonly_help'] = 'if using a specifc test file without any generated I/O checkpoints and tests';
$string['spectest'] = 'Specific test file';
$string['spectest_help'] = 'to add a specific (unit) test file that the student will use (tests somthing other than I/O)';
$string['spectestassessment'] = 'Specific assessment test file';
$string['spectestassessment_help'] = 'to add a specific (unit) test file that the teacher will use for assessment';
$string['cpname'] = 'Checkpoint Name';
$string['cpname_help'] = 'The name of the checkpoint';
$string['cpordering'] = 'Ordering value';
$string['cpweight'] = 'Checkpoint mark weight';
$string['cpweight_help'] = 'Checkpoint mark weight';
$string['cpordering_help'] = 'Defines the order in which checkpoints are presented';
$string['cpdescription'] = 'Checkpoint description';
$string['cpdescription_help'] = 'A description of the checkpoint';
$string['cpruntimeargs'] = 'Checkpoint runtime arguments';
$string['cpruntimeargs_help'] = 'The runtime args of all tests related to this checkpoint';
$string['testgradeonly'] = 'Test for grading only?';
$string['testgradeonly_help'] = 'Defines if the tests is for grading only (true) i.e. students will not be able to see the results of the test';
$string['testordering'] = 'Ordering value';
$string['testweight'] = 'Test mark weight';
$string['testweight_help'] = 'Test mark weight';
$string['testordering_help'] = 'Defines the order in which tests are presented';
$string['testdecription'] = 'Test description';
$string['testdecription_help'] = 'A description of the test';
$string['testruntimeargs'] = 'Test runtime arguments';
$string['testruntimeargs_help'] = 'The runtime arguments specifc to this test i.e the -add part of \'-add #val1 #val2\' (overrides any cpruntimeargument)';
$string['testretval'] = 'Expected return value';
$string['testretval_help'] = 'The value the compiler is expected to return i.e. 0 for correct compile or 1 for errors';
$string['testinput'] = 'Input for the test';
$string['testinputt'] = 'Input for the test as text';
$string['testinput_help'] = 'the input for the test as a file (text file ... file type is does not matter)';
$string['testoutput'] = 'Output for the test';
$string['testoutput_help'] = 'the output for the test as a file (text file ... file type is does not matter)';
$string['testoutputerr'] = 'Output error for the test (optional)';
$string['testoutputerr_help'] = 'the outputerr for the test as a file if require again as text file where the file type is does not matter';
$string['testoutput'] = 'Output for the test';
$string['testoutputt'] = 'Output for the test as text';
$string['testinputt_help'] = 'the input for the test as a text';
$string['testoutputt_help'] = 'the output for the test as a text';
$string['testoutputerr'] = 'Output error for the test (optional)';
$string['testoutputerrt'] = 'Output error for the test as text (optional)';
$string['testoutputerrt_help'] = 'the outputerr for the test as text';
$string['ioastext'] = 'use text rather than files for i/o/oerr';
$string['ioastext_help'] ='use text rather than files for i/o/oerr';
