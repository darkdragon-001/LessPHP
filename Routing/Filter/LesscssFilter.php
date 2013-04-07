<?php

App::uses('LessPHPHandler', 'LessPHP.Lib');
App::uses('DispatcherFilter', 'Routing');

class LesscssFilter extends DispatcherFilter {

/**
 * Filter priority, we need it to run before router
 *
 * @var int
 */
	public $priority = 9;

/**
 * Checks if request is for a compiled asset, otherwise skip any operation
 *
 * @param CakeEvent $event containing the request and response object
 * @throws NotFoundException
 * @return CakeResponse if the client is requesting a recognized asset, null otherwise
 */
    public function beforeDispatch(CakeEvent $event) {
		// do not use in production mode
		$production = !Configure::read('debug');
		if ($production) {
			return;
		}
		
        $request = $event->data['request'];
        $response = $event->data['response'];
		$lessphp = new LessPHPHandler();
        if (
			substr($request->url, 0, strlen($lessphp->getSettings('compiledPath'))) === $lessphp->getSettings('compiledPath') &&
			substr($request->url, -strlen('.css')) === '.css'
		) {
			$file = basename($request->url, '.css');
			$content = $lessphp->compileFile($file, null, $options = array('save'=>false));
			
			$response->type('css');
            $response->body($content);
            $event->stopPropagation();
            return $response;
        }
    }
}