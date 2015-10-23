<?php

class RoleController extends Controller{
	protected $authModel;
	protected $vehicleModel;
	protected $userModel;
	protected $userId;

	public function __construct(){
		parent::__construct();

		$this->authModel = $this->loadModel('auth')->init();

		if(!$this->authModel->isLoggedIn()) redirect(PATH . 'login');

		$this->vehicleModel = $this->loadModel('vehicle')->init();
		$this->userModel = $this->loadModel('user')->init();

		$this->userId = $this->authModel->getUserData('external_id');

		$this->view->set('userLogged', true);
	}

	public function index(){
		redirect(PATH . strtolower($this->authModel->getRole()));
	}
}