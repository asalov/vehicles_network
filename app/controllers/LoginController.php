<?php

class LoginController extends Controller{
	private $authModel;

	public function __construct(){
		parent::__construct();

		$this->authModel = $this->loadModel('auth')->init();
	}

	public function index(){
		$this->view->render('login/index');
	}

	public function authenticate($loginOption){
		try{
			$account = ucfirst(strtolower($loginOption));
			$user = $this->authModel->login($account);

			// var_dump($user);
			// $this->view->render('home/index');

			if($user !== null){
				// Redirect depending on user role
				redirect(PATH . 'home/' . strtolower($user->role));
			}else{
				$this->view->set('error', 'You do not have permission to access this system.');

				$this->view->render('login/index');
			}

		}catch(Exception $e){
			$this->view->set('error', $e->getMessage());

			$this->view->render('login/index');
		}
	}

	public function logout(){
		$this->authModel->logout();

		redirect(PATH . 'login');
	}

	public function privacy(){
		echo 'Terms and conditions';
	}
}