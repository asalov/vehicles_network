<?php

use \Scheb\YahooFinanceApi\ApiClient as YahooFinanceClient;

class HomeController extends Controller{
	private $authModel;
	private $api;

	public function __construct(){
		parent::__construct();

		$this->authModel = $this->loadModel('auth', false)->init();

		if(!$this->authModel->isLoggedIn()) redirect(PATH . 'login');

		$this->api = new VehiclesAPI;
	}

	public function index(){
		$this->view->render('home/index');
	}

	public function driver(){
		// DRIVER
		// if the user is assigned a heavy vehicle driver role, then this user gets
		// recommendations to YouTube videos regarding the type of heavy
		// vehicle that the driver has been assigned
		// get vehicles connected to driver => Bitacora -> Vehicle_model -> name

		$vehicleModel = $this->api->get('bitacora')->data('idBitacora')->Vehicle_Vehicle_model_idvehicle_model;
		$vehicleName = $this->api->get('vehicle_model')->data('idVehicle_model')->name;

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

		echo $videos;

		$this->view->render('home/index');
	}

	public function analyst(){
		// ANALYST
		// if the user is assigned an analyst role, then the user gets a list of the vehicles
		// and information about their usage (times, user assigned, fuel
		// consumption, reported issues)
		// get all vehicles + info 
		// Vehicle -> Logs -> Status_type, Manual_Issues? + more?

		$vehicles = $this->api->get('vehicle')->data('idvehicle');
		$sensor = $this->api->get('sensor')->data('idsensor');

		echo '<h1>Vehicle list</h1>';

		foreach($vehicles as $vehicle){
			$id = $vehicle['id'];

			echo '<b>ID:</b>' . $id. '<br>';
			echo '<b>Plate:</b>' . $vehicle->plate . '<br>';

			$model = $this->api->get('vehicle_model', $vehicle->Vehicle_model_idVehicle_model)->data('idVehicle_model');

			echo '<b>Model:</b>' . $model->name . '<br>';

			// Get sensor info

			// Would it work if there were more than one sensors??
			if((string) $sensor->Vehicle_idvehicle == (string) $id){ // Fix this wierd shit
				$sensorType = $this->api->get('sensor_type', $sensor->Sensor_type_idSensor_type)->data('idSensor_type');
				echo '<b>Sensor type:</b>' . $sensorType->name . '<br>';
			}

			echo '<hr>';
		}

		$this->view->render('home/index');	
	}

	// Get vehicle usage info
	public function getUsageData($vehicleId){
		$usage = $this->api->get('bitacora')->data('idBitacora');
		
		$data = [];

		// Would it work if there were more than one bitacorasas??
		if((string) $usage->Vehicle_idvehicle == $vehicleId){ // Fix this wierd shit
			$data['start_time'] = $usage->start_time;
			$data['end_time'] = $usage->end_time;

			$user = $this->api->get('user', $usage->User_idUser)->data('idUser');

			$data['user'] = $user->username;
		}

		echo (!empty($data)) ? json_encode($data) : 'No usage information.';
	}

	// Get vehicle log info
	public function getLogs($vehicleId){
		$log = $this->api->get('logs')->data('idLogs');

		$data = [];

		// Would it work if there were more than one logs??
		if((string) $log->Sensor_Vehicle_idvehicle == $vehicleId){ // Fix this wierd shit
			$status = $this->api->get('status_type', $log->Sensor_Sensor_type_idSensor_type)->data('idStatus_type');
			
			$data['status'] = $status->name;
			$data['log_url'] = $this->api->getHostUrl() . $log->logname;
		}

		echo (!empty($data)) ? json_encode($data) : 'No log information.';
	}
	
	public function director(){
		// DIRECTOR
		// if the user is assigned a partner or director role, the user can get the
		// historical stock market value of his/her company in a CSV file.
		// get director company + stock market API

		$yahoo = new YahooFinanceClient;

		$data = $yahoo->getHistoricalData('VOLVY', new DateTime('2015-09-01'), new DateTime);

		print_r($data);

		$this->view->render('home/index');
	}
}