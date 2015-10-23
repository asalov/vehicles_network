<?php

class VehicleModel extends Model{
	private $api;

	public function init(){
		$this->api = new VehiclesAPI;

		return $this;
	}

	public function getVehicles(){
		return $this->api->get('vehicle')->data('idvehicle');
	}

	// information about their usage (times, user assigned, fuel consumption, reported issues)
	// get all vehicles + info
	// Vehicle -> Logs -> Status_type, Manual_Issues? + more?
	// $status = $this->api->get('status_type', $log->Sensor_Sensor_type_idSensor_type)->data('idStatus_type');
	// $data['status'] = getStr($status->name);
	
	public function getVehicleInfo($modelId, $organizationId){
		$model = $this->api->get('vehicle_model', $modelId)->data('idVehicle_model');
		$organization = $this->api->get('organization', $organizationId)->data('idOrganization');

		$data = [];

		$data['model'] = getStr($model->name);
		$data['organization'] = getStr($organization->name);

		return $data;
	}

	// Get sensor data
	public function getSensorData($vehicleId){
		$sensors = $this->api->get('sensor')->data();
		
		$data = [];

		foreach($sensors as $sensor){
			if(getStr($sensor->Vehicle_idvehicle) === $vehicleId){ // Fix this wierd shit
				$sensorType = $this->api->get('sensor_type', $sensor->Sensor_type_idSensor_type)->data('idSensor_type');
				array_push($data, ['name' => getStr($sensorType->name)]);
			}			
		}

		return $data;
	}

	// Get vehicle usage data
	public function getUsageData($vehicleId){
		$usages = $this->api->get('bitacora')->data();
		
		$data = [];

		foreach($usages as $usage){
			// Would it work if there were more than one bitacorasas??
			if(getStr($usage->Vehicle_idvehicle) === $vehicleId){ // Fix this wierd shit

				// Get user
				$user = $this->getVehicleUser(getStr($usage->User_idUser));

				array_push($data, [
					'start_time' => getStr($usage->start_time),
					'end_time' => getStr($usage->end_time),
					'user' => $user->first_name . ' ' . $user->last_name
				]);
			}	
		}

		return $data;
	}

	// Get vehicle log info
	public function getLogData($vehicleId){
		$logs = $this->api->getLog('idvehicle', $vehicleId)->data();

		// Return nothing if no results
		if(count($logs->children()) === 0) return [];
		
		$data = [];

		foreach($logs as $log){
			array_push($data, [
				'sensor' => getStr($log->sensorTypeName),
				'link' => $this->api->getHostUrl() . getStr($log->logname)
			]);
		}
		
		return $data;
	}

	public function getVehicleUser($id){
		$q = $this->db->select('users', ['first_name', 'last_name'], ['external_id' => $id]);

		return $q->first();
	}

	public function getLastDrivenPath($vehicleId){
		$logData = $this->api->getLog('idvehicle', $vehicleId)->data();

		$link = '';

		foreach($logData as $log){
			if(getStr($log->sensorTypeName) === 'GPS') $link = $this->api->getHostUrl() . getStr($log->logname);
		}

		return file_get_contents($link);
	}

	public function getAllLogs($urlArr){
		$content = '';

		foreach($urlArr as $dest){
			$content .= file_get_contents($dest);
		}

		return $content;
	}
}