<?php
defined('MOODLE_INTERNAL') || die();

function xmldb_local_library_install() {
	global $CFG;

	set_config("save_values", true,"local_mdk");
	set_config("fast_command_reload_plugins", true,"local_mdk");
	set_config("reload_plugins", serialize(array()),"local_mdk");

}
