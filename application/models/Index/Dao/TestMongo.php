<?php
/**
 * 测试mongo操作
 *
 *
 *
 */
namespace Index\Dao;

class TestMongoModel extends \Index\Dao\BaseModel
{
    public function __construct() 
    {
        parent::__construct();

        $this->_sanger_db = $sanger_db = \Db\Mongod::getInstance(array('db' => 'projectdb'));
    }  

    public function testFind()
    {
        
        $log_operate_db  = $this->_sanger_db->selectTable('sg_log_operate');

        $_condition = array();
        //$infos = $log_operate_db->field(array('test_id' => '_id', 'title'))->where(array('_id' =>getObjectId('5a08e4d995a47102470c94b7')))->findAll();
        $infos = $log_operate_db->field(array('test_id' => '_id', 'title'))->skip(1)->limit(2)->sort(array('_id' => -1))->findAll();
        $info = $log_operate_db->field(array('test_id' => '_id', 'title'))->skip(0)->limit(2)->sort(array('_id' => -1))->find();
        var_print($infos);
        var_print($info);
        $count = $log_operate_db->where(array('_id' =>getObjectId('5a0a044295a47102470c94cb')))->count();
        echo $count;
    } 

    public function testUpdate()
    {
        $log_operate_db  = $this->_sanger_db->selectTable('sg_log_operate');

        $result = '';
        try {
            $result = $log_operate_db->where(array('_id' => getObjectId('5a08e4d995a47102470c94b7')))->update(array('content' => '测试更新内容2', 'title' => '更新测试标题'));
        } catch (\Exception $e) {
            echo $e->getMessage();
        }
        var_dump($result);
    }
    

    public function testInsert() 
    {
        var_print($this->_sanger_db);
        $log_operate_db  = $this->_sanger_db->selectTable('sg_log_operate');
        //$result = $log_operate_db->insert(array('title' => '测试标题1', 'content' => '测试内容1'));
        
        $params = array(
            array('title' => '批量测试标题1', 'content' => '批量测试内容1'),
            array('title' => '批量测试标题2', 'content' => '批量测试内容2'),
        );
        $result = $log_operate_db->insertAll($params);
        echo '<br>=========================================================<br>';
        return true;
    }

    public function testDelete()
    {
        
        $log_operate_db  = $this->_sanger_db->selectTable('sg_log_operate');

        $log_operate_db->where(array('_id' => getObjectId('5a0a044195a47102470c94c6')))->delete();
    }
}
