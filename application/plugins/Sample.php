<?php
/**
 * @name SamplePlugin
 * @desc Yaf定义了如下的6个Hook,插件之间的执行顺序是先进先Call
 * @see http://www.php.net/manual/en/class.yaf-plugin-abstract.php
 * @author lancelot
 */
class SamplePlugin extends Yaf\Plugin_Abstract {

	public function routerStartup(Yaf\Request_Abstract $request, Yaf\Response_Abstract $response) {
        //Yaf\Dispatcher::getInstance()->autoRender(false);
        $current_url = strtolower($_SERVER['REQUEST_URI']);
        echo $current_url;
        echo '<pre>';
        if (\Yaf\Registry::get('config')->site->use_module == 1) {
            return true;
        }
        
        $uri = $request->getRequestUri();
        $uris = explode('/', trim($uri, '/'));
        
        $request->setRequestUri('/'.implode('/', $uris));
	}

	public function routerShutdown(Yaf\Request_Abstract $request, Yaf\Response_Abstract $response) {
	}

	public function dispatchLoopStartup(Yaf\Request_Abstract $request, Yaf\Response_Abstract $response) {
	}

	public function preDispatch(Yaf\Request_Abstract $request, Yaf\Response_Abstract $response) {
        $module_name = $request->getModuleName();
        $action_name = $request->getActionName();
        $controller_name = $request->getControllerName();
        var_dump($controller_name);
        var_dump($action_name);
        if ($module_name != 'Index') {
            $view_path = 'modules/'. $module_name.'/views/'.$controller_name.'/';
        } else  {
            return true;
        }

        echo '<pre>';
        echo $view_path;
        $view = new \Yaf\View\Simple(APP_PATH.$view_path);
        return true;
        echo '<br>';
        echo $view->getScriptPath();
        echo '<br>';

	}

	public function postDispatch(Yaf\Request_Abstract $request, Yaf\Response_Abstract $response) {
	}

	public function dispatchLoopShutdown(Yaf\Request_Abstract $request, Yaf\Response_Abstract $response) {
        ob_start();
        $result = Yaf\Loader::import(APPLICATION_PATH.'/application/views/inc/layout.php');
        $layout_content = ob_get_clean();
echo $layout_content;
        $info = $response->getBody();
        echo $info;
        echo $response->response();
        
        if (false !== strpos($info, '{__NOLAYOUT__}')) {
        
        } else {
        
        }
        $response->appendBody("<div>这是一个测试</div>");
        
	}
}
