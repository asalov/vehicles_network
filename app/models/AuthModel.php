<?php

class AuthModel extends Model{
	private $session;
	private $user;
	private $service;

	public function init(){
		$this->session = new Session;
		$this->user = new User($this->session, $this->db);
		$this->service = new Hybrid_Auth('../config/auth_config.php');

		return $this;
	}

	public function login($account){
		if(isset($_REQUEST['hauth_start']) || isset($_REQUEST['hauth_done'])) Hybrid_Endpoint::process();

		$authAdapter = $this->service->authenticate($account);

		if($authAdapter) return $this->user->login($authAdapter->getUserProfile()->email);

		return null;
	}

	public function logout(){
		$this->user->logout();

		$this->service->logoutAllProviders();
	}

	public function isLoggedIn(){
		return $this->user->isLoggedIn();
	}

	public function getUserData($field = null){
		if($field == null) return $this->user->data();

		return $this->user->get($field);
	}

	public function getRole(){
		$roleId = $this->user->get('role_id');
		$q = $this->db->select('roles', 'name', ['id' => $roleId]);

		return $q->first()->name;
	}

	public function checkPermissions($permission){
		return $this->user->hasPermission($permission);
	}
}