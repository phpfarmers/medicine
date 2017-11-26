<?php
/**
 * 测试
 *
 *
 *
 */
namespace Report\Logic;

class TestModel extends \Report\Logic\BaseModel
{
    public function __construct() 
    {
        parent::__construct();
        $this->_db = new \Report\Dao\TestMysqlModel();
    }   

    public function ceshi()
    {
        $infos = $this->_db->testFindAll();
        var_print($infos);
    }    
}
