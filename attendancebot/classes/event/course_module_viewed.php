<?php
// This file is part of Moodle - https://moodle.org/
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
// along with Moodle.  If not, see <https://www.gnu.org/licenses/>.

namespace mod_attendancebot\event;

/**
 * The course_module_viewed event class.
 *
 * @package     mod_attendancebot
 * @category    event
 * @copyright   2024 Your Name <you@example.com>
 * @license     https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class course_module_viewed extends \core\event\course_module_viewed {

    // For more information about the Events API please visit {@link https://docs.moodle.org/dev/Events_API}.
    
    /**
     * Init method.
     *
     * @return void
     */
    protected function init() {
      $this->data['crud'] = 'r';
      $this->data['edulevel'] = self::LEVEL_PARTICIPATING;
      $this->data['objecttable'] = 'attendancebot';
    }
    
    
}
