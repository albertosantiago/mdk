<?php
defined('MOODLE_INTERNAL') || die;


if ($hassiteconfig) { // needs this condition or there is error on login page
	$ADMIN->add('development', new admin_category('mdk', get_string('pluginname', 'local_mdk')));
	/**
	 * PLUGINS
	 */
	$ADMIN->add('mdk', new admin_category('mdk_plugins', "Plugins Manager"));
    $ADMIN->add('mdk_plugins', new admin_externalpage('emdk_plugins_install_or_update',
            "Install/Update",
            new moodle_url('/local/mdk/plugins/index.php')));
    $ADMIN->add('mdk_plugins', new admin_externalpage('emdk_plugins_reload',
    		"Reload Plugins",
    		new moodle_url('/local/mdk/plugins/reload.php')));
    $ADMIN->add('mdk_plugins', new admin_externalpage('emdk_plugins_create',
    		"Create new plugin",
    		new moodle_url('/local/mdk/plugins/create.php')));

    /**
     * TOOLS
     */
    $ADMIN->add('mdk', new admin_category('mdk_tools', "Tools"));
    $ADMIN->add('mdk_tools', new admin_externalpage('mdk_system',
    		"CMS",
    		new moodle_url('/local/mdk/tools/system.php')));
    $ADMIN->add('mdk_tools', new admin_externalpage('mdk_sql',
    		"SQL",
    		new moodle_url('/local/mdk/tools/sql.php')));
    
    
    $ADMIN->add('mdk', new admin_externalpage('mdk_fast',
    		"Fast commands",
    		new moodle_url('/local/mdk/fast.php')));
    $ADMIN->add('mdk', new admin_externalpage('mdk_editor',
    		"Theme Editor",
    		new moodle_url('/local/mdk/theme_editor.php')));
}
