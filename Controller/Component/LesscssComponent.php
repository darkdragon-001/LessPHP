<?php

App::uses('LessPHPHandler', 'LessPHP.Lib');
App::uses('Component', 'Controller');

class LesscssComponent extends Component {

	protected $lessphp;
	
	public function initialize(Controller $controller) {
		parent::initialize($controller);
		
		$this->lessphp = new LessPHPHandler();
	}
	
	public function compileString() {
		return call_user_func_array(array($this->lessphp, 'compileString'), func_get_args());
	}
	
	public function compileFile() {
		return call_user_func_array(array($this->lessphp, 'compileFile'), func_get_args());
	}
}
