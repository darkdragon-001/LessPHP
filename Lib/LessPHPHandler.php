<?php

/**
 * Pre-processing filter that adds support for LESS.css files.
 *
 * Requires lessphp to be installed.
 *
 * eg. git submodule add https://github.com/leafo/lessphp.git app/Vendor/lessphp
 *
 * @see http://leafo.net/lessphp
 */

class LessPHPHandler {
	protected $less;
	public $settings;
	
	public function __construct($settings = array()) {
		// set settings
		$default_settings = array(
			'ext' => '.less',
			'path' => 'lessphp/lessc.inc.php',
			'sourcePath' => CSS,				// relative path to WWW_ROOT with less style files
			'compiledPath' => 'compiled_css/',	// full path with compiled files
		);
		$this->settings = array_merge($default_settings, $settings);
		
		// load lessphp
		App::import('Vendor', 'lessc', array('file' => $this->settings['path']));
		if (!class_exists('lessc')) {
			throw new Exception('Cannot load "LessPHP".');
		}
		$this->less = new lessc();
	}
	
	public function setSetting($name, $value) {
		$this->settings[$name] = $value;
	}
	
	public function getSettings($name = null) {
		if ($name === null) {
			return $this->settings;
		} else {
			return $this->settings[$name];
		}
	}
	
	public function compileString($string) {	// compiles a string and returns result. use for html style attributes or <style> tag
		return $this->less->compile($string);
	}
	
	public function compileFile($src, $dst = null, $options = array()) {	// compiles a file (ext: .less) and saves/returns result
		$options += array(
			'save' => true,	// save file
			'alwaysCompile' => false,	// always recompile
		);
		
		if (is_array($src)) {
			foreach ($src as $file) {
				$this->compileFile($file, $dst, $options);
			}
			return true;
		}
		
		// generate file paths
		$src = $this->fullPath($this->settings['sourcePath'], $src, $this->settings['ext']);	// TODO make source lookup path variable
		$dst = 	(is_string($dst) && !empty($dst)) ? $dst : '';
		$dst .= ((strlen($dst) > 0 && $dst{strlen($dst)-1} === '/') || empty($dst)) ? basename($src, $this->settings['ext']) : '';
		$dst = $this->fullPath(WWW_ROOT . $this->settings['compiledPath'], $dst, '.css');
		
		// TODO check if $src exists, otherwise throw error
		
		// return/save result
		$compileFunction = ($options['alwaysCompile']) 
								? 'compileFile' 
								: 'checkedCompile';
		
		try {
			call_user_func(array($this->less, $compileFunction), $src, $dst);
			$out = file_get_contents($dst);
		} catch (Exception $e) {
			throw new Exception('Error compiling LESS.css file.', 0, $e);
			$out = false;
		}
		if (!$options['save']) {
			unlink($dst);	// remove file when not needed
		}
		
		return $out;
	}
	
	protected function fullPath($prefix, $file, $ext) {
		// prepend prefix
		if ($file{0} !== '/') {
			$file = $prefix . $file;
		}
		
		// append file extension
		if (substr($file, -strlen($ext)) !== $ext) {
			$file .= $ext;
		}
		
		return $file;
	}
	
	public function isLessFile($file) {	// checks if file has extension .less and exists
		return file_exists($this->fullPath($this->settings['sourcePath'], $file, $this->settings['ext']));
	}
	
	public function getCssFile($file) {
		return $this->fullPath($this->settings['compiledPath'], basename($file, $this->settings['ext']), '.css');
	}
}

