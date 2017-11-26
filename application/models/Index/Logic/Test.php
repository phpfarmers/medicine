<?php
/**
 * 测试
 *
 *
 *
 */
namespace Index\Logic;

class TestModel extends \Index\Logic\BaseModel
{
    public function __construct() 
    {
        parent::__construct();
        $this->_db = new \Index\Dao\TestMysqlModel();
    }   

    public function ceshi()
    {
        $infos = $this->_db->testFindAll();
        var_print($infos);
    }    


}
