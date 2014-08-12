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
 * Renderer.
 *
 * @package tool_filetypes
 * @copyright 2014 The Open University
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * Class containing the renderer functions for displaying the custom file type.
 *
 * @package tool_filetypes
 * @copyright 2014 The Open University
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class tool_filetypes_renderer extends plugin_renderer_base {

    /**
     * Renderer for displaying the file type edit table.
     *
     * @param array $filetypes An array of customised file type objects
     * @return string HTML code
     */
    public function edit_table(array $filetypes) {

        $out = $this->heading(get_string('editfiletypes', 'tool_filetypes'));
        $count = count($filetypes);
        if ($count) {
            // Display the file type table if there are any custom file types exist.
            $table = new html_table();
            $headings = new html_table_row();
            $headings->cells = array(
                    new html_table_cell(get_string('extension', 'tool_filetypes')),
                    new html_table_cell(get_string('description', 'tool_filetypes')),
                    new html_table_cell(get_string('mimetype', 'tool_filetypes')),
                    new html_table_cell(get_string('icon', 'tool_filetypes')),
                    new html_table_cell(get_string('edit')));
            foreach ($headings->cells as $cell) {
                $cell->header = true;
            }
            $table->data = array($headings);
            foreach ($filetypes as $filetype) {
                $row = new html_table_row();
                $row->cells = array();
                $row->cells[] = new html_table_cell(s($filetype->extension));
                $row->cells[] = new html_table_cell(s($filetype->description));
                $row->cells[] = new html_table_cell(s($filetype->mimetype));
                $row->cells[] = new html_table_cell($this->pix_icon('f/' . s($filetype->icon),
                        s($filetype->description)));
                $editurl = new \moodle_url('/admin/tool/filetypes/edit.php',
                        array('oldextension' => $filetype->extension));
                $editbutton = html_writer::link($editurl, $this->pix_icon('t/edit', get_string('update')));
                $deleteurl = new \moodle_url('/admin/tool/filetypes/delete.php',
                        array('extension' => $filetype->extension));
                $deletebutton = html_writer::link($deleteurl,
                        $this->pix_icon('t/delete', get_string('delete')));
                $row->cells[] = new html_table_cell($editbutton . ' ' . $deletebutton);
                $table->data[] = $row;
            }
            $out .= html_writer::table($table);
        } else {
            $out .= html_writer::tag('div', get_string('emptylist', 'tool_filetypes'));
        }
        // Displaying the 'Add' button.
        $out .= $this->single_button(new moodle_url('/admin/tool/filetypes/edit.php',
                array('name' => 'add')), get_string('add'), 'get');
        return $out;
    }
}
