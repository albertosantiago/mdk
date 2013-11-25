<?php
if (!defined('AJAX_SCRIPT')) {
    define('AJAX_SCRIPT', true);
}
require_once(dirname(__FILE__) . '/../../../config.php');
require_once('lib.php');
?>
<style>
#mdk_debugbar{
	display:none;
	position:fixed;
	top:0px;
	left:0px;
	width:800px;
	height:100%;
	background:#fafafb;
	border-left:2px solid #AAA;
	font-family:Trebuchet ms;
	z-index:1000;
}

#mdk_debugbar_tab{
	display:block;
	position:fixed;
	top:0px;
	left:0px;
	width:80px;
	height:15px;
	background:#000;
	border-left:2px solid #666;
	color:#fff;
	font-weight:bold;
	padding:2px;
}

#mdk_debugbar_content{
	padding-left:10px;
	font-size:10px;
	overflow:scroll;
	height:90%;
}

#mdk_debugbar h2{
	color:#fff;
	background:#000;
	width:97%;
	display:block;
	padding-left:1%;
	padding-right:2%;
	padding-bottom:5px;
	padding-top:5px;
}

#mdk_debugbar h4{
	color:#fff;
	background:#000;
	width:95%;
	display:block;
	padding-left:1%;
	padding-right:2%;
	padding-bottom:5px;
	padding-top:5px;
}

a.mdk_debug_toolbar_icons,a.mdk_debug_toolbar_icons:visited{
	float:right;
	color:#fff;
	font-size:21px;
	font-weight:bold;
	padding:2px;
	border:1px solid #fff;
	line-height:8px;
	padding-bottom:4px;
	padding-top:0px"
}

div.mdk_debug_group ul{
	display:none;
}
</style>
<div id="mdk_debugbar_tab">
	<a href="#" style="color:#fff" onclick="event.preventDefault();Mdk_Debugbar.setVisible(true)">+ DEBUG INFO</a>	
</div>
<div id="mdk_debugbar">
	<h2>MDK Debugbar <a class="mdk_debug_toolbar_icons" href="#" onclick="event.preventDefault();Mdk_Debugbar.setVisible(false)">-</a></h2>
	<div id="mdk_debugbar_content">
		<pre style="margin:0px;">
		<?php 
		
		function paintGroup($data, $title){
			$id = "mdk_".time()."_".md5($title);
			if(($key=="_post")||($key=="_post")||$key=="_files"){
				$style = "style='display:block'";
			}
			$extraHeader = "<span>0</span>";
			if(!empty($data)){
				$extraHeader = "<a style='margin-top:-16px;'  class='mdk_debug_toolbar_icons'  href='#' onclick='event.preventDefault();Mdk_Debugbar.collapseGroup(\"$id\")'>+</a>";
			}
			echo "<div class='mdk_debug_group'><h4>$title $extraHeader </h4><ul id='$id' >";
			foreach($data as $key => $value){
				echo "<li>";
				echo $key;
				echo ":";
				echo $value;
				echo "</li>";
			}				
			echo "</ul></div>";
		}
		
		$info = Mdk_DebugBar::getDebugInfo();
		foreach($info as $key => $value){
			paintGroup($value, $key);	
		}
		?>
		</pre>
	</div>
</div>