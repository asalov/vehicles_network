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
		$types = ['temp', 'speed', 'weight', 'gps', 'timer'];

		foreach($sensors as $sensor){
			if(getStr($sensor->Vehicle_idvehicle) === $vehicleId){
				$sensorType = $this->api->get('sensor_type', getStr($sensor->Sensor_type_idSensor_type))->data('idSensor_type');
				$name = getStr($sensorType->name);
				
				$sensorData = [
					'id' => getStr($sensor['id']),
					'name' => $name
				];

				foreach($types as $type){
					if(strpos(strtolower($name), $type) !== false){
						$sensorData['type'] = $type;
						break;
					}
				}

				array_push($data, $sensorData);
			}			
		}

		return $data;
	}

	// Get vehicle usage data
	public function getUsageData($vehicleId){
		$usages = $this->api->get('bitacora')->data();
		
		$data = [];

		foreach($usages as $usage){
			if(getStr($usage->Vehicle_idvehicle) === $vehicleId){ 

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

	// Get vehicle log info
	public function getLogData($query){
		$startDate = $query['start_date'];
		$endDate = $query['end_date'];
		// $vectors = $query['vectors'];

		$data = [];

		foreach($query['sensors'] as $sensorId){
			$log = $this->api->getLog('idsensor', $sensorId)->data('idLogs'); // Select only first log
			$sensorType = str_replace(' ', '', getStr($log->sensorTypeName));

			$rows = explode("\n", file_get_contents($this->api->getHostUrl() . getStr($log->logname)));
			
			foreach($rows as $row){
				$fields = explode(',', $row);

				$date = $fields[0];

				if((strtotime($date) >= strtotime($startDate)) && (strtotime($date) <= strtotime($endDate))){
					$date = new DateTime($date);

					array_push($data, [
						'date' => $date->format('m/d/Y H:i'),
						$sensorType => (int) $fields[1]
					]);
				}
			}	
		}

		return $data;
	}

	public function getVehicleAnnotations($vehicleId, $userId){
		$sql = "SELECT annotations.id AS 'id', user_id, first_name, last_name, content, created_at
				FROM users, annotations WHERE users.id = annotations.user_id
				AND annotations.vehicle_id = :vehicleId
				ORDER BY created_at DESC";

		$q = $this->db->query($sql, ['vehicleId' => $vehicleId], 'array');

		$results = [];

		foreach($q->results() as $res){
			$res['is_owner'] = ($res['user_id'] === $userId);

			array_push($results, $res);
		}

		return $results;
	}

	public function addVehicleAnnotation($userId, $vehicleId, $text){
		$q = $this->db->insert('annotations', ['user_id' => $userId, 'vehicle_id' => $vehicleId, 'content' => $text]);

		return ($q->hasResults()) ? $q->lastId() : null;
	}

	public function deleteVehicleAnnotation($userId, $annotationId){
		$q = $this->db->delete('annotations', ['user_id' => $userId, 'id' => $annotationId]);

		return $q->hasResults();
	}

	// Check if user has created annotation
	public function hasAnnotation($userId, $annotationId){
		$q = $this->db->select('annotations', 'id', ['user_id' => $userId, 'id' => $annotationId]);

		return $q->hasResults();
	}
}