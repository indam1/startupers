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

 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */


require('../../config.php');



use availability_completion\condition;

require_once($CFG->libdir . '/completionlib.php');

$id = optional_param('id', 0, PARAM_INT);        // Course module ID
$plainrendering = optional_param('plain', 0, PARAM_INT); // Display borderless as image
$lightweightrendering = optional_param('lightweight', 0, PARAM_INT); // Display borderless as svg
$cm = get_coursemodule_from_id('activitymap', $id, 0, false, MUST_EXIST);
$course = $DB->get_record('course', array('id'=>$cm->course), '*', MUST_EXIST);
$courseid = $cm->course;

require_course_login($course, true, $cm);
$context = context_module::instance($cm->id);
require_capability('mod/activitymap:view', $context);
$PAGE->set_url('/mod/activitymap/view.php', array('id' => $cm->id));
$PAGE->set_title('RoadMap');

/// Print the page header
if($plainrendering == false && $lightweightrendering == false)
{
    echo $OUTPUT->header();
}

$module = $DB->get_record('activitymap', array('id'=>$cm->instance), '*', MUST_EXIST);

$sql = "Select cm.id, cm.module, m.name 
        from {course_modules} cm
        left outer join {modules} m on m.id = cm.module
        where cm.module <> 25 and deletioninprogress = 0
        order by cm.id asc";

$modules = $DB->get_records_sql($sql);

$i = 1;
foreach ($modules as $modul) {
    $modul->number = $i;
    $i++;
}

$templatecontext= (object)[
    'modules' => array_values($modules),
];

echo $OUTPUT->render_from_template('mod_activitymap/view', $templatecontext);

// --------------------------------
// Print the footer
if($plainrendering == false && $lightweightrendering == false)
{
    echo $OUTPUT->footer();
}

