<?php

class User{
	private $session;
	private $db;
	private $data = [];
	private $loggedIn;
	
	public function __construct(Session $session, DB $db){
		$this->session = $session;
		$this->db = $db;
		
		$this->loggedIn = $this->find($this->session->get('loggedIn'));
	}

	public function login($email){
		$sql = "SELECT users.external_id AS 'id', roles.name AS 'role' 
				FROM users, roles WHERE users.role_id = roles.id AND email = :email";
		$q = $this->db->query($sql, ['email' => $email]);

		$user = $q->first();

		if($user !== null){
			$this->loggedIn = true;
			$this->session->set('loggedIn', $user->id, true);
			$this->session->regenerate();
		}

		return $user;
	}

	public function logout(){
		$this->session->delete('loggedIn');
		$this->session->destroy();

		$this->loggedIn = false;
	}
	
	// Find user by id
	private function find($id){
		$query = $this->db->select('users', '*', ['external_id' => $id]);

		if($query->rows() === 1){
			$this->data = $query->first();
			return true;
		}
		
		return false;
	}
	
	public function data(){
		return $this->data;
	}
	
	// Get user data
	public function get($item){
		return (isset($this->data->{$item})) ? $this->data->{$item} : null;
	}
	
	// Check if a user has a certain permission
	public function hasPermission($permisson){
		$id = $this->get('external_id');

		$sql = "SELECT perm_id FROM users, permissions, role_permissions
				WHERE permissions.id = role_permissions.perm_id 
				AND users.role_id = role_permissions.role_id
				AND users.external_id = :id AND permissions.name = :permission";
		$query = $this->db->query($sql, ['id' => $id, 'permission' => $permisson]);

		return $query->hasResults();
	}

	public function isLoggedIn(){
		if($this->session->timeout('loggedIn')) $this->loggedIn = false;

		return $this->loggedIn;
	}	
}