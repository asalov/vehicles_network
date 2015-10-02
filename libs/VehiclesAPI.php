<?php

class VehiclesAPI{

	public function __construct(){

	}

	public function get($table, $id = ''){
		$location = ucfirst(strtolower($table));

		if($id !== '') $location .= '&id=' . $id;

		return $this->retrieveData($location); 
	}

	private function retrieveData($url){
		$url = 'http://4me302-ht15.host22.com/index.php?table=' . $url;

		return simplexml_load_string(file_get_contents($url));
	}
}