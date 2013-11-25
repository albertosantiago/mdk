<?php
require_once(dirname(__FILE__) . '/../../../config.php');
require_once('../lib.php');
require_once('lib.php');

$context = context_system::instance();

require_login();
require_capability('moodle/site:config', $context);

$pluginManager = plugin_manager::instance();

if(!empty($_POST['action'])){
	if($_POST['action']=="set_config"){
		$saveValues  = (isset($_POST['save_values'])) ? true:false;
		$fastCommand = (isset($_POST['fast_command_enable']))?true:false;
		set_config("save_values", $saveValues,"local_mdk");
		set_config("fast_command_reload_plugins", $fastCommand,"local_mdk");
		redirect("reload_plugins.php","La configuración se guardo con exito");
	}
	if($_POST['action']=="reinstall"){
		if($MDK->save_values){
			set_config("reload_plugins", serialize($_POST['plugins']),"local_mdk");
		}
	}
}

get_string("MIERDAS");

$PAGE->set_url('/local/mdk/plugins/reload.php');
$PAGE->set_context($context);
$PAGE->set_title("Moodle Development Kit");
$PAGE->set_heading("Moodle Development Kit");
$PAGE->requires->js("/vendors/jquery/jquery-1.9.1.min.js");

echo $OUTPUT->header();
echo $OUTPUT->heading("Plugins Manager");

mdk_plugins_set_tabs("RELOAD");
?>
<div>
<?php 
if((!empty($_POST['action']))&&($_POST['action']=="reinstall")){
?>
<input type="button" onclick="window.location='reload.php'" style="float:right" value="Volver"/>
<br/><br/>
<div id="shell" style="width:100%;overflow:scroll;height:300px;display:block;border:1px solid #ccc;background:#fafafb;color:#000;">
<pre>
<?php 
mdk_reload_plugins($_POST['plugins']);
?>
</pre></div>
<br/>
<input type="button" onclick="window.location='reload.php'" style="float:right" value="Volver"/>
<?php 
}else{?>
<div style="float:left">
	<form name="reload_plugins" action="" method="POST" >
	<input type="hidden" name="action" value="reinstall" />
	<input type="submit" value="Reinstallar" />
	<br/><br/>
	<div style="text-align:right;">
		<span style="padding:15px;">Marcar todos</span><input type="checkbox" id="set_all" style="display:block;float:right;margin-top:2px;margin-right:30px;">
	</div>
	<br/>
	<div id="pluginList">
	<?php 
		$pluginTree = $pluginManager->get_plugins(true);
		foreach($pluginTree as $pluginType => $plugins){
			if($pluginType=="theme") continue;
			
			$html = "";
			foreach($plugins as $plugin){
				if($plugin->source != "std"){
					$checked = "";
					$pluginName = $pluginType."_".$plugin->name;
					if($MDK->save_values){
						if(isset($MDK->reload_plugins)){
							if(in_array($pluginName, $MDK->reload_plugins)){
								$checked = "checked='checked'";
							}
						}
					}
					$html .= "<li style='border-bottom:1px solid #ccc;width:320px;padding:10px;'><span style='width:250px;display:block;float:left;clear:left;'>$plugin->displayname</span><input type='checkbox' value='$pluginName' name=\"plugins[]\" $checked /></li>";
				}		
			}
			if(!empty($html)){
				$html = "<h4>".$pluginManager->plugintype_name_plural($pluginType)."</h4><ul>$html</ul>";
			}
			echo $html;
		}
	?>
	</div>
	<br/><br/>
	<input type="submit" value="Reinstallar" />
	</form>
</div>
<div style="float:right;margin-left:20px;background:#fafafa;border:2px solid #ccc;width:40%;padding:20px;">
	<form name="reload_plugins" action="" method="POST">
		<input type="hidden" name="action" value="set_config" />
		<h4>Configuración</h4>
		<br/>
		<label for="save">Salvar configuración</label>
		<input type="checkbox" name="save_values" <?php echo ($MDK->save_values) ? 'checked="checked"' : ''; ?>/>
		<br/><br/>
		<label for="save">Habilitar Fast Command</label>
		<input type="checkbox" name="fast_command_enable" <?php echo ($MDK->fast_command_reload_plugins) ? 'checked="checked"':''; ?>/>
		<br/>
		<input type="submit" value="Salvar Configuración" style="float:right;margin-top:20px;"/>
	</form>
</div>
<?php 
}
?>
</div>
<?php 
echo $OUTPUT->footer();
?>
<script type="text/javascript">

window.onload = function(){
	$("#set_all").click(function(){
		$("#pluginList").find("input[type='checkbox']")
				.prop("checked",$(this).prop("checked"));
	});
}
</script>