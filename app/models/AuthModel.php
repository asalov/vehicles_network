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
			// Further authentication
			$userId;
			switch($account){
				case 'Twitter':
					$userId = 1;
				break;
				case 'Google':
					$userId = 6;
				break;
				default:
					$userId = 4;
				break;
			}

			$this->loggedIn = true;
			$this->session->set('loggedIn', $userId, true);
			$this->session->regenerate();
		}

		return $authAdapter->getUserProfile();
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