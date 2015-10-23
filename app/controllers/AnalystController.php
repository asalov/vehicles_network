<?php

class AnalystController extends RoleController{
	
	public function __construct(){
		parent::__construct();
	}

	public function index(){
		$this->view->set('showVisualization', true);
		$this->view->set('vehicles', $this->vehicleModel->getVehicles());

		$this->view->render('home/usage');			
	}

	public function getExtraInfo($modelId, $organizationId){
		echo toJson($this->vehicleModel->getVehicleInfo($modelId, $organizationId));
	}

	public function getSensors($vehicleId){
		echo toJson($this->vehicleModel->getSensorData($vehicleId));
	}

	public function getUsage($vehicleId){
		echo toJson($this->vehicleModel->getUsageData($vehicleId));
	}

	public function getLogs($vehicleId){
		echo toJson($this->vehicleModel->getLogData($vehicleId));
	}

	public function getLogContents(){
		echo $this->vehicleModel->getAllLogs($_POST['links']);
	}	
}