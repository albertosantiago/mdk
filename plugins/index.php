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

mdk_plugins_set_tabs("INSTALL_UPGRADE");
?>
<script type="text/javascript">

	function displayForm(){
		$("#splitForm").css("display","none");
		$("#normalForm").css("display","block");
	}

	function displaySplitForm(){
		$("#normalForm").css("display","none");
		$("#splitForm").css("display","block");
	}

	function addFile(){
		$("#splitFiles").append('<br/><input type="file" name="plugin[]"/>');
	}
	
</script>
<form name="form" id="normalForm" action="index.php" method="POST" enctype="multipart/form-data">
	<label for="type">Tipo de plugin:</label>
	<select name="type">
		<option value="1">Theme</option>
		<option value="2">Local</option>
		<option value="2">Block</option>
	</select>
	&nbsp;&nbsp;&nbsp;&nbsp;
	<input type="file" name="plugin">
	<input type="submit" value="Update/Install plugin" />
	<a href="#" style="float:right;" onclick="displaySplitForm();">+ Upload Split Plugin</a>
</form>
<form name="splitForm" id="splitForm" action="index.php" method="POST" enctype="multipart/form-data" style="display:none;">
	<input type="hidden" name="split" value="1" />
	<label for="type">Tipo de plugin:</label>
	<select name="type">
		<option value="1">Theme</option>
		<option value="2">Local</option>
	</select>
	<br/><br/>
	<div id="splitFiles">
		<input type="file" name="plugin[]">
	</div>
	<a href="#" style="float:left;" onclick="addFile();">+ Add file</a>
	<br/><br/><br/>
	<input type="submit" value="Update/Install plugin" />
	<a href="#" style="float:right;" onclick="displayForm();">+ Upload Normal Plugin</a>
</form>
<br/><br/><br/>
<div style="width:100%;overflow:scroll;height:600px;display:block;border:1px solid #ccc">
<pre>
<?php 
	if(!empty($_POST)){
		$installer = new Mdk_Install();
		switch($_POST['type']){
			case 1:
				$installer->installTheme();
				break;
			case 2:
				$installer->installLocal();
				break;
		}
		$installer->clear();
	}
?>
</pre>
</div>
<?php 
echo $OUTPUT->footer();