<?php
require_once($CFG->libdir.'/adminlib.php');
require_once($CFG->libdir.'/pluginlib.php');
require_once($CFG->libdir.'/upgradelib.php');
require_once('debug/lib.php');

$MDK = get_config("local_mdk");
if(!empty($MDK->reload_plugins)){
	$MDK->reload_plugins = unserialize($MDK->reload_plugins);
}


function local_mdk_extends_navigation ($nav) {
	mdk_debug_set_page_settings();
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

function mdk_dir_copy($src, $dst) {
    $dir = opendir($src);
    @mkdir($dst);
    while(false !== ( $file = readdir($dir)) ) {
        if (( $file != '.' ) && ( $file != '..' )) {
            if ( is_dir($src . '/' . $file) ) {
                mdk_dir_copy($src . '/' . $file,$dst . '/' . $file);
            }
            else {
                copy($src . '/' . $file,$dst . '/' . $file);
            }
        }
    }
    closedir($dir);
} 

function mdk_display_errors($error){
	echo "<div style='margin-bottom:20px;font-weight:bold;color:#ff0000;background:#ffeeee;padding:10px;border:2px solid #ff0000'>$error</div>";
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

class Mdk_Object{}
