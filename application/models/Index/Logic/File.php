<?php
/**
 * 文件系统类
 *
 * @return void
 */
namespace Index\Logic;

class FileModel extends \Index\Logic\BaseModel
{
    public function __construct() 
    {
        parent::__construct();
        $this->_db = new \Index\Dao\FileMysqlModel();
    }   

    public function ceshi()
    {
        $infos = $this->_db->testFindAll();
        var_print($infos);
    }    
}
