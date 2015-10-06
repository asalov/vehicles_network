<?php

class VehiclesAPI{
	private $data;
	private $hostUrl = 'http://4me302-ht15.host22.com/';

	public function __construct(){

	}

	public function get($table, $id = ''){
		$location = ucfirst(strtolower($table));

		if($id !== '') $location .= '&id=' . $id;

		$this->retrieveData($location); 

		return $this;
	}

	private function retrieveData($url){
		$url = $this->hostUrl . 'index.php?table=' . $url;

		$this->data = simplexml_load_string(file_get_contents($url));
	}

	public function data($prop = ''){
		return ($prop == '') ? $this->data : $this->data->{$prop};
	}

	public function getHostUrl(){
		return $this->hostUrl;
	}
}