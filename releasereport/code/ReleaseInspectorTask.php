<?php

class ReleaseInspectorTask extends CliController{

	protected $log = array();

	protected $quiet = false;

	function index(){
		exec("git log --pretty=tformat:'%H'", $commits);
		if (is_array($commits)){
			$sha = $commits[0];
			if (preg_match('/^[0-9a-f]{40}$/', $sha) && !DataObject::get_one('Release','"Release"."SHA" = \''.$sha.'\'')){
				$release = new Release(array('SHA'=>$sha));
				$release->write();
				$this->log[] = 'Detected new release '.$sha;
			}
		}
		$this->showLog();
	}

	function showLog(){
		if ($this->quiet) return;
		foreach ($this->log as $logLine){
			echo $logLine.PHP_EOL;
		}
	}
}