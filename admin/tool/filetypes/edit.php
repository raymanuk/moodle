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
 * Display the file type updating page.
 *
 * @package tool_filetypes
 * @copyright 2014 The Open University
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
require(__DIR__ . '/../../../config.php');
require_once($CFG->libdir . '/adminlib.php');
require_once('edit_form.php');

admin_externalpage_setup('customfiletypes');

$oldextension = optional_param('oldextension', '', PARAM_TEXT);
$mform = new tool_filetypes_form('edit.php', array('oldextension' => $oldextension));
$title = get_string('addfiletypes', 'tool_filetypes');

if ($oldextension) {
    // This is editing to existing filetype, load data to the form.
    $filetype = \tool_filetypes\utils::get_filetypes($oldextension);
    $mform->set_data($filetype);
    $title = get_string('editfiletypes', 'tool_filetypes');
}

$backurl = new \moodle_url('/admin/tool/filetypes/index.php');
if ($mform->is_cancelled()) {
    redirect($backurl);
} else if ($data = $mform->get_data()) {
    if ($data->oldextension) {
        // Update an existing file type.
        \tool_filetypes\utils::update_entry($data->oldextension, $data->extension,
                $data->mimetype, $data->icon, $data->description);

    } else {
        // Add a new file type entry.
        \tool_filetypes\utils::add_entry($data->extension,
                $data->mimetype, $data->icon, $data->description);
    }
    redirect($backurl);
}

// Page settings.
$context = context_system::instance();
$PAGE->set_url(new \moodle_url('/admin/tool/filetypes/edit.php', array('oldextension' => $oldextension)));
$PAGE->navbar->add($title);
$PAGE->set_context($context);
$PAGE->set_pagelayout('admin');
$PAGE->set_heading($title);
$PAGE->set_title($SITE->fullname. ': ' . $title);

// Display the page.
echo $OUTPUT->header();
$mform->display();
echo $OUTPUT->footer();
