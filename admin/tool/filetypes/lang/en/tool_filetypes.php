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
 * Strings for custom file types.
 *
 * @package tool_filetypes
 * @copyright 2014 The Open University
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */


$string['pluginname'] = 'Custom file types';
$string['addfiletypes'] = 'Add a new custom file type';
$string['editfiletypes'] = 'Edit an existing custom file type';
$string['deletefiletypes'] = 'Delete an custom file type';
$string['extension'] = 'File extension';
$string['extension_help'] = 'File name extension without the dot, e.g. &lsquo;mobi&rsquo;';
$string['description'] = 'File description';
$string['description_help'] = 'Simple file type description, e.g. &lsquo;Kindle ebook&rsquo;';
$string['mimetype'] = 'File MIME type';
$string['mimetype_help'] = 'MIME type associated with this file type, e.g. &lsquo;application/x-mobipocket-ebook&rsquo;';
$string['icon'] = 'File icon';
$string['icon_help'] = 'Icon filename.

The list of icons is taken from the /pix/f directory inside your Moodle installation. You can add custom icons to this folder if required.';
$string['emptylist'] = 'You haven&rsquo;t added any custom file types yet.';

$string['error_notfound'] = 'The file type with extension {$a} cannot be found.';
$string['error_extension'] = 'The file type extension <strong>{$a}</strong> already exists or is invalid. File extensions must be unique and must not contain special characters.';
$string['error_addentry'] = 'The file type extension, description,  MIME type, and icon must not contain line feed and semicolon characters.';
$string['delete_confirmation'] = 'Are you absolutely sure you want to remove <strong>{$a}</strong>?';
