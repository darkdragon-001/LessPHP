<?php

App::uses('LessPHPHandler', 'LessPHP.Lib');
App::uses('AppHelper', 'View/Helper');

class LesscssHelper extends AppHelper {
	public $helpers = array('Html');
	protected $lessphp;
	
	public function __construct(View $View, $settings = array()) {
		// call parent constructor
		parent::__construct($View, array());
		
		// load lessphp
		$this->lessphp = new LessPHPHandler($settings);
	}
	
	public function compileString($string) {	// compiles a string and returns result. use for html style attributes or <style> tag
		return $this->lessphp->compileString($string);
	}
	
	public function css() {	// embed css file
		call_user_func_array(array($this->Html, 'css'), func_get_args());
	}

	public function less($path, $rel = null, $options = array()) {	// embed less file
		if (is_array($path)) {
			$out = '';
			foreach ($path as $file) {
				$out .= $this->less($file, $rel, $options);
			}
			return $out;
		}
		
		$this->lessphp->compileFile($path, null, $options + array('save' => true, 'alwaysCompile' => false));
		
		// use assetURL to create fullPath for Html->css()
		$url = $this->assetUrl($this->lessphp->getCssFile($path), $options + array('pathPrefix' => '', 'ext' => '.css'));
		return $this->Html->css($url, $rel, $options);
	}

	public function style($path) {	// auto determine file type based on extension
		$args = func_get_args();
		
		if (is_array($path)) {
			$out = '';
			foreach ($path as $file) {
				$out .= call_user_func_array(array($this, 'style'), $args + array(0 => $file));
			}
		}
		
		if ($this->lessphp->isLessFile($path)) {	// file is .less file
			$out = call_user_func_array(array($this, 'less'), $args + array(0 => $path));
		} else {									// assume .css file
			$out = call_user_func_array(array($this, 'css'), $args + array(0 => $path));
		}
		
		return $out;
	}
}

