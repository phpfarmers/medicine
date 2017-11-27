<?php
/**
 * @name IndexController
 * @author lancelot
 * @desc 默认控制器
 * @see http://www.php.net/manual/en/class.yaf-controller-abstract.php
 */
class BaseController extends Yaf\Controller_Abstract {

	public function _initialize()
    {
        if ($this->getRequest()->isXmlHttpRequest()) {
            Yaf\Dispatcher::getInstance()->disableView();
        }
    }
}
