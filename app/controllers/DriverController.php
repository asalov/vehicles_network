<?php

class DriverController extends RoleController{
	
	public function __construct(){
		parent::__construct();
	}

	public function index($resultsPageToken = null){
		$this->view->set('recommendations', $this->userModel->showVideoRecommendations($this->userId, $resultsPageToken));

		$this->view->render('home/recommendations');
	}

	public function map(){
		$this->view->set('showMap', true);
		$this->view->set('showVisualization', true);

		$this->view->render('home/map');
	}

	public function getGPSData(){
		// Get log file for user assigned vehicle
		// Get last log of type
		$vehicleId = getStr($this->userModel->getAssignedVehicle($this->userId)->Vehicle_idvehicle);

		echo $this->vehicleModel->getLastDrivenPath($vehicleId);		
	}
}