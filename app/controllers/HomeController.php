<?php

class HomeController extends Controller{
	private $authModel;

	public function __construct(){
		parent::__construct();

		$this->authModel = $this->loadModel('auth', false)->init();

		if(!$this->authModel->isLoggedIn()) redirect(PATH . 'login');
	}

	public function index(){
		$session = new Session;
		$api = new VehiclesAPI;

		$user = $api->get('user', $session->get('loggedIn'))->data('idUser');
		$role = $api->get('role', $user->Role_idRole2)->data('idRole');

		$this->view->set('name', $user->username);
		$this->view->set('role', $role->name);

		$this->view->render('home/index');
	}
}