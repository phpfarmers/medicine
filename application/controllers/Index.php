<?php
/**
 *
 */
class IndexController extends BaseController 
{

    public function init()
    {
        $request = $this->getRequest()->getParams();
        parent::_initialize();
    }
	
    /**
     * 首页
     *
     * @return    void
     **/
	public function indexAction() 
    {
        $this->getView()->assign("name", '首页ceshi');
        //$test = new \Index\Logic\TestModel();
        //echo $test->ceshi();
        //$this->display('../inc/header');
	}

    public function testAction()
    {
        echo '<pre>';
        print_r($config = Yaf\Registry::get('config'));
        echo $config['directory'];exit;
        $this->getView()->display();
    }
}
