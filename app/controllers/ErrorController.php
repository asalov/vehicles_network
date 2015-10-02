<?php

class ErrorController extends Controller{
	public function __construct(){
		parent::__construct();
	}

	public function index(){
		$this->view->set('title', '404 - Page Not Found');

		$this->view->render('error/index');
	}

	public function show(){
		$this->view->set('title', 'Page Unavailable');

		$this->view->render('error/show');
	}
}
