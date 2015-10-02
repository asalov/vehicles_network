<?php

class User{
	private $session;
	private $db;
	private $hash;
	private $data = [];
	private $loggedIn;
	
	public function __construct(Session $session, DB $db, Hash $hash){
		$this->session = $session;
		$this->db = $db;
		$this->hash = $hash;
		
		$this->loggedIn = $this->find($this->session->get('loggedIn'));
	}

	public function login($username, $password){
		$query = $this->db->select('users', '*', ['username' => $username]);

		if($query->rows() === 1){
			$user = $query->first();

			if($this->hash->verifyPass($password, $user->password)){
				$this->loggedIn = true;

				$this->session->set('loggedIn', $user->id, true);
				$this->session->regenerate();

				$this->data = $user;		
			}
		}

		return $this->loggedIn;
	}

	public function logout(){
		$this->session->delete('loggedIn');
		$this->session->destroy();

		$this->loggedIn = false;
	}
	
	// Find user by id
	private function find($id){
		$query = $this->db->select('users', '*', ['id' => $id]);

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
		$id = $this->get('id');

		$sql = "SELECT perm_value FROM user_perms, permissions 
				WHERE permissions.id = user_perms.perm_id 
				AND user_id = :id AND permission_name = :permission";
		$query = $this->db->query($sql, ['id' => $id, 'permission' => $permisson]);

		return $query->first() === 1;
	}

	public function isLoggedIn(){
		if($this->session->timeout('loggedIn')) $this->loggedIn = false;

		return $this->loggedIn;
	}	
}