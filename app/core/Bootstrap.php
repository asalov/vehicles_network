<?php

class Bootstrap{
	private $controllerName;
	private $method = DEFAULT_METHOD;

	public function __construct(){
		$this->controllerName = getClassByName(DEFAULT_CONTROLLER);

		$this->route($this->parseUrl());
	}

	private function parseUrl(){
		$url = [];

		if(isset($_GET['url'])){
			$url = explode('/', rtrim($_GET['url'], '/'));

			$this->controllerName = getClassByName($url[0]);

			array_shift($url);

			if(!empty($url)){
				$this->method = $url[0];
				array_shift($url);	
			}
		}
		return $url;
	}

	private function route($url){
		if(class_exists($this->controllerName)){
			$controller = new $this->controllerName;
			$params = (!empty($url)) ? array_values($url) : [];

			if(method_exists($controller, $this->method)){
				call_user_func_array([$controller, $this->method], $params);
			}else{
				$this->error();
			}
		}else{
			$this->error();
		}
	}

	private function error(){
		$error = new ErrorController;
		$error->{DEFAULT_METHOD}();

		redirect(404);
	}
}