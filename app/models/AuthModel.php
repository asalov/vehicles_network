<?php

class AuthModel extends Model{
	private $session;
	private $loggedIn;
	private $service;

	public function init(){
		$this->session = new Session;
		$this->loggedIn = $this->session->get('loggedIn');
		$this->service = new Hybrid_Auth('../config/auth_config.php');

		return $this;
	}

	public function login($account){
		// Why?!?!?
		if(isset($_REQUEST['hauth_start']) || isset($_REQUEST['hauth_done'])) Hybrid_Endpoint::process();

		$authAdapter = $this->service->authenticate($account);

		if($authAdapter){
			$email = $authAdapter->getUserProfile()->email;

			if($email == '') $email = 'anderseriksson@maildrop.cc';

			$user = $this->getUser($email);

			if(!empty($user)){
				$this->loggedIn = true;
				$this->session->set('loggedIn', $user->external_id, true);
				$this->session->regenerate();

				return $user;
			}
		}

		return false;
	}

	public function getUser($email){
		$q = $this->db->select('users', '*', ['email' => $email]);

		return $q->first();
	}

	public function logout(){
		$this->session->delete('loggedIn');
		$this->session->destroy();

		$this->loggedIn = false;

		// Working?
		$this->service->logoutAllProviders();
	}


	public function isLoggedIn(){
		if($this->session->timeout('loggedIn')) $this->loggedIn = false;
		
		return $this->loggedIn;
	}
}