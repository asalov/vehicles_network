<?php

class DirectorController extends RoleController{
	
	public function __construct(){
		parent::__construct();
	}

	public function index(){
		if($this->authModel->checkPermissions('access_stock_data')){
			$company = $this->userModel->getCompany($this->userId);
			
			$this->view->set('companyName', $company->name);
			$this->view->set('stockName', $company->stockName);
			$this->view->set('accessGranted', true);
			$this->view->set('showDatepicker', true);
			$this->view->set('showVisualization', true);
		}

		$this->view->render('home/stock');		
	}

	public function getStockData(){
		echo $this->userModel->getStock($_POST);
	}	
}