<?php
require_once(dirname(__FILE__) . '/../../../config.php');
require_once($CFG->libdir.'/adminlib.php');
require_once($CFG->libdir.'/pluginlib.php');

$context = context_system::instance();

require_login();
require_capability('moodle/site:config', $context);

$PAGE->set_url('/local/mdk/tools/system.php');
$PAGE->set_context($context);
$PAGE->set_title("Moodle Developer Kit");
$PAGE->set_heading("Moodle Developer Kit");
$PAGE->requires->js("/vendors/jquery/jquery-1.9.1.min.js");

echo $OUTPUT->header();
echo $OUTPUT->heading("");

$shell = new Mdk_Shell();

if(!empty($_POST)){
	try{
		$shell->exec($_POST['cmd']);	
	}catch(Exception $e){
		$error = $e->getMessage();
	}
}

mdk_set_tools_tabs("CMD");

if(isset($error)){
	mdk_display_errors($error);
}

?>
<br/>
<a id="sh"></a> 
<div id="shell" style="width:100%;overflow:scroll;height:300px;display:block;border:1px solid #ccc;background:#000;color:#fff;">
<?php 
echo $shell->displayResults();
?>
</div>
<br/><br/>
<form name="myform" action="system.php#sh" method="POST">
	<input type="text" name="cmd" id="cmd" style="width:600px">
	<input type="submit" value="Ejecutar comando">
</form>
<br/>
<a href="#" style="float:right;" onclick="event.preventDefault();showHistory();">+ Show History</a>
<div id="commandHistory" style="display:none">
<ol>
<?php 
	$commands = $shell->getCommandHistory();
	foreach($commands as $command){
		echo "<li><a href='#' onclick='event.preventDefault();setCommand(\"$command\");return false;'>$command</span></li>";	
	}
?>
</ol>
</div>
<?php 
echo $OUTPUT->footer();
?>
<script type="text/javascript">
$(function(){
	$("#shell").scrollTop(600000000);
});

function showHistory(){
	$("#commandHistory").css("display","block");
}

function setCommand(cmd){
	$("#cmd").val(cmd);
}
</script>