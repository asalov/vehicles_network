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

	public function logs($vehicleId){
		$this->view->set('showDatepicker', true);
		$this->view->set('showVisualization', true);

		// MOVE TO MODEL
		$allLogs = $this->vehicleModel->getLogData($vehicleId);
		$logs = [];
		$types = ['temp', 'speed', 'weight'];

		foreach($allLogs as $log){
			$sensor = $log['sensor'];

			if($sensor !== 'GPS' && $sensor !== 'Timer'){
				$sensor = strtolower($sensor);

				foreach($types as $type){
					if(strpos($sensor, $type) !== false){
						$log['type'] = $type;
						break;
					}
				}

				array_push($logs, $log);
			}
		}

		$this->view->set('logs', $logs);

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

	public function getLogs($vehicleId){
		echo toJson($this->vehicleModel->getLogData($vehicleId));
	}

	public function getLogContents(){
		echo toJson($this->vehicleModel->getAllLogs($_POST['logs']));

		// $logs = [
		// 	'EngineWaterTemperature' => 'http://4me302-ht15.host22.com/veh17_EngineWaterTemp.log',
		// 	'HydraulicOilTemperature' => 'http://4me302-ht15.host22.com/veh17_Hydrualoljetemp.log',
		// 	'TransmissionTemperatureConv' => 'http://4me302-ht15.host22.com/veh17_Transmission_conv_temp.log'
		// ];
		// echo toJson($this->vehicleModel->getAllLogs($logs));
		// $this->vehicleModel->getAllLogs(['http://4me302-ht15.host22.com/veh17_EngineWaterTemp.log']);
	}

	public function getAnnotations($logId){
		echo toJson($this->vehicleModel->getLogAnnotations($logId));
	}

	public function saveAnnotation(){
		$logId = $_POST['log_id'];
		$text = $_POST['text'];

		$this->vehicleModel->saveLogAnnotation($this->userId, $logId, $text);
	}
}