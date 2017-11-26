<?php
namespace Db\Driver;

/**
 * mongo
 *
 * @return   void
 **/
class Mongodb
{
    private static $_db_array = array();
    private $_params          = array('skip' => null, 'limit' => null, 'sort' => '', 'field' => array(), 'where' => array());

    private $_conn;
    private $_db_name;
    private $_table_str;


    /**
     * 构造实例
     *
     * @param    string
     * @param    resource
     *
     * @return   void
     **/ 
    private function __construct($table_name, $conn, $db_name)
    {
        ini_set('mongo.long_as_object', 1);
        $this->_table_name = $table_name;
        $this->_conn       = $conn;
        $this->_db_name    = $db_name;
        $this->_table_str  = $db_name.'.'.$table_name;
    }

    /**
     * 获取实例
     *
     * @param    string
     * @param    array
     *
     * @return   object
     **/
    static function getInstance($table_name,  $conn, $db_link_id, $db_name)
    {
        $link_id = md5($table_name.'_'.$db_link_id);
        if (empty(self::$_db_array[$link_id])) {
            self::$_db_array[$link_id] = new self($table_name, $conn, $db_name);
        }

        return self::$_db_array[$link_id];
    }

    /**
     * where条件
     *
     * @param    array
     *
     * @return   object
     **/
    public function where($params = array())
    {
        if (!is_array($params) || empty($params)) {
            throw new \Exception('<b style="color:red">where 参数必须是数组且不能为空</b>');
        }

        $this->_params['where'] = $params;
        
        return $this;
    }
    /**
     * 排序
     *
     * @param    string
     *
     * @return   object
     **/
    public function sort($params = array())
    {
        if (!is_array($params) || empty($params)) {
            throw new \Exception('<b style="color:red">sort 错误: 参数必须是数组且不能为空</b>');
        }
        $this->_params['sort'] = $params;
        return $this;
    }

    /**
     * 分页
     *
     * @param    string
     *
     * @return   object
     **/
    public function limit($limit = '')
    {
        if ($limit === '' || !is_int($limit)) {
            throw new \Exception('<b style="color:red">limit 错误: 参数不能为空且为正整数</b>');
        }
        $this->_params['limit'] = $limit;
        return $this;
    }

    /**
     * 分页
     *
     * @param    string
     *
     * @return   object
     **/
    public function skip($skip = '')
    {
        if ($skip === '' || !is_int($skip)) {
            throw new \Exception('<b style="color:red">skip 错误: 参数不能为空且为正整数</b>');
        }
        $this->_params['skip'] = $skip;
        return $this;
    }

    /**
     * 获取字段
     *
     * @param    string
     *
     * @return   object
     **/
    public function field($field = array())
    {
        if (!is_array($field) || empty($field)) {
            throw new \Exception('<b style="color:red">field 错误: 参数必须是数组且不能为空</b>');
        }
        $this->_params['field'] = $field;

        return $this;
    }

    /**
     * 查询多条数据
     *
     *
     * @return   array
     **/
    public function findAll()
    {
        $options = $this->_bindQueryParams();
        
        $query = new \MongoDB\Driver\Query($this->_params['where'], $options);
        
        $cursor = $this->_conn->executeQuery($this->_table_str, $query,new \MongoDB\Driver\ReadPreference(\MongoDB\Driver\ReadPreference::RP_PRIMARY_PREFERRED)); 
        $infos = array();
        $i = 0;
        
        $cursor = $cursor->toArray();
        foreach ($cursor as $val) {
            if ($this->_params['field']) {
                $field_names = array_flip($this->_params['field']);
                if (isset($field_names['_id']) && !empty($field_names['_id'])) {
                    $id = $field_names['_id'];
                    $val->$id = strval($val->_id);    
                }
            }
            
            $infos[$i] = json_decode(json_encode($val), true);
            $i ++;
        }

        $this->_clearParams();

        unset($this->cursor);
        
        return $infos;
    }

    /**
     * 查找单条数据
     *
     * @return   array
     **/
    public function find()
    {
        $info = array();
        try {
            $options = $this->_bindQueryParams();
        
            $options['limit'] = 1;

            $query = new \MongoDB\Driver\Query($this->_params['where'], $options);

            $cursor = $this->_conn->executeQuery($this->_table_str, $query); 
            foreach ($cursor as $val) {
                if ($this->_params['field']) {
                    $field_names = array_flip($this->_params['field']);
                    if (isset($field_names['_id']) && !empty($field_names['_id'])) {
                        $id = $field_names['_id'];
                        $val->$id = strval($val->_id);    
                    }
                }
                $info = json_decode(json_encode($val), true);
                break;
            }

        } catch (\Exception $e) {
            $this->_clearParams();
            throw new \Exception('<b style="color:red"> find 错误: '.$e->getMessage().'</b>');
        }
        $this->_clearParams();
        unset($cursor);
        return $info;

    }

    /**
     * 统计数量
     *
     * @param    array
     *
     * @return   int
     **/
    public function count()
    {
        try {
        $command = new \MongoDB\Driver\Command(array('count' => $this->_table_name, 'query' => $this->_params['where']));

        $result  = $this->_conn->executeCommand($this->_db_name, $command);
        } catch (\Exception $e) {
            $this->_clearParams();
            throw new \Exception('<b style="color:red"> count 错误: '.$e->getMessage().'</b>');
        }
        $this->_clearParams();
        return $result->toArray()[0]->n;
    }

    /**
     * 插入单条数据
     *
     * @param    array
     *
     * @return   string
     **/
    public function insert($params)
    {
        try {
            if (empty($params)) {
                throw new \Exception('参数不能为空'); 
            }
            $bulk = new \MongoDB\Driver\BulkWrite;
            $insert_id = $bulk->insert($params);
            $result = $this->_conn->executeBulkWrite($this->_table_str, $bulk);
        } catch (\Exception $e) {
            throw new \Exception('<b style="color:red">insert 错误: '.$e->getMessage().'</b>');
        }

        $count = $result->getInsertedCount();

        if ($count < 1) {
            throw new \Exception('<b style="color:red">insert 失败: 数据没有插入成功</b>');
        }
        
        return $insert_id;
    }

    /**
     * 插入多条数据
     *
     * @param    array
     *
     * @return   boolean 
     **/
    public function insertAll($params)
    {
        $insert_ids = array();
        try {
            if (empty($params)) {
                throw new \Exception('参数不能为空'); 
            }
            $bulk = new \MongoDB\Driver\BulkWrite;
            foreach ($params as $val) {
                $insert_ids[] = $bulk->insert($val);
            }
            $result = $this->_conn->executeBulkWrite($this->_table_str, $bulk);
        } catch (\Exception $e) {
            throw new \Exception('<b style="color:red">insert 错误: '.$e->getMessage().'</b>');
        }

        $count = $result->getInsertedCount();

        if ($count < 1) {
            throw new \Exception('<b style="color:red">insertAll 失败: 数据没有插入成功</b>');
        }

        return true;
    }

    /**
     * 更新数据 
     *
     * @param    array
     *
     * @return   array
     **/
    public function update($params)
    {
        if (!is_array($this->_params['where']) || empty($this->_params['where'])) {
            throw new \Exception('<b style="color:red">update 错误: 条件必须是数组且不能为空</b>');
        }

        if (!is_array($params)) {
            throw new \Exception('<b style="color:red">update 错误: 保存参数必须是数组且不能为空</b>');
        }

        $bulk = new \MongoDB\Driver\BulkWrite;
        
        try {
            $result = $bulk->update($this->_params['where'], $params);
            $result = $this->_conn->executeBulkWrite($this->_table_str, $bulk);
            
        } catch (\MongoDB\Driver\Exception\InvalidArgumentException $e) {
            $this->_clearParams();
            throw new \Exception('<b style="color:red"> update 错误: '. $e->getMessage().'</b>');
        }
    
        $this->_clearParams();
        if ($result->getMatchedCount() < 1) {
            throw new \Exception('<b style="color:red">update 没有找到匹配的条件</b>');
        }
        
        if ($result->getModifiedCount() < 1) {
            throw new \Exception('<b style="color:red">update 没有更新匹配的数据</b>');
        }

        return true;
    }

    /**
     * 删除数据
     *
     * @param    array
     *
     * @return   array
     **/
    public function delete()
    {
        if (!is_array($this->_params['where']) ||  empty($this->_params['where'])) {
            throw new \Exception('<b style="color:red">delete 错误: 条件必须是数组且不能为空</b>');
        }
        try {
            $bulk = new \MongoDB\Driver\BulkWrite;
            $bulk->delete($this->_params['where']);

            $result = $this->_conn->executeBulkWrite($this->_table_str, $bulk);
        } catch (\Exception $e) {
            throw new \Exception('<b style="color:red">delete 错误: '.$e->getMessage().'</b>');
        }

        var_print($result);

        return true;
    }

    /**
     * 组合查询参数
     *
     * @param    array
     *
     * @return   array
     **/
    private function _bindQueryParams()
    {
        $options = array();
        if ($this->_params['field']) {
            foreach ($this->_params['field'] as $val) {
                $projection[$val] = 1;
            }
            $options += array('projection' => $projection);
        } 

        if (isset($this->_params['skip'])) {
            $options += array('skip' => intval($this->_params['skip']));
        }
        if (isset($this->_params['limit'])) {
            $options += array('limit' => intval($this->_params['limit']));
        }

        if ($this->_params['sort']) {
            $options += array('sort' => $this->_params['sort']);
        }

        return $options;
        
    }

    /**
     * 清除参数
     *
     * @return   void
     **/
    private function _clearParams()
    {
        $this->_params = array('skip' => null, 'limit' => null, 'field' => array(), 'where' => array());
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
