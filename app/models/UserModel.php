<?php

use \Scheb\YahooFinanceApi\ApiClient as YahooFinanceClient;

class UserModel extends Model{
	private $api;

	public function init(){
		$this->api = new VehiclesAPI;

		return $this;
	}

	public function showVideoRecommendations($userId){
		// Review this!

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
			$vehicleModel = $this->api->get('vehicle_model', $userVehicle)->data('idVehicle_model');
			$googleAPI = new Google_Client;
			$googleAPI->setDeveloperKey(YOUTUBE_API_KEY);

			$youtube = new Google_Service_YouTube($googleAPI);

			$searchResults = $youtube->search->listSearch('id, snippet', ['q' => getStr($vehicleModel->name)]);
			
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

		return null;
	}

	public function getStockData($userId){	
		$yahoo = new YahooFinanceClient;

		$organizationId = $this->api->get('user', $userId)->data('idUser')->Organization_idOrganization;
		$company = $this->api->get('organization', $organizationId)->data('idOrganization');

		return $yahoo->getHistoricalData($company->stockName, new DateTime('2015-09-01'), new DateTime);
	}	
}