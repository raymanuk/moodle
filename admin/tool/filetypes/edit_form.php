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
 * Customised file types editing form.
 *
 * @package tool_filetypes
 * @copyright 2014 The Open University
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once($CFG->dirroot . '/lib/formslib.php');

/**
 * Form for adding a new custom file type or updating an existing custom file type.
 *
 * @package tool_filetypes
 * @copyright 2014 The Open University
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class tool_filetypes_form extends moodleform {

    /**
     * Define the form.
     */
    public function definition() {
        global $CFG;
        $mform = $this->_form;
        $oldextension = $this->_customdata['oldextension'];

        $mform->addElement('text', 'extension', get_string('extension', 'tool_filetypes'));
        $mform->setType('extension', PARAM_TEXT);
        $mform->addRule('extension', null, 'required', null, 'client');
        $mform->addHelpButton('extension', 'extension', 'tool_filetypes');

        $mform->addElement('text', 'description',  get_string('description', 'tool_filetypes'));
        $mform->setType('description', PARAM_TEXT);
        $mform->addRule('description', null, 'required', null, 'client');
        $mform->addHelpButton('description', 'description', 'tool_filetypes');

        $mform->addElement('text', 'mimetype',  get_string('mimetype', 'tool_filetypes'));
        $mform->setType('mimetype', PARAM_TEXT);
        $mform->addRule('mimetype', null, 'required', null, 'client');
        $mform->addHelpButton('mimetype', 'mimetype', 'tool_filetypes');

        $fileicons = \tool_filetypes\utils::get_file_icons();
        $mform->addElement('select', 'icon',
                get_string('icon', 'tool_filetypes'), $fileicons);
        $mform->addHelpButton('icon', 'icon', 'tool_filetypes');

        $mform->addElement('hidden', 'oldextension', $oldextension);
        $mform->setType('oldextension', PARAM_TEXT);
        $this->add_action_buttons(true, get_string('savechanges'));
    }

    /**
     * Validate the form input data.
     *
     * @param array $data form data
     * @param array $files files in form
     * @return array errors
     */
    public function validation($data, $files) {
        // When editing an existing filetype, $oldextension will be set the existing extension.
        $oldextension = optional_param('oldextension', '', PARAM_TEXT);
        $errors = parent::validation($data, $files);
        $extension = trim($data['extension']);
        if (\tool_filetypes\utils::is_filetype_invalid($extension, $oldextension)) {
            $errors['extension'] = get_string('error_extension', 'tool_filetypes', $extension);
        }
        return $errors;
    }
}
