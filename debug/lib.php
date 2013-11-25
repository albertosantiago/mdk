<?php
/**
 * PARA INICIAR Y MANEJAR - LA BARRA DE DEPURACIÓN
 */
$CFG->debugdisplay = false;

function mdk_exception_handler($exception){
	var_dump($exception->getMessage());
}

function mdk_error_handler($errno,$errstr,$errfile,$errline){
	$debugBar = Mdk_Debugbar::getInstance();
	$error = array("errno"=>$errno,"errstr"=>$errstr,"errfile"=>$errfile,"errline"=>$errline);
	$debugBar->add($error);
}

set_exception_handler("mdk_exception_handler");
set_error_handler("mdk_error_handler");

function mdk_debug_set_page_settings(){
	global $PAGE;
	$PAGE->requires->js(new moodle_url('/vendors/jquery/jquery-1.9.1.min.js'));
	$PAGE->requires->js(new moodle_url('/local/mdk/debug/debugbar.js'));
	$PAGE->requires->js_init_call("Mdk_Debugbar.init");
}

class Mdk_DebugBar{
	
	private static $instance;
	
	private function __construct(){}
	
	public static function getInstance(){
		global $CFG, $USER;
		if(empty(self::$instance)){
			self::$instance = new Mdk_DebugBar();
		}
		$_SESSION['mdk_debug'] = array();
		$_SESSION['mdk_debug']['_files']= $_FILES;
		$_SESSION['mdk_debug']['_get']=$_GET;
		$_SESSION['mdk_debug']['_post']=$_POST;
		$_SESSION['mdk_debug']['_server']=$_SERVER;
		$_SESSION['mdk_debug']['cfg']=$CFG;
		$_SESSION['mdk_debug']['user']=$USER;
		return self::$instance;
	}
	
	public function add($error){
	if(!isset($_SESSION['mdk_debug']['errors'])){
			$_SESSION['mdk_debug']['errors'] = array();
		}
		if(!isset($_SESSION['mdk_debug']['errors'][$error['errno']])){
			$_SESSION['mdk_debug']['errors'][$error['errno']] = array();
		}
		$_SESSION['mdk_debug']['errors'][$error['errno']][] = $error;
	}
	
	public function getErrors(){
		return $_SESSION['mdk_debug']['errors'];
	}
	
	public static function getDebugInfo(){
		return $_SESSION['mdk_debug'];
	}
	
	public function __destruct(){}
}