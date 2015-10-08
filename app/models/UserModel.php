<?php

class UserModel extends Model{
	private $api;

	public function init(){
		$this->api = new VehiclesAPI;

		return $this;
	}

	public function showVideoRecommendations($userId, $resultsPageToken = null){
		// Review this!
		// if the user is assigned a heavy vehicle driver role
		// get vehicles connected to driver => Bitacora -> Vehicle_model -> name
		$bitacora = $this->api->get('bitacora')->data('idBitacora');

		$userVehicle = null;

		foreach($bitacora as $b){
			if(getStr($b->User_idUser) === $userId){
				$userVehicle = $b->Vehicle_Vehicle_model_idvehicle_model;

				break;
			}
		}

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

	public function getCompanyStockName($userId){
		// historical stock market value of company in a CSV file.

		// http://ichart.finance.yahoo.com/table.csv?s=$organization->stockName

		$organizationId = $this->api->get('user', $userId)->data('idUser')->Organization_idOrganization;
		$company = $this->api->get('organization', $organizationId)->data('idOrganization');

		return $company->stockName;
	}	
}