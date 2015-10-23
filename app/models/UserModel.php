<?php

class UserModel extends Model{
	private $api;

	public function init(){
		$this->api = new VehiclesAPI;

		return $this;
	}

	// Review this!
	public function getAssignedVehicle($userId){
		$bitacora = $this->api->get('bitacora')->data();

		foreach($bitacora as $b){
			if(getStr($b->User_idUser) === $userId) return $b;
		}
	}

	public function showVideoRecommendations($userId, $resultsPageToken = null){
		// Review this!
		// if the user is assigned a heavy vehicle driver role
		// get vehicles connected to driver => Bitacora -> Vehicle_model -> name

		$bitacora = $this->api->get('bitacora')->data();

		$userVehicle = $this->getAssignedVehicle($userId)->Vehicle_Vehicle_model_idvehicle_model;

		if($userVehicle !== null){
			$vehicleModel = getStr($this->api->get('vehicle_model', $userVehicle)->data('idVehicle_model')->name);
			$googleAPI = new Google_Client;
			$googleAPI->setDeveloperKey(YOUTUBE_API_KEY);

			$youtube = new Google_Service_YouTube($googleAPI);

			$searchOptions = [
				'q' => $vehicleModel,
				'maxResults' => 10,
				'type' => 'video'
			];

			if($resultsPageToken !== null) $searchOptions['pageToken'] = $resultsPageToken;

			$searchResults = $youtube->search->listSearch('id, snippet', $searchOptions);
			$searchResults->searchQuery = $vehicleModel; // Add query string to output

			return $searchResults;
		}

		return null;
	}

	public function getCompany($userId){
		$organizationId = $this->api->get('user', $userId)->data('idUser')->Organization_idOrganization;
		
		return $this->api->get('organization', $organizationId)->data('idOrganization');
	}

	public function getStock($params){		
		$url = 'http://ichart.finance.yahoo.com/table.csv?';

		$i = 0;
		$len = count($params);

		foreach($params as $key => $val){
			$url .= $key . '=' . $val;

			if($i < $len - 1) $url .= '&';

			$i++;
		}

		return file_get_contents($url);
	}
}