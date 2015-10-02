<?php

class LoginController extends Controller{
	private $authModel;

	public function __construct(){
		parent::__construct();

		$this->authModel = $this->loadModel('auth', false)->init();
	}

	public function index(){
		$this->view->render('login/index');
	}

	public function authenticate($loginOption){
		try{
			$account = ucfirst(strtolower($loginOption));
			$user = $this->authModel->login($account);
		}catch(Exception $e){
			$this->view->set('error', 'Ooophs, we got an error: ' . $e->getMessage());

			$this->view->render('login/index');
		}

		// Redirect depending on role
		// NEW COMMENT ADDED FOR TESTING GIT!!!!
		redirect(PATH);
	}

	public function logout(){
		$this->authModel->logout();

		redirect(PATH . 'login');
	}
}