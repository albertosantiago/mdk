<?php

function mdk_set_tools_tabs($currenttab){
	global $USER,$CFG;
	$context = context_system::instance(0);

	$tabs = array();
	$row = array();
	$row[] = new tabobject('CMD', $CFG->wwwroot.'/local/mdk/tools/system.php', "CMD");
	$row[] = new tabobject('SQL', $CFG->wwwroot.'/local/mdk/tools/sql.php', "SQL");
	$tabs[] = $row;
	print_tabs($tabs, $currenttab);
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
