<?php
/**
 * layout插件
 */
class LayoutPlugin extends Yaf\Plugin_Abstract {
    
    private $_layout_file;
    private $_layout_dir;
    private $_layout_vars;
    public function __construct($layout_file)
    {
        $this->_layout_file = $layout_file;
        $this->_layout_dir  = APP_PATH.'/views/inc/';
    }

    public function __set($key, $value)
    {
        $this->_layout_vars[$key] = $value;
    }

    

	public function routerStartup(Yaf\Request_Abstract $request, Yaf\Response_Abstract $response) {
	}

	public function routerShutdown(Yaf\Request_Abstract $request, Yaf\Response_Abstract $response) {
	}

	public function dispatchLoopStartup(Yaf\Request_Abstract $request, Yaf\Response_Abstract $response) {
	}

	public function preDispatch(Yaf\Request_Abstract $request, Yaf\Response_Abstract $response) {
	}

	public function postDispatch(Yaf\Request_Abstract $request, Yaf\Response_Abstract $response) {
        $body = $response->getBody();
        $response->clearBody();
        
        $layout = new Yaf\View\Simple($this->_layout_dir);
        $layout->content = $body;

        $response->setBody($layout->render($this->_layout_file));
	}

	public function dispatchLoopShutdown(Yaf\Request_Abstract $request, Yaf\Response_Abstract $response) {
	}
}
