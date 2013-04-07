<?php

App::uses('LessPHPHandler', 'LessPHP.Lib');
App::uses('Folder', 'Utility');

class LesscssShell extends AppShell {

	public $tasks = array();
	
	protected $lessphp;

/**
 * Method called on startup
 *
 */
	public function startup() {
		parent::startup();
		
		$this->stdout->styles('error', array('text' => 'red', 'underline' => false));
		
		$this->lessphp = new LessPHPHandler();
	}

/**
 * Clear all compiled .css files.
 *
 * @return void
 */
	public function clear() {
		$this->out('Clearing compiled files');
		$this->hr();
		
		$files = $this->_collectCompiledFiles(WWW_ROOT.$this->lessphp->getSettings('compiledPath'));
		foreach ($files as $file) {
			$result = unlink($file);
			
			$success = ($result !== false);
			$status = ($success) ? '<success>Success</success>' : '<error>Error</error>';
			$this->out('Deleting file "' . $file . '" [' . $status . ']');
		}
	}

/**
 * Collects the compiled files.
 *
 * @param array $paths
 */
	protected function _collectCompiledFiles($paths) {
		$files = array();
		foreach ((array)$paths as $path) {
			$Folder = new Folder($path);
			$found_files = $Folder->findRecursive('.*\.(css)', true);
			$files = array_merge($files, $found_files);
		}
		
		return $files;
	}

/**
 * Builds all (changed) files.
 * 
 * Allows params for files, otherwise will crawl through CSS directory and look for .less files.
 * 
 * @return void
 */
	public function build() {
		$this->out('Building less files');
		$this->hr();
		
		if (empty($this->args)) {
			$files = $this->_collectFiles($this->lessphp->getSettings('sourcePath'));
		} else {
			$files = $this->args;
		}
		foreach ($files as $file) {
			$result = $this->lessphp->compileFile($file, null, array(
				'save' => true,
				'alwaysCompile' => !empty($this->params['force'])
			));
			
			$success = ($result !== false);
			$status = ($success) ? '<success>Success</success>' : '<error>Error</error>';
			$this->out('Compiling file "' . $file . '" [' . $status . ']');
		}
	}

/**
 * Collects the files to compile.
 *
 * @param array $paths
 */
	protected function _collectFiles($paths) {
		$files = array();
		foreach ((array)$paths as $path) {
			$Folder = new Folder($path);
			$found_files = $Folder->findRecursive('.*(\\'.$this->lessphp->getSettings('ext').')', true);
			$files = array_merge($files, $found_files);
		}
		
		return $files;
	}
	
/**
 * get the option parser.
 *
 * @return void
 */
	public function getOptionParser() {
		$parser = parent::getOptionParser();
		return $parser->description(array(
			'LessPHP Shell',
			'',
			'Compiles and clears your .less files.'
		))->addSubcommand('clear', array(
			'help' => 'Clears all builds defined in the ini file.'
		))->addSubcommand('build', array(
			'help' => 'Generate all builds defined in the ini and view files.'
		))->addOption('force', array(
			'help' => 'Force files to rebuild. Ignores timestamp rules.',
			'short' => 'f',
			'boolean' => true
		));
	}
}