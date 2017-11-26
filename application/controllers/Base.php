<?php
/**
 *
 *
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
