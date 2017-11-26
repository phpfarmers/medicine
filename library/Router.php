<?php

/**
 * mongo
 *
 * @return   void
 **/
class Router extends \yaf\Request_Abstract implements \Yaf\Route_Interface
{
    public function route($request)
    {
        $host = $this->_getHostMapConfig();
        if (empty($host)) {
            return true;
        }
        $request->module = $host;
        $uri  = trim($_SERVER['REQUEST_URI'], '/');
        if (strpos($uri, '?') !== false) {
            $uris = explode('/', substr($uri, 0, strpos($uri, '?')));
        } else {
            $uris = explode('/', $uri);
        }
        if (isset($uris[0])) {
            $request->controller = $uris[0];
        }
        if (isset($uris[1])) {
            $request->action = $uris[1];
        }
        $params = array();

        if (isset($uris[2]) && !empty($uris[2])) {
            $query_strings = array_slice($uris, 2);
            foreach ($query_strings as $key => $val) {
                if ($key %2 == 0) {
                    $params[$val] = isset($query_strings[$key+1]) ? $query_strings[$key + 1] : '';
                }
            }

        }

        $request_params = $_REQUEST;
        if ($request_params) {
            foreach ($request_params as $key => $val) {
                $params[$key] = $val;
            }
        }
        $request->params = $params;
/*        
        $smarty_params = Yaf\Application::app()->getConfig()->smarty;
        
        $template_params = array();
        foreach ($smarty_params as $key =>  $val){
            $template_params[$key] = $val;
        } 

        $template_dir = APP_PATH.'/modules/'.$request->module.'/views/';
        $template_params['compile_dir'] = APP_PATH. '/../Runtime/compile/'.$request->module.'/';
        $smarty = new \Smarty\Adapter($template_dir, $template_params);
        \Yaf\Dispatcher::getInstance()->setView($smarty);
*/
        return true;
    }
    
    private function _getHostMapConfig()
    {
        $host = $_SERVER['HTTP_HOST'];
        $hosts = explode('.', $host);
        
        $config = array(
            'report' => 'Report',
        );

        if (isset($config[$hosts[0]])) {
            return $config[$hosts[0]];
        }
        return '';
    }

    public function assemble(array $mvc, array $query = null)
    {
        return true;
    }
}

