<?php
require_once(dirname(__FILE__) . '/../../config.php');
require_once('lib.php');
require_once($CFG->libdir.'/adminlib.php');
require_once($CFG->libdir.'/pluginlib.php');

$context = context_system::instance();
$PAGE->set_context($context);
$PAGE->set_pagelayout("clear");

require_login();
require_capability('moodle/site:config', $context);

echo $OUTPUT->header(); // send headers

if(!empty($_GET["action"])){
	switch($_GET['action']){
		case 1:
			purge_all_caches();
			echo "OK";
			break;
		case 2:
			if($MDK->fast_command_reload_plugins){
				mdk_reload_plugins($MDK->reload_plugins);
				echo "OK";
			}else{
				echo "KO";
			}
			break;
	}
	die;
}

