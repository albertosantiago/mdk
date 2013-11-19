<?php


function mdk_set_tabs($currenttab){
	global $USER,$CFG;
	$context = context_system::instance(0);

	$tabs = array();
	$row = array();
	$row[] = new tabobject('PLUGINS', $CFG->wwwroot.'/local/mdk/index.php', "Install/Update plugins");
	$row[] = new tabobject('CMD', $CFG->wwwroot.'/local/mdk/system.php', "CMD");
	$row[] = new tabobject('SQL', $CFG->wwwroot.'/local/mdk/sql.php', "SQL");
	$row[] = new tabobject('FAST', $CFG->wwwroot.'/local/mdk/fast.php', "Fast Commands");
	
	$tabs[] = $row;
	print_tabs($tabs, $currenttab);
}

# recursively remove a directory
function mdk_rrmdir($path) {
	if (is_dir($path) === true)	{
		$files = new RecursiveIteratorIterator(
							new RecursiveDirectoryIterator($path), RecursiveIteratorIterator::CHILD_FIRST);
		foreach ($files as $file){
			if (in_array($file->getBasename(), array('.', '..')) !== true){
				if ($file->isDir() === true){
					rmdir($file->getPathName());
				}
				else if (($file->isFile() === true) 
						||($file->isLink() === true)){
					unlink($file->getPathname());
				}
			}
		}
		return rmdir($path);
	}else if ((is_file($path) === true) 
			||(is_link($path) === true)){
		return unlink($path);
	}
	return false;
}

function mdk_display_errors($error){
	echo "<div style='margin-bottom:20px;font-weight:bold;color:#ff0000;background:#ffeeee;padding:10px;border:2px solid #ff0000'>$error</div>";
}

class Mdk_Install{

	public $tmpdir;
	
	public function __construct(){
		$this->checkTmpDir();
	}
	
	function __destruct() {}
	
	public function installTheme(){
		global $CFG;
		$themedir = $CFG->dirroot."/theme/";
		try{
			$pluginName = $this->loadPlugin();
			$pluginDir  = $themedir.$pluginName;
			$pluginTmpDir = $this->tmpdir.$pluginName;
			if(is_dir($pluginDir)){
				echo "Actualizando plantilla $pluginName...\n";
				emdk_rrmdir($pluginDir);
			}
			echo "Moviendo ficheros temporales...\n";
			rename($pluginTmpDir,$pluginDir);
			echo "La plantilla ha sido actualizada con exito...\n";
			return true;
		}catch(Exception $e){
			print_r($e);
			return false;
		}
	}
	
	
	public function installLocal(){
		global $CFG;
		$localdir = $CFG->dirroot."/local/";
		try{
			$pluginName = $this->loadPlugin();
			$pluginDir  = $localdir.$pluginName;
			$pluginTmpDir = $this->tmpdir.$pluginName;
			if(is_dir($pluginDir)){
				echo "Desinstalando plugin local $pluginName...\n";
				uninstall_plugin('local', $pluginName);
				emdk_rrmdir($pluginDir);
			}
			echo "Moviendo ficheros temporales...\n";
			rename($pluginTmpDir,$pluginDir);
			echo "El plugin ha sido actualizada con exito...\n";
			return true;
		}catch(Exception $e){
			print_r($e);
			return false;
		}
	}
	
	/**
	 * FUNCIONES PRIVADAS
	 */
	
	private function checkTmpDir(){
		global $CFG;
		$this->tmpdir = $CFG->tempdir."/emdk/";
		if(!is_dir($this->tmpdir)){
			echo "Creando directorio temporal...\n";
			mkdir($this->tmpdir);
		}
	}
	
	private function loadPlugin(){
		global $CFG;
		echo "Moviendo plugin al directorio temporal...\n";
		$ret = $this->moveUploadedPlugin();
		if(!$ret){
			print_r($_FILES);
			throw new Exception("El plugin no se pudo cargar en el sistema");
		}
		echo "Descomprimiendo archivos...\n";
		try {
			$phar = new PharData("$this->tmpdir/plugin.tar.gz");
			$phar->extractTo($this->tmpdir, null, true);
		} catch (Exception $e) {
			print_r($e);
		}
		$files = scandir($this->tmpdir);
		/**
		 * Tiene que haber 4 ficheros en el directorio
		 * .
		 * ..
		 * $nombreFichero
		 * plugin.tar.gz
		*/
		if(sizeOf($files)!=4){
			throw new Exception("Formato de plugin invalido");
		}
		return $files[2];
	}
	
	private function moveUploadedPlugin(){
		if(!empty($_POST['split'])){
			$size = sizeOf($_FILES['plugin']['name']);
			for($i=0;$i<$size;$i++){
				move_uploaded_file($_FILES['plugin']['tmp_name'][$i], $this->tmpdir.$_FILES['plugin']['name'][$i]);
			}
			exec("cat ".$this->tmpdir."*.part-* > ".$this->tmpdir."/plugin.tar.gz");
			exec("rm ".$this->tmpdir."*.part-*");
			return true;
		}else{
			return move_uploaded_file($_FILES['plugin']['tmp_name'], "$this->tmpdir/plugin.tar.gz");
		}
	}
	
	public function clear(){
		global $CFG;
		echo "Purgando caches del sistema...\n";
		purge_all_caches();
		echo "Eliminando ficheros temporales...\n";
		mdk_rrmdir($this->tmpdir);
		echo "El proceso finalizo con exito.";
	}
}

class Mdk_History{
	
	private $id;
	
	public function __construct($id){
		$this->id = $id; 
		if(empty($_SESSION["mdk"])){
			$_SESSION['mdk'] = array();
		}
		if(empty($_SESSION["mdk"][$id])){
			$_SESSION['mdk'][$id] = array();
		}
	}

	public function save($key,$value){
		if(empty($_SESSION["mdk"][$this->id][$key])){
			$_SESSION['mdk'][$this->id][$key] = array();
		}
		$_SESSION['mdk'][$this->id][$key][] = $value;
		return true;
	}
	
	public function get($key){
		if(!empty($_SESSION['mdk'][$this->id][$key])){
			return $_SESSION['mdk'][$this->id][$key];
		}else{
			return array();
		}
	}
}

class Mdk_Shell{
	
	private $history;
	private $pwd;
	
	public function __construct(){
		$this->history = new Mdk_History("Shell");
		if(empty($_SESSION['mkd_shell_pwd'])){
			$_SESSION['mkd_shell_pwd'] = exec("pwd");
		}
		$this->pwd = $_SESSION['mkd_shell_pwd'];
	}
	
	public function exec($cmd){
		chdir($this->pwd);
		$cmd = trim($cmd);
		if(!$this->isSpecialCommand($cmd)){
			if(empty($cmd)){
				throw new Exception("Malformed Command");	
			}
			$ret = shell_exec($cmd);
			$this->history->save("commands",$cmd);
			$this->history->save("results",$ret);
			$this->history->save("pwd",$this->pwd);
		}else{
			$this->execSpecialCommand($cmd);
		}
	}
	
	public function execSpecialCommand($cmd){
		$cmdAux = $this->getCommand($cmd);
		switch($cmdAux){
			case "CD":
				$this->execCommandCD($cmd);
				break;
			case "CLEAR":
				$this->execCommandClear();
				break;
		}
		return true;
	}
	public function execCommandCD($cmd){
		$words = explode(" ",trim($cmd));
		if(sizeof($words)!=2){
			return false;
		}
		$ret = chdir($words[1]);
		$_SESSION['mkd_shell_pwd'] = getcwd();
		$this->history->save("commands", $cmd);
		$this->history->save("results", "");
		$this->history->save("pwd",$this->pwd);
		$this->pwd = getcwd();
	}
	
	public function execCommandClear(){
		$this->history->save("commands","clear");
		$this->history->save("results", "\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n");
		$this->history->save("pwd",$this->pwd);
	}
	
	public function isSpecialCommand($cmd){
		$cmd = $this->getCommand($cmd);
		if(in_array($cmd, array("CLEAR","CD"))){
			return true;
		}
		return false;
	}
	
	public function getCommand($cmd){
		$words = explode(" ",trim(strtoupper($cmd)));
		return trim($words[0]);
	}
	
	public function getCommandHistory(){
		return $this->history->get("commands");
	}
	
	public function displayResults(){
		echo "<pre>";
		$results  = $this->history->get("results");
		$commands = $this->history->get("commands");
		$pwd 	  = $this->history->get("pwd");
		$size = sizeof($results);
		for($i=0;$i<$size;$i++){
			echo $pwd[$i].">".$commands[$i]."\n"; 
			echo $results[$i]."\n"; 
		}
		echo $this->pwd.">";
		echo "</pre>";
	}		
}
