<?php
/**
 * @name IndexController
 * @author lancelot
 * @desc 默认控制器
 * @see http://www.php.net/manual/en/class.yaf-controller-abstract.php
 */
class TestController extends BaseController {

    public function init()
    {
        parent::_initialize();
    }
	
	public function indexAction() {
        $request = $this->getRequest()->getParams();
        //$test = new \Report\Dao\TestMysqlModel();
        //$test->testFind();

        $test = new \Pt\Logic\TestModel();
        $test->ceshi();
	}
}
