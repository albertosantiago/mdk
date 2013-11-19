<?php
require_once(dirname(__FILE__) . '/../../config.php');
require_once($CFG->libdir.'/adminlib.php');
require_once($CFG->libdir.'/pluginlib.php');

$context = context_system::instance();

require_login();
require_capability('moodle/site:config', $context);

if(!empty($_GET["action"])){
	switch($_GET['action']){
		case 1:
			purge_all_caches();
			echo "OK";
			break;
	}
	die;
}

$PAGE->set_url('/local/mdk/index.php');
$PAGE->set_context($context);
$PAGE->set_title("Moodle Development Kit");
$PAGE->set_heading("Moodle Development Kit");
$PAGE->requires->js("/vendors/jquery/jquery-1.9.1.min.js");

echo $OUTPUT->header();
echo $OUTPUT->heading("");

mdk_set_tabs("FAST");
?>
<div id="actionsContainer" style="width:300px;">
	<input type="button" onclick="exec(1)" value="Purge cache" />
	<div id="wait1" style="float:right"></div>
</div>

<script type="text/javascript">

	function exec(action){
		$("#wait"+action).html("<img src='pix/spinner.gif' style='width:35px' />");
		$.ajax({
				url:	 "fast.php?action="+action,
				success: function(success){
					$("#wait"+action).html("El comando se ejecuto con exito");
				}	
			}
		);
	}
	
</script>

<?php 
echo $OUTPUT->footer();