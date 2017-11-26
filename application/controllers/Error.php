<?php
/**
 * 
 */
class ErrorController extends Yaf\Controller_Abstract 
{

	public function errorAction($exception) 
    {
		//1. assign to view engine
		$this->getView()->assign("exception", $exception);
		//5. render by Yaf 
	}
}
