<?php

class Model{
	protected $db = null;

	public function loadDb(DB $db){
		$this->db = $db;	
	}
}