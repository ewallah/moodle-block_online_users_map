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
 * Online Users Map block - Customised online users map based on work of Alex Little
 *
 * @package   block_online_users_map
 * @copyright iplusacademy.org (www.iplusacademy.org)
 * @author    Renaat Debleu <info@eWallah.net>
 * @author    Alex Little
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

$plugin->component = 'block_online_users_map';
$plugin->version = 2024062600;
$plugin->requires = 2023042400;
$plugin->release = 'v4.4.1';
$plugin->supported = [402, 404];
$plugin->maturity = MATURITY_STABLE;
