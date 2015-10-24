<?php

class AnalystController extends RoleController{
	
	public function __construct(){
		parent::__construct();
	}

	public function index(){
		$this->view->set('vehicles', $this->vehicleModel->getVehicles());

		$this->view->render('home/usage');			
	}

	public function logs($vehicleId){
		$this->view->set('showDatepicker', true);
		$this->view->set('showVisualization', true);

		if($this->authModel->checkPermissions('add_notes')) $this->view->set('addNotes', true);

		// Show vehicle plate + model?

		$this->view->set('vehicleId', $vehicleId);
		$this->view->set('sensors', $this->vehicleModel->getSensorData($vehicleId));

		$this->view->render('home/logs');
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

	public function getLogs(){
		echo toJson($this->vehicleModel->getLogData($_POST));
	}

	public function getAnnotations($vehicleId){
		echo toJson($this->vehicleModel->getVehicleAnnotations($vehicleId, $this->authModel->getUserData('id')));
	}

	public function addAnnotation(){
		if($this->authModel->checkPermissions('add_notes')){
			$vehicleId = $_POST['vehicle_id'];
			$text = $_POST['text'];

			echo toJson($this->vehicleModel->addVehicleAnnotation($this->authModel->getUserData('id'), $vehicleId, $text));			
		}
	}

	public function deleteAnnotation($annotationId){
		$userLocalId = $this->authModel->getUserData('id');

		if($this->authModel->checkPermissions('add_notes') && $this->vehicleModel->hasAnnotation($userLocalId, $annotationId)){
			echo $this->vehicleModel->deleteVehicleAnnotation($userLocalId, $annotationId);
		}

		echo false;
	}
}