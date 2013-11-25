<?php
require_once(dirname(__FILE__) . '/../../../config.php');
require_once('lib.php');
require_once($CFG->libdir.'/adminlib.php');
require_once($CFG->libdir.'/pluginlib.php');

$context = context_system::instance();

require_login();
require_capability('moodle/site:config', $context);

$PAGE->set_url('/local/mdk/plugins/index.php');
$PAGE->set_context($context);
$PAGE->set_title("Moodle Development Kit");
$PAGE->set_heading("Moodle Development Kit");
$PAGE->requires->js("/vendors/jquery/jquery-1.9.1.min.js");

echo $OUTPUT->header();
echo $OUTPUT->heading("Plugins Manager");

mdk_plugins_set_tabs("CREATE");
?>
<form name="form" id="normalForm" action="index.php" method="POST" enctype="multipart/form-data">
	<label for="type">Tipo de plugin:</label>
	<select name="type">
		<option value="1">Theme</option>
		<option value="2">Local</option>
		<option value="2">Block</option>
	</select>
	&nbsp;&nbsp;
	<label for="name">Nombre:</label>
	<input type="text" name="name" />
	&nbsp;&nbsp;
	<input type="submit" value="Create" />
</form>
<br/><br/><br/>
<div style="width:100%;overflow:scroll;height:600px;display:block;border:1px solid #ccc">
<pre>
<?php 
	$plugin = new MDK_Plugin();
	$plugin->setName("test");
	$plugin->setType("theme");
	$plugin->generate();
	
?>
</pre>
</div>
<?php 
echo $OUTPUT->footer();