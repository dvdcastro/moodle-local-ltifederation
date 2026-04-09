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
 * Task definitions for local_ltifederation.
 *
 * @package     local_ltifederation
 * @copyright   2026 David Castro
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

$tasks = [
    // Scheduled task: sync all providers with autosync=1 (daily at 3:00 am).
    [
        'classname' => '\local_ltifederation\task\sync_all_providers',
        'blocking'  => 0,
        'minute'    => '0',
        'hour'      => '3',
        'day'       => '*',
        'month'     => '*',
        'dayofweek' => '*',
        'disabled'  => 0,
    ],
    // Scheduled task: clean up expired draft registrations (daily at 3:30 am).
    [
        'classname' => '\local_ltifederation\task\cleanup_draft_registrations',
        'blocking'  => 0,
        'minute'    => '30',
        'hour'      => '3',
        'day'       => '*',
        'month'     => '*',
        'dayofweek' => '*',
        'disabled'  => 0,
    ],
];
