<?php

use \Scheb\YahooFinanceApi\ApiClient as YahooFinanceClient;

class VehicleModel extends Model{
	private $api;

	public function __construct(){
		$this->api = new VehiclesAPI;
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
		$sensor = $this->api->get('sensor')->data('idsensor');
		
		$data = [];

		$data['model'] = getStr($model->name);
		// Would it work if there were more than one sensors??
		if(getStr($sensor->Vehicle_idvehicle) === $vehicleId){ // Fix this wierd shit
			$sensorType = $this->api->get('sensor_type', $sensor->Sensor_type_idSensor_type)->data('idSensor_type');
			$data['sensor'] = $sensorType->name;
		}

		return $data;
	}

	// Get vehicle usage data
	public function getUsageData($vehicleId){
		$usage = $this->api->get('bitacora')->data('idBitacora');
		
		$data = [];

		// Would it work if there were more than one bitacorasas??
		if(getStr($usage->Vehicle_idvehicle) === $vehicleId){ // Fix this wierd shit
			$data['start_time'] = getStr($usage->start_time);
			$data['end_time'] = getStr($usage->end_time);

			// Get user
			$user = $this->getVehicleUser(getStr($usage->User_idUser));
			$data['user'] = $user->first_name . ' ' . $user->last_name;
		}

		return $data;
	}

	// Get vehicle log info
	public function getLogData($vehicleId){
		$log = $this->api->get('logs')->data('idLogs');

		$data = [];

		// Would it work if there were more than one logs??
		if(getStr($log->Sensor_Vehicle_idvehicle) === $vehicleId){ // Fix this wierd shit
			$status = $this->api->get('status_type', $log->Sensor_Sensor_type_idSensor_type)->data('idStatus_type');
			
			$data['status'] = getStr($status->name);
			$data['link'] = $this->api->getHostUrl() . getStr($log->logname);
		}

		return $data;
	}

	public function getVehicleUser($id){
		$q = $this->db->select('users', ['first_name', 'last_name'], ['external_id' => $id]);

		return $q->first();
	}

	// Should this be here?
	public function showVideoRecommendations(){
		$model = $this->api->get('bitacora')->data('idBitacora')->Vehicle_Vehicle_model_idvehicle_model;
		$vehicleName = $this->api->get('vehicle_model', $model)->data('idVehicle_model')->name;

		$googleAPI = new Google_Client;
		$googleAPI->setDeveloperKey(YOUTUBE_API_KEY);

		$youtube = new Google_Service_YouTube($googleAPI);

		$searchResults = $youtube->search->listSearch('id, snippet', ['q' => $vehicleName]);
		
		$videos = '';
		foreach($searchResults['items'] as $result){
			switch ($result['id']['kind']) {
        		case 'youtube#video':
          			$videos .= sprintf('<li>%s (%s)</li>',
              		$result['snippet']['title'], $result['id']['videoId']);
          		break;
		        // case 'youtube#channel':
		        //   $channels .= sprintf('<li>%s (%s)</li>',
		        //       $result['snippet']['title'], $result['id']['channelId']);
		        //   break;
		        // case 'youtube#playlist':
		        //   $playlists .= sprintf('<li>%s (%s)</li>',
		        //       $result['snippet']['title'], $result['id']['playlistId']);
		        //   break;
     		}
		}

		return $videos;
	}

	public function getStockData(){	
		$yahoo = new YahooFinanceClient;

		return $yahoo->getHistoricalData('VOLVY', new DateTime('2015-09-01'), new DateTime);
	}
}