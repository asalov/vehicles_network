<?php

use \Scheb\YahooFinanceApi\ApiClient as YahooFinanceClient;

class LoginController extends Controller{
	private $authModel;

	public function __construct(){
		parent::__construct();

		$this->authModel = $this->loadModel('auth', false)->init();
	}

	public function index(){
		$this->view->render('login/index');
	}

	public function authenticate($loginOption){
		try{
			$account = ucfirst(strtolower($loginOption));
			$user = $this->authModel->login($account);
		}catch(Exception $e){
			$this->view->set('error', 'Ooophs, we got an error: ' . $e->getMessage());

			$this->view->render('login/index');
		}

		// Redirect depending on the role of the user
		// New test comment I just made!!!
		redirect(PATH);
	}

	public function logout(){
		$this->authModel->logout();

		redirect(PATH . 'login');
	}

	public function driver(){
		// DRIVER
		// if the user is assigned a heavy vehicle driver role, then this user gets
		// recommendations to YouTube videos regarding the type of heavy
		// vehicle that the driver has been assigned
		// get vehicles connected to driver => Bitacora -> Vehicle_model -> name

		$api = new VehiclesAPI;

		$vehicleModel = $api->get('bitacora')->data('idBitacora')->Vehicle_Vehicle_model_idvehicle_model;
		$vehicleName = $api->get('vehicle_model')->data('idVehicle_model')->name;

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
	}

	public function director(){
		// DIRECTOR
		// if the user is assigned a partner or director role, the user can get the
		// historical stock market value of his/her company in a CSV file.
		// get director company + stock market API

		$yahoo = new YahooFinanceClient;

		$data = $yahoo->getHistoricalData('VOLVY', new DateTime('2015-09-01'), new DateTime);

		print_r($data);
	}

	public function analyst(){
		// ANALYST
		// if the user is assigned an analyst role, then the user gets a list of the vehicles
		// and information about their usage (times, user assigned, fuel
		// consumption, reported issues)
		// get all vehicles + info 
		// Vehicle -> Logs -> Status_type, Manual_Issues? + more?

		$api = new VehiclesAPI;

		$vehicles = $api->get('vehicle')->data('idvehicle');
		$sensor = $api->get('sensor')->data('idsensor');
		$usage = $api->get('bitacora')->data('idBitacora');
		$log = $api->get('logs')->data('idLogs');

		echo '<h1>Vehicle list</h1>';

		foreach($vehicles as $vehicle){
			$id = $vehicle['id'];

			echo '<b>ID:</b>' . $id. '<br>';
			echo '<b>Plate:</b>' . $vehicle->plate . '<br>';

			$model = $api->get('vehicle_model', $vehicle->Vehicle_model_idVehicle_model)->data('idVehicle_model');

			echo '<b>Model:</b>' . $model->name . '<br>';

			// Get sensor info

			// Would it work if there were more than one sensors??
			if((string) $sensor->Vehicle_idvehicle == (string) $id){ // Fix this wierd shit
				$sensorType = $api->get('sensor_type', $sensor->Sensor_type_idSensor_type)->data('idSensor_type');
				echo '<b>Sensor type:</b>' . $sensorType->name . '<br>';
			}

			// Get usage info

			// Would it work if there were more than one bitacorasas??
			if((string) $usage->Vehicle_idvehicle == (string) $id){ // Fix this wierd shit
				echo '<p>Usage stats</p>';
				echo '<b>Start time:</b>' . $usage->start_time . '<br>';
				echo '<b>End time:</b>' . $usage->end_time . '<br>';

				$user = $api->get('user', $usage->User_idUser)->data('idUser');

				echo '<b>Driver:</b>' . $user->username . '<br>';
			}

			// Get log info

			// Would it work if there were more than one logs??
			if((string) $log->Sensor_Vehicle_idvehicle == (string) $id){ // Fix this wierd shit
				echo '<p>Log info</p>';

				$status = $api->get('status_type', $log->Sensor_Sensor_type_idSensor_type)->data('idStatus_type');
				
				echo '<b>Status:</b>' . $status->name . '<br>';
				echo '<b>Log file:</b> <a href="' . $api->getHostUrl() . $log->logname . '">Log</a><br>';
			}

			echo '<hr>';
		}
	}
}