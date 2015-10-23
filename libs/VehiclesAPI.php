<?php

class VehiclesAPI{
	private $data;
	private $hostUrl = 'http://4me302-ht15.host22.com/';

	public function __construct(){

	}

	public function get($table, $id = ''){
		$params = 'table=' . ucfirst(strtolower($table));

		if($id !== '') $params .= '&id=' . $id;

		$this->retrieveData('getData', $params); 

		return $this;
	}

	private function retrieveData($method, $params){
		$url = $this->hostUrl . $method . '.php?' . $params;

		$this->data = simplexml_load_string(file_get_contents($url));
	}

	public function data($prop = ''){
		return ($prop == '') ? $this->data : $this->data->{$prop};
	}

	public function getHostUrl(){
		return $this->hostUrl;
	}

	public function getLog($field, $value){
		$params = 'parameter=' . $field . '&value=' . $value;

		$this->retrieveData('getLogContext', $params); 

		return $this;
	}
}