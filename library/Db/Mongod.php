<?php
namespace Db;

/**
 * mongo
 *
 * @return   void
 **/
class Mongod
{
    
    static $db = null;

    private $_mongo_db;
    private $_link_id;
    private $_db_name;
    private function __construct($params)
    {
        try {
            $this->_getMongoClient($params);
        } catch(\Exception $e) {
            exit('连接mongo失败');
        }
    }

    /**
     * 获取实例
     *
     * @param    array
     *
     * @return   object
     **/
    static public function getInstance($params = array())
    {
        if (empty(self::$db[$params['db']])) {
            self::$db[$params['db']] = new self($params);
            self::$db['db_name']     = $params['db'];
        }

        return self::$db[$params['db']];
    }

    /**
     * 连接mongo
     *
     * @param    array
     *
     * @return   object
     **/
    private function _getMongoClient($params,$retry = 3)
    {
        $config = \Yaf\Registry::get('config');
        $db_config = $config->db['mongo'];
        if (empty($params['db'])) {
            exit('请配置mongo');
        }
        $db_config = $db_config[$params['db']];
        if (!isset($db_config)) {
            exit('没有对应的mongo配置');
        }

        try {
            $this->_mongo_db = new \MongoDB\Driver\Manager("mongodb://".$db_config['host'].":".$db_config['port'].'/'.$db_config['dbname']);
            $this->_db_name   = $db_config['dbname'];
            $this->_link_id   = $db_config['host'].'_'.$db_config['dbname'];
            return;
        } catch(\Exception $e) {
        }

        if ($retry > 0) {
            return $this->_getMongoClient($params, --$retry);
        }

        throw new \Exception('尝试3次连接失败');
    }

    /**
     * 选择表格
     *
     * @param    array
     *
     * @return   object
     **/
    public function selectTable($table_name)
    {
        return \Db\Driver\Mongodb::getInstance($table_name, $this->_mongo_db, $this->_link_id, $this->_db_name);
    }



    /**
     * 返回错误
     *
     * @return   string
     **/
    public function getError()
    {
        return $this->_error;
    }
    
}

/*
 $sanger_db = \Common\Custom\MongoTest::getInstance(array('db' => 'project_db'));

 //选择对应的表
 $log_operate_db  = $sanger_db->selectTable('sg_log_operate');
    
 单条数据:
 $info = $log_operate_db->field(array('log_id' => '_id', 'from_id', 'time'))->findOne(array('_id' => new \MongoId('58ec93d5dde3eed81100002b')));
 多条数据查询：
 $infos = $log_operate_db->field($field)->sort(array('time' => -1))->limit($params['limit'])->skip($params['skip'])->find($_condition);

 插入数据
$result = $log_operate_db->insert($params);
 */
