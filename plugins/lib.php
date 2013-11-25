<?php

function mdk_reload_plugins($plugins, $verbose=true){
	foreach($plugins as $plugin){
		list($pluginType, $pluginName) = explode("_",$plugin);
		uninstall_plugin($pluginType, $pluginName);
	}
	upgrade_noncore($verbose);
}


function mdk_plugins_set_tabs($currenttab){
	global $USER,$CFG;
	$context = context_system::instance(0);

	$tabs = array();
	$row = array();
	$row[] = new tabobject('INSTALL_UPGRADE', $CFG->wwwroot.'/local/mdk/plugins/index.php', "Install/Update");
	$row[] = new tabobject('RELOAD', $CFG->wwwroot.'/local/mdk/plugins/reload.php', "Reinstall");
	$row[] = new tabobject('CREATE', $CFG->wwwroot.'/local/mdk/plugins/create.php', "Create");
	
	$tabs[] = $row;
	print_tabs($tabs, $currenttab);
}

class Mdk_Plugin{
	
	public $templatedir;
	private $name;
	private $type;
	const REPLACEMENT_STRING = "##__PLUGIN_NAME__##";
	
	public function __construct(){
		global $CFG;
		$this->templatedir = $CFG->dirroot."/local/mdk/plugins/templates/";
	}
	
	public function setName($name){
		$this->name = $name;
	}
	
	public function setType($type){
		$this->type = $type;
	}
	
	public function generate(){
		$pluginPath = $this->getPluginPath();
		$this->copy();
		$this->renameFiles($pluginPath);
		$this->renameContents($pluginPath);
	}
	
	public function renameFiles($src){
		$dir = opendir($src);
		while(false !== ( $file = readdir($dir)) ) {
			if (( $file != '.' ) && ( $file != '..' )) {
				if ( is_dir($src . '/' . $file) ) {
					$this->renameFiles($src.'/'.$file);
				}
				else {
					$fileName = $src.'/'.$file;
					if(strrpos($fileName,self::REPLACEMENT_STRING)!==false){
						$newFileName = str_replace(self::REPLACEMENT_STRING,$this->name, $fileName);
						copy($fileName, $newFileName);
						unlink($fileName);
					}
				}
			}
		}
		closedir($dir);
		return true;
	}
	
	public function renameContents($src){
		$dir = opendir($src);
		while(false !== ( $file = readdir($dir)) ) {
			if (( $file != '.' ) && ( $file != '..' )) {
				if ( is_dir($src . '/' . $file) ) {
					$this->renameContents($src.'/'.$file);
				}
				else {
					$fileName = $src.'/'.$file;
					$content = file_get_contents($fileName);
					$content = str_replace(self::REPLACEMENT_STRING, $this->name, $content);
					file_put_contents($fileName, $content);
				}
			}
		}
		closedir($dir);
		return true;
	}
	
	private function copy(){
		$path = $this->getPluginPath();
		if(is_dir($path)){
			mdk_rrmdir($path);
		}
		mdk_dir_copy($this->templatedir.$this->type, $this->getPluginPath());
	}
	
	private function getPluginPath(){
		global $CFG;
		return $CFG->dirroot."/".$this->type."/".$this->name;
	}
	
	
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