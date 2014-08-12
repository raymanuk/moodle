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
 * Unit tests for the custom file types.
 *
 * @package tool_filetypes
 * @copyright 2014 The Open University
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * Unit tests for the custom file types.
 *
 * @package tool_filetypes
 * @copyright 2014 The Open University
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class tool_filetypes_test extends advanced_testcase {
    /**
     * @var array Initial custom file type entries. These are some dummy file types
     *   which should never exists in Moodle.
     */
    private static $entries = array(
            array('extension' => 'mobi8', 'mimetype' => 'application/x-mobipocket-ebook',
            'icon' => 'mobi', 'description' => 'Kindle ebook'),
            array('extension' => 'daisy8', 'mimetype' => 'audio/mpeg3',
            'icon' => 'daisy', 'description' => 'Audio book'),
            array('extension' => 'ggb8', 'mimetype' => 'application/vnd.geogebra.file',
            'icon' => 'ggb', 'description' => 'GeoGebra data'));

    /**
     * Tests add, delete and update custom file types.
     */
    public function test_filetypes_editing() {
        global $DB, $CFG;

        $this->resetAfterTest(true);
        // Test add_entry() function by creating some entries.
        $counts = count(self::$entries);
        for ($i = 0; $i < $counts; $i++) {
            \tool_filetypes\utils::add_entry(self::$entries[$i]['extension'],
                    self::$entries[$i]['mimetype'], self::$entries[$i]['icon'],
                    self::$entries[$i]['description']);
        }
        $filetypes = $CFG->customfiletypes;
        for ($i = 0; $i < $counts; $i++) {
            $this->assertTrue(strpos($filetypes, self::$entries[$i]['extension']) !== false);
        }

        // Test delete_entry() function.
        $deletetype = self::$entries[0]['extension'];
        \tool_filetypes\utils::delete_entry($deletetype);
        $filetypes = $CFG->customfiletypes;
        $this->assertTrue(strpos($filetypes, $deletetype) === false);

        // Test update_entry() function.
        $updatetype = self::$entries[1]['extension'];
        \tool_filetypes\utils::update_entry($updatetype, 'war', 'application/x-zip', 'war',
                'Web application archive');
        $filetypes = $CFG->customfiletypes;
        $this->assertTrue(strpos($filetypes, $updatetype) === false);
        $this->assertTrue(strpos($filetypes, 'war') !== false);
    }

    /**
     * Tests is_filetype_invalid() function.
     */
    public function test_is_filetype_invalid() {
        global $DB;

        $this->resetAfterTest(true);
        // The pdf file extension already exists in default moodle minetypes.
        $this->assertTrue(\tool_filetypes\utils::is_filetype_invalid('pdf'));
        // The mobi8 enxtension can be added as a new custom file type as it hasn't existed yet.
        $this->assertFalse(\tool_filetypes\utils::is_filetype_invalid(self::$entries[0]['extension']));
        // Add mobi8 as a new custom file type.
        \tool_filetypes\utils::add_entry(self::$entries[0]['extension'],
                self::$entries[0]['mimetype'], self::$entries[0]['icon'], self::$entries[0]['description']);
        // Now mobi8 becomes invalid as it has already been added.
        $this->assertTrue(\tool_filetypes\utils::is_filetype_invalid(self::$entries[0]['extension']));
        // However we can still update the mobile8 file type .
        $this->assertFalse(\tool_filetypes\utils::is_filetype_invalid(self::$entries[0]['extension'],
                self::$entries[0]['extension']));
    }

    /**
     * Tests get_filetypes() function.
     */
    public function test_get_filetypes() {
        $this->resetAfterTest(true);

        // Create some entries first.
        $counts = count(self::$entries);

        for ($i = 0; $i < $counts; $i++) {
            \tool_filetypes\utils::add_entry(self::$entries[$i]['extension'], self::$entries[$i]['mimetype'],
                    self::$entries[$i]['icon'], self::$entries[$i]['description']);
        }
        // Get all the custom file types.
        $filetypes = \tool_filetypes\utils::get_filetypes();
        $this->assertEquals(count($filetypes), $counts);

        for ($i = 0; $i < $counts; $i++) {
            $this->assertTrue(array_key_exists(self::$entries[$i]['extension'], $filetypes));
            // Get a particular file type.
            $filetype = \tool_filetypes\utils::get_filetypes(self::$entries[$i]['extension']);
            $this->assertEquals($filetype->extension, self::$entries[$i]['extension']);
            $this->assertEquals($filetype->mimetype, self::$entries[$i]['mimetype']);
            $this->assertEquals($filetype->icon, self::$entries[$i]['icon']);
            $this->assertEquals($filetype->description, self::$entries[$i]['description']);
        }
    }
}
