<?php
/**
 * @name Bootstrap
 * @author lancelot
 * @desc 所有在Bootstrap类中, 以_init开头的方法, 都会被Yaf调用,
 * @see http://www.php.net/manual/en/class.yaf-bootstrap-abstract.php
 * 这些方法, 都接受一个参数:Yaf_Dispatcher $dispatcher
 * 调用的次序, 和申明的次序相同
 */
class Bootstrap extends Yaf\Bootstrap_Abstract{

    public function _initConfig() {
		//把配置保存起来
		$arrConfig = Yaf\Application::app()->getConfig();
		Yaf\Registry::set('config', $arrConfig);
	}

	public function _initPlugin(Yaf\Dispatcher $dispatcher) {
		//注册一个插件
		//$objSamplePlugin = new SamplePlugin();
		//$dispatcher->registerPlugin($objSamplePlugin);
	}

	public function _initRoute(Yaf\Dispatcher $dispatcher) {
        
		//在这里注册自己的路由协议,默认使用简单路由
        $router = $dispatcher->getInstance()->getRouter();
        $router->addRoute('route', new Router());
        //$route = new Yaf\Route\Simple("m", "c", "a");
        //$router->addRoute("simple", $route);
        //$router->addConfig(Yaf\Registry::get('config')->routes);
	}
	
	public function _initView(Yaf\Dispatcher $dispatcher){
		//在这里注册自己的view控制器，例如smarty,firekylin
        //var_print(Yaf\Register::get('smarty'));
		//var_print(Yaf\Registry::get('config')->smarty);
        //$smarty = new \Smarty\Adapter(null, Yaf\Registry::get('config')->smarty);
    
	}

    public function _initSmarty(Yaf\Dispatcher $dispatcher)
    {
        /*$router = Yaf\Dispatcher::getInstance()->getRouter();

        var_print($router);
        $request = Yaf\Dispatcher::getInstance()->getRequest();
        echo '-------------------';
        echo $request->getModuleName();
        var_dump($request->module);
        var_print($request);
        $smarty = new \Smarty\Adapter(null, Yaf\Application::app()->getConfig()->smarty);
        $dispatcher->setView($smarty);*/
    }

    public function _initLayout(Yaf\Dispatcher $dispatcher)
    {
        //$layout = new LayoutPlugin('layout.php');
        //$dispatcher->registerPlugin($layout);


    }
}
