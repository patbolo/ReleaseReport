<?php

class ProjectManagementConnectorManager{

	private $projectManagementSystems=array();

	private static $instance;

	public function register($name, IProjectManagementSystem $connector){
		$this->projectManagementSystems[$name] = $connector;
	}

	public function unregister($name){
		unset($this->projectManagementSystems[$name]);
	}

	public static function get(){
		if (!isset(self::$instance)) self::$instance = new ProjectManagementConnectorManager();
		return self::$instance;
	}

	public function getConnectors(){
		return $this->projectManagementSystems;
	}
}