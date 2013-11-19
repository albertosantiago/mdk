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
 * Add page to admin menu.
 *
 * @package    local
 * @subpackage adminer
 * @copyright  2011 Andreas Grabs
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die;


if ($hassiteconfig) { // needs this condition or there is error on login page
	$ADMIN->add('development', new admin_category('mdk', get_string('pluginname', 'local_mdk')));
	
    $ADMIN->add('mdk', new admin_externalpage('emdk_install_or_update',
            "Install/Update plugins",
            new moodle_url('/local/mdk/index.php')));
    
    $ADMIN->add('mdk', new admin_externalpage('mdk_system',
    		"Console",
    		new moodle_url('/local/mdk/system.php')));

    $ADMIN->add('mdk', new admin_externalpage('mdk_sql',
    		"SQL",
    		new moodle_url('/local/mdk/sql.php')));
    $ADMIN->add('mdk', new admin_externalpage('mdk_fast',
    		"Fast commands",
    		new moodle_url('/local/mdk/fast.php')));
}
