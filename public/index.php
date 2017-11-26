<?php
@header('Content-type:text/html;Charset=utf-8');
/*
class test
{
    private test = 1;

    public function count()
    {
        $this->test = '';
        
    }

    public function setTest($value)
    {
        $this->test = $value;
    }
}
exit;

*/


define('ROOT_PATH', dirname(__FILE__));
define('APP_PATH', ROOT_PATH.'/../application');
require_once(ROOT_PATH.'/../common/function.php');
$application = new Yaf\Application(ROOT_PATH.'/../common/conf/application.ini');

$application->bootstrap()->run();


?>
