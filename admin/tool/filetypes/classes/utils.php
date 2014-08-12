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
 * A collections of class methods used by the custom file type tool.
 *
 * @package tool_filetypes
 * @copyright 2014 The Open University
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace tool_filetypes;

defined('MOODLE_INTERNAL') || die();

/**
 * Class with collective methods for managing and displaying customised file types.
 *
 * @package tool_filetypes
 * @copyright 2014 The Open University
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class utils {
    /**
     * @var string Character used to separate each file type entry
     */
    const ENTRY_DIVIDER = "\n";
    /**
     * @var string Character used to separate attributes within one file type entry
     */
    const ATR_DIVIDER = ';';
    /**
     * Add a new customised file type entry. Each entry includes 4 attributes which are extension,
     * mimetype, icon, and description. Entries are stored in the config table as a single record
     * for better performance. They are separated by line feed character.
     * The attributes within each entry are separated by semicolon character.
     * If you want to use a custom icon called 'mobi' then you will need to put icon files
     * such as mobi.png, mobi-24.png, mobi-32 etc. in pix/f folder.
     *
     * @param string $extension Extension of the file type such as mobi for Kindle ebooks
     * @param string $mimetype Mime type of the file type such as pplication/x-mobipocket-ebook
     *   for Kindle ebooks.
     * @param string $icon Icon name of the file type without extension or size e.g. 'document'
     * @param string $description Description of the file type
     * @throws moodle_exception If add entry failed
     */
    public static function add_entry($extension, $mimetype, $icon, $description) {
        global $CFG;

        // Check if the input entry data contains invalid characters.
        if ((strpos($extension . $mimetype . $icon . $description, self::ENTRY_DIVIDER) !== false) ||
                (strpos($extension . $mimetype . $icon, self::ATR_DIVIDER) !== false)) {
            throw new \moodle_exception('error_addentry', 'tool_filetypes',
                    new \moodle_url('/admin/tool/filetypes/index.php'));
        }

        $existing = '';
        if (!empty($CFG->customfiletypes)) {
            $existing = $CFG->customfiletypes . self::ENTRY_DIVIDER;
        }
        set_config('customfiletypes',
                $existing . trim($extension) . self::ATR_DIVIDER . trim($mimetype) .
                self::ATR_DIVIDER . $icon . self::ATR_DIVIDER . trim($description));
    }

    /**
     * Delete a customised file type entry.
     *
     * @param string $extension Extension of the file type
     * @throws moodle_exception If delete failed
     */
    public static function delete_entry($extension) {
        global $CFG;
        $customfiletypes = '';
        if (!empty($CFG->customfiletypes)) {
            // Add line feed characters to the start and end of the string in order to use regular
            // expression to match the pattern.
            $customfiletypes = self::ENTRY_DIVIDER . $CFG->customfiletypes . self::ENTRY_DIVIDER;
        }

        if ($customfiletypes === '' ||
                strpos($customfiletypes, self::ENTRY_DIVIDER . $extension . self::ATR_DIVIDER) === false) {
            throw new \moodle_exception('error_notfound', 'tool_filetypes',
                     new \moodle_url('/admin/tool/filetypes/index.php'), $extension);
        }
        $customfiletypes = preg_replace('~' . self::ENTRY_DIVIDER . $extension . '.+?' .
                self::ENTRY_DIVIDER . '~', self::ENTRY_DIVIDER, $customfiletypes);
        $customfiletypes = trim($customfiletypes, self::ENTRY_DIVIDER);
        if ($customfiletypes == '') {
            unset_config('customfiletypes');
        } else {
            set_config('customfiletypes', $customfiletypes);
        }

    }

    /**
     * Update an existing file type entry.
     *
     * @param string $oldextension Extension of the file type before update
     * @param string $newextension Extension of the file type after update
     * @param string $mimetype Mime type of the file type
     * @param string $icon Icon name of the file type without extension or size e.g. 'document'
     * @param string $description Description of the file type
     * @throws moodle_exception If update failed
     */
    public static function update_entry($oldextension, $newextension, $mimetype, $icon, $description) {
        global $CFG;
        $customfiletypes = '';
        if (!empty($CFG->customfiletypes)) {
            // Add line feed characters to the start and end of the string in order to use regular
            // expression to match the pattern.
            $customfiletypes = self::ENTRY_DIVIDER . $CFG->customfiletypes . self::ENTRY_DIVIDER;

        }

        if ($customfiletypes === '' ||
                strpos($customfiletypes, self::ENTRY_DIVIDER . $oldextension . self::ATR_DIVIDER) === false) {
            throw new \moodle_exception('error_notfound', 'tool_filetypes',
                     new \moodle_url('/admin/tool/filetypes/index.php'), $oldextension);
        }
        $customfiletypes = preg_replace('~' . self::ENTRY_DIVIDER . $oldextension . '.+?' .
                self::ENTRY_DIVIDER . '~', self::ENTRY_DIVIDER . trim($newextension) .
                self::ATR_DIVIDER . trim($mimetype) . self::ATR_DIVIDER . $icon . self::ATR_DIVIDER .
                trim($description) . self::ENTRY_DIVIDER , $customfiletypes);
        set_config('customfiletypes', trim($customfiletypes, self::ENTRY_DIVIDER));
    }

    /**
     * Check if the given file type extension is invalid.
     * The customised added file type extension must be unique.
     *
     * @param string $extension Extension of the file type to add
     * @param string $oldextension Extension of the file type before update or an empty string
     *   by default for adding a new file type
     * @return bool True if it the file type trying to add already exists
     */
    public static function is_filetype_invalid($extension, $oldextension='') {
        $mimeinfo = get_mimetypes_array();
        if ($oldextension !== '') {
            unset($mimeinfo[$oldextension]);
        }
        return array_key_exists(trim($extension), $mimeinfo);
    }

    /**
     * Get all customised file types or the file type with specified extension.
     * The returned file type objects containing properties of extension, mimetype, icon, and description.
     *
     * @param string $extension Extension of the requested file type
     * @return array An array of filetype objects indexed by file type extension
     * @throws moodle_exception If the there is no file type associated with the given extension
     */
    public static function get_filetypes($extension = '') {
        global $CFG;

        $results = array();
        if (empty($CFG->customfiletypes)) {
            return $results;
        } else {
            $entries = explode(self::ENTRY_DIVIDER, $CFG->customfiletypes);
            foreach ($entries as $entry) {
                // File type description is on the last so that it can contain semicolon characters.
                $temp = explode(self::ATR_DIVIDER, $entry, 4);
                $item = new \stdClass();
                $item->extension = $temp[0];
                $item->mimetype = $temp[1];
                $item->icon = $temp[2];
                $item->description = $temp[3];
                $results[$item->extension] = $item;
            }
            if ($extension == '') {
                return $results;
            }
            if (array_key_exists($extension, $results)) {
                return $results[$extension];
            }
            throw new \moodle_exception('error_notfound', 'tool_filetypes',
                     new \moodle_url('/admin/tool/filetypes/index.php'), $extension);
        }
    }
    /**
     * Get the customised file type description based on the given minetype.
     *
     * @param string $mimetype Mime type of the customised file type
     * @return string The customised minetype description or an empty string if no found
     */
    public static function get_mimetype_description ($mimetype) {
        $description = '';
        if (!empty($mimetype)) {
            $filetypes = self::get_filetypes();
            foreach ($filetypes as $filetype) {
                if ($filetype->mimetype == $mimetype) {
                    $description = $filetype->description;
                    break;
                }
            }
        }
        return $description;
    }
    /**
     * Get all unique file type icons from a specific path excluding sub-directories if any.
     * Icon files such as pdf.png, pdf-24.png and pdf-36.png etc. are counted as the same icon type.
     * The resultant array has both key and value set to the icon name prefix such as 'pdf'=> 'pdf'
     *
     * @param string $path The path of the icon path
     * @return array An array of unique file icons within the given path
     */
    public static function get_icons_from_path($path) {
        $icons = array();
        // Get a list of file icons from core.
        if ($handle = @opendir($path)) {
            while (($file = readdir($handle)) !== false) {
                $matches = array();
                if (preg_match('~(.+?)(?:-24|-32|-48|-64|-72|-80|-96|-128|-256)?\.(?:gif|png)$~',
                        $file, $matches)) {
                    $key = $matches[1];
                    $icons[$key] = $key;
                }
            }
            closedir($handle);
        }
        return $icons;
    }

    /**
     * Get unique file type icons from pix/f folder.
     *
     * @return array An array of unique file type icons.
     */
    public static function get_file_icons() {
        global $CFG, $PAGE;
        $icons = array();
        $path = $CFG->dirroot . '/pix/f';
        // Get a list of file icons from core.
        $icons = self::get_icons_from_path($path);
        ksort($icons);
        return $icons;
    }
}
