<?php
/**
 * @name IndexController
 * @author lancelot
 * @desc 默认控制器
 * @see http://www.php.net/manual/en/class.yaf-controller-abstract.php
 */
class IndexController extends BaseController {

    public function init()
    {
        parent::_initialize();
    }
	
	public function indexAction() {
        var_print($this->getRequest());
        $request = $this->getRequest()->getParams();
        var_print($request);
        echo "这里是报告页面";
        $test = new \Pt\Dao\TestMysqlModel();
        $test->testFind();
        $this->getView()->display('index');
	}
}
