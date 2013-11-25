<?php

defined('MOODLE_INTERNAL') || die;

if ($ADMIN->fulltree) {


// Tagline setting
    $name = 'theme_##__PLUGIN_NAME__##/tagline';
    $title = get_string('tagline','theme_##__PLUGIN_NAME__##');
    $description = get_string('taglinedesc', 'theme_##__PLUGIN_NAME__##');
    $setting = new admin_setting_configtext($name, $title, $description, '');
    $settings->add($setting);
    
    // footerline setting
    $name = 'theme_##__PLUGIN_NAME__##/footerline';
    $title = get_string('footerline','theme_##__PLUGIN_NAME__##');
    $description = get_string('footerlinedesc', 'theme_##__PLUGIN_NAME__##');
    $setting = new admin_setting_configtext($name, $title, $description, '');
    $settings->add($setting);


	// Background color setting
	$name = 'theme_##__PLUGIN_NAME__##/backgroundcolor';
	$title = get_string('backgroundcolor','theme_##__PLUGIN_NAME__##');
	$description = get_string('backgroundcolordesc', 'theme_##__PLUGIN_NAME__##');
	$default = '#454545';
	$previewconfig = NULL;
	$setting = new admin_setting_configcolourpicker($name, $title, $description, $default, $previewconfig);
	$settings->add($setting);

	// link color setting
	$name = 'theme_##__PLUGIN_NAME__##/linkcolor';
	$title = get_string('linkcolor','theme_##__PLUGIN_NAME__##');
	$description = get_string('linkcolordesc', 'theme_##__PLUGIN_NAME__##');
	$default = '#2a65b1';
	$previewconfig = NULL;
	$setting = new admin_setting_configcolourpicker($name, $title, $description, $default, $previewconfig);
	$settings->add($setting);

	// link hover color setting
	$name = 'theme_##__PLUGIN_NAME__##/linkhover';
	$title = get_string('linkhover','theme_##__PLUGIN_NAME__##');
	$description = get_string('linkhoverdesc', 'theme_##__PLUGIN_NAME__##');
	$default = '#222222';
	$previewconfig = NULL;
	$setting = new admin_setting_configcolourpicker($name, $title, $description, $default, $previewconfig);
	$settings->add($setting);



}