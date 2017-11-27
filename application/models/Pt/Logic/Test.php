<?php
/**
 * 测试
 *
 *
 *
 */
namespace Pt\Logic;

class TestModel extends \Pt\Logic\BaseModel
{
    public function __construct() 
    {
        parent::__construct();
        $this->_db = new \Pt\Dao\TestMysqlModel();
    }   

    public function ceshi()
    {
        $infos = $this->_db->testFindAll();
        var_print($infos);
    }    


}
