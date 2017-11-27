<?php
/**
 * 基础控制器
 *
 * @return void
 */
class BaseController extends Yaf\Controller_Abstract 
{

	public function _initialize()
    {
        if ($this->getRequest()->isXmlHttpRequest()) {
            Yaf\Dispatcher::getInstance()->disableView();
        }
        // 使用layout
        //layout(true);
    }
}
