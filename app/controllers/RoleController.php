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

	public function index(){
		$this->view->render('home/index');
	}

	public function driver(){
		// DRIVER
		// if the user is assigned a heavy vehicle driver role, then this user gets
		// recommendations to YouTube videos regarding the type of heavy vehicle that the driver has been assigned
	
		$this->view->set('recommendations', $this->userModel->showVideoRecommendations($this->userId));

		$this->view->render('home/recommendations');
	}

	public function analyst(){
		// ANALYST
		// if the user is assigned an analyst role, then the user gets a list of the vehicles
		// and information about their usage (times, user assigned, fuel consumption, reported issues)
		// get all vehicles + info
		// Vehicle -> Logs -> Status_type, Manual_Issues? + more?

		$this->view->set('vehicles', $this->vehicleModel->getVehicles());

		$this->view->render('home/usage');	
	}

	public function director(){
		// DIRECTOR
		// if the user is assigned a partner or director role, the user can get the
		// historical stock market value of his/her company in a CSV file.
		// get director company + stock market API

		if($this->authModel->checkPermissions('access_stock_data')){
			$this->view->set('stockData', $this->userModel->getStockData($this->userId));
		}else{
			$this->view->set('permissionRestriction', true);
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