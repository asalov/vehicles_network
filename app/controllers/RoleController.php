<?php

class RoleController extends Controller{
	private $authModel;
	private $vehicleModel;
	private $userModel;
	private $userId;

	public function __construct(){
		parent::__construct();

		$this->authModel = $this->loadModel('auth')->init();

		if(!$this->authModel->isLoggedIn()) redirect(PATH . 'login');

		$this->vehicleModel = $this->loadModel('vehicle')->init();
		$this->userModel = $this->loadModel('user')->init();

		$this->userId = $this->authModel->getUserData('external_id');

		$this->view->set('userLogged', true);
	}

	public function driver($resultsPageToken = null){
		$this->view->set('recommendations', $this->userModel->showVideoRecommendations($this->userId, $resultsPageToken));

		$this->view->render('home/recommendations');
	}

	public function analyst(){
		$this->view->set('vehicles', $this->vehicleModel->getVehicles());

		$this->view->render('home/usage');	
	}

	public function director(){
		if($this->authModel->checkPermissions('access_stock_data')){
			$this->view->set('stockName', $this->userModel->getCompanyStockName($this->userId));
			$this->view->set('accessGranted', true);
		}

		$this->view->render('home/stock');	
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
}