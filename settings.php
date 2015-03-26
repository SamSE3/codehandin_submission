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
 * Defines the global settings of the codehandin submission plugin
 * 
 * @package    assignsubmission_codehandin
 * @copyright  2014 Samuel Deane
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */


global $DB;

// Note: Should never be auto enabled by default!
//$settings->add(new admin_setting_configcheckbox('assignsubmission_codehandin/default',
//                   new lang_string('default', 'assignsubmission_codehandin'),
//                   new lang_string('default_help', 'assignsubmission_codehandin'), 0));

$name = new lang_string('defaultproglang', 'assignsubmission_codehandin');
$description = new lang_string('defaultproglang_help', 'assignsubmission_codehandin');
$languages = $DB->get_records_select_menu('codehandin_proglang', null, null, 'id ASC', 'id, name');
$settings->add(new admin_setting_configselect('assignsubmission_codehandin/defaultproglang', 
        $name, $description, 1, $languages)); 

$settings->add(new admin_setting_configcheckbox('assignsubmission_codehandin/mustattemptcompile',
                   new lang_string('mustattemptcompile', 'assignsubmission_codehandin'),
                   new lang_string('mustattemptcompile_help', 'assignsubmission_codehandin'), 1));