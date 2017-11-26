<?php

/**
 * 使用layout
 *
 * @param    string
 *
 * @return   void
 **/
function layout($file_name)
{
    $layout_templete = $file_name;

    if ($file_name === true) {
        $arr_config = Yaf\Application::app()->getConfig();
        if (empty($arr_config->site->layout_template)) {
            exit('layout模板为空,请配置');
        }
        $layout_template = $arr_config->site->layout_template;
    }
    $layout = new LayoutPlugin($layout_template);
    Yaf\Dispatcher::getInstance()->registerPlugin($layout); 
}

/**
 * 获取对象ID
 *
 * @param    string
 *
 * @return   void
 **/
function getObjectId($id)
{
    return new \MongoDb\BSON\ObjectID($id);
}

function include_file($file_name)
{
    $request = Yaf\Dispatcher::getInstance()->getRequest();

    $module_name = $request->getModuleName();
    if ($module_name == 'Index') {
        $file_path = APP_PATH.'/views/';
    } else {
        $file_path = APP_PATH.'/modules/'.$module_name.'/views/';
    }
    include_once($file_path.$file_name);
}

/**
 * 格式化打印
 * 
 * @param    array
 *
 * @return   void
 **/
function var_print($params)
{
    echo '<pre>';
    print_r($params);
}
