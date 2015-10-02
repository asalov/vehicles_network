<?php

class Controller{
	private $models = [];
	protected $view;

	public function __construct(){
		$this->view = new View;
	}

	protected function loadModel($name, $loadDb = true){
		$modelName = getClassByName($name, 'model');

		if(class_exists($modelName)){
			$this->models[$name] = new $modelName;

			if($loadDb){
				try{
					$dbHandler = new ErrorHandler;
					$db = new DB(DB_HOST, DB_NAME, DB_USER, DB_PASS, $dbHandler, DB_TYPE);
					$this->getModel($name)->loadDb($db);					
				}catch(Exception $e){
					regError($e->__toString());
				}
			}

			return $this->getModel($name);
		}else{
			regError('Class ' . $modelName . ' does not exist.');
		}
		return null;
	}

	protected function getModel($name){
		return (isset($this->models[$name])) ? $this->models[$name] : null;
	}
}