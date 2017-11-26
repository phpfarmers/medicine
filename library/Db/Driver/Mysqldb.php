<?php
namespace Db\Driver;

/**
 * mysql实现
 *
 * @return   void
 **/
class Mysqldb
{
    private static $_table_arrs = array();
    private $_table_name;
    private $_conn;
    private $_field_name = '*';
    private $_where      = array();
    private $_sort       = '';
    private $_alias      = '';
    private $_group      = ''; 
    private $_join       = ''; 
    private $_trans_times = 0;

    private $_operate_types = array(
        '1' => 'IGNORE',
        '2' => 'REPLACE',
    );

    public function __clone()
    {
    }

    /**
     * 构造方法
     *
     * @return   void
     **/
    private function __construct($table_name, $conn)
    {
        $this->_table_name = $table_name;
        $this->_conn       = $conn;

        $this->_conn->query("set names utf8");
    }


    /**
     * 实例化连接
     *
     * @param    string
     * @param    resource
     * @param    string
     *
     * @return   object 
     **/
    public static function table($table_name, $conn, $link_id)
    {
        $table_link_id = md5($table_name.'_'.$link_id);
        if (!isset(self::$_table_arrs[$table_link_id])) {
            self::$_table_arrs[$table_link_id] = new self($table_name, $conn);
        }

        return self::$_table_arrs[$table_link_id];
    }    

    /**
     * 别名
     *
     * @param   string
     * 
     * @return  object
     **/
    public function alias($alias_name)
    {
        if (empty($alias_name)) {
            throw new \Exception('<b style="color:red">alias 不能为空</b>');
        }
        if (!is_string($alias_name)) {
            throw new \Exception('<b style="color:red">alias 命名必须是字符串</b>');
        }
        $this->_alias = $alias_name;

        return $this;
    }

    /**
     * 字段
     *
     * @param    array
     *
     * @return   object
     **/
    public function field($params)
    {
        if (!is_array($params)) {
            throw new \Exception('<b style="color:red">field 参数必须是数组</b>');
        }
        $this->_field_name = implode(',', $params);
    
        return $this;
    }

    /**
     * 连表
     *
     * @param    array
     *
     * @return   object
     **/
    public function join($params)
    {
        if (!is_array($params)) {
            throw new \Exception('<b style="color:red">join 参数必须是数组</b>');
        }

        $this->_join = implode(' ', $params);

        return $this;
    }
    

    /**
     * 排序
     *
     * @param    array
     *
     * return   object
     **/
    public function sort($params)
    {
        if (!is_array($params)) {
            throw new \Exception('<b style="color:red">sort 字段必须是数组</b>');
        }

        $this->_sort = implode(',', $params);

        return $this;
    }

    /**
     * 条件
     *
     * @param    array
     *
     * @return   object
     **/
    public function where($params)
    {
        if (!is_array($params)) {
            throw new \Exception('<b style="color:red">条件必须是数组</b>');
        }

        $this->_where = implode(' AND ', $params);

        return $this;
    }

    /**
     * 分组
     *
     * @param    array
     *
     * @return   object
     **/
    public function group($param)
    {
        if (!is_string($param)) {
            throw new \Exception('<b style="color:red">group 字段必须是字符串格式</b>');
        }

        $this->_group = $param; 

        return $this;
    }

    
    /**
     * 查询多条
     *
     *
     * @return  array
     **/
    public function findAll()
    {
        $sql = "SELECT ".$this->_field_name." FROM `".$this->_table_name."` ";
        
        $this->_bindQueryParams($sql);
        $this->_filterSql();
        $this->_clearParams();
        try {
            $cursor = $this->_conn->query($this->_sql);
            $infos = $cursor->fetchAll();

        } catch (\Exception $e) {
            throw new \Exception('<b style="color:red"> 查询mysql错误: '.$e->getMessage().'</b>');
        }

        if (empty($infos)) {
            return array();
        }
        return $infos;
    }

    /**
     * 查询单条
     *
     *
     * @return  array
     **/
    public function find()
    {
        $sql = "SELECT ".$this->_field_name." FROM `".$this->_table_name."`";
        
        $this->_bindQueryParams($sql);
        $this->_filterSql();
        $this->_clearParams();

        $this->_sql .= " limit 1";
        try {
            $cursor = $this->_conn->query($this->_sql);

            $info = $cursor->fetch();

        } catch (\Exception $e) {
            throw new \Exception('<b style="color:red"> 查询单条数据错误: '.$e->getMessage().'</b>');
        }
        if (empty($info)) {
            return array();
        }
        return $info;
    }

    /**
     * 数量统计
     *
     * @param    string 
     *
     * @return   array
     **/
    public function count($sql = '')
    {
        try {
            if (!empty($sql)) {
                $this->_sql = $sql;
            } else {
                $sql = "SELECT COUNT(".$this->_field_name.") FROM `". $this->_table_name ."`";
                $this->_bindQueryParams($sql);
                $this->_clearParams();
                $this->_sql .= " limit 1";
            }
            $this->_filterSql();
            $count = $this->_conn->query($this->_sql)->fetchColumn(); 

        } catch (\Exception $e) {
            throw new \Exception('<b style="color:red"> 统计数据错误: '.$e->getMessage().'</b>');
        }
        return $count;
    }

    /**
     * sql查询 
     *
     * @param    string 
     *
     * @return   array
     **/
    public function query($sql)
    {
        try {
            $this->_sql = $sql;
            $this->_filterSql();

            $cursor = $this->_conn->query($this->_sql);
            $infos = $cursor->fetchAll();
            if (empty($infos)) {
                return array();
            }
        } catch (\Exception $e) {
            throw new \Exception('<b style="color:red"> 查询mysql错误: '.$e->getMessage().'</b>');
        }

        return $infos;
        
    }

    /**
     * bind查询参数 
     *
     * @param    string 
     *
     * @return   array
     **/
    private function _bindQueryParams($sql)
    {
        $this->_sql = $sql;

        if ($this->_alias) {
            $this->_sql .= $this->_alias.' ';
        }

        if ($this->_join) {
            $this->_sql .= $this->_join;
        }
        
        if ($this->_where) {
            $this->_sql .= " WHERE ".$this->_where;
        }
        if ($this->_sort) {
            $this->_sql .= " ORDER BY ". $this->_sort;
        }
        
    }

    /**
     * 批量插入数据 
     *
     * @param    array
     *
     * @return   int
     **/
    /*public function insertAll($params)
    {
        if (empty($params)) {
            throw new \Exception('<b style="color:red"> insertAll 错误: 参数为空</b>');
        }

        $sql = "INSERT INTO `".$this->_table_name."` ";

        $fields = array();
        $values_location = array();
        foreach ($params[0] as $key => $val) {
            $fields[] = $key;
            $values_location[] = ":".$key;
        }

        $values = array();

        foreach ($params as $key => $val) {
            $values[] = array_combine($values_location, $val);
        }



        $sql .= "(".implode(',', $fields).") VALUES(".implode(',', $values_location).')';

        $this->_sql = $sql;
        $this->_filterSql();
        try {
            $cursor = $this->_conn->prepare($this->_sql);
            $cursor->execute($values);
        } catch (\Exception $e) {
            throw new \Exception('<b style="color:red"> execute mysql错误: '.$e->getMessage().'</b>');
        }

        return $this->_conn->lastinsertid();
        var_dump($params);exit;
    }*/

    public function insertAll($params, $operate_type = '')
    {
        if (empty($params)) {
            throw new \Exception('<b style="color:red"> insertAll 错误: 参数为空</b>');
        }

        if (!empty($operate_type) && isset($this->_operate_types[$operate_type])) {
            $sql = "INSERT ".$this->_operate_types[$operate_type]."  INTO `".$this->_table_name."` ";
        } else {
            $sql = "INSERT INTO `".$this->_table_name."` ";
        }

        $fields = array();
        foreach ($params[0] as $key => $val) {
            $fields[] = $key;
        }

        $values = array();

        foreach ($params as $key => $val) {
            $values[] = "('".implode("','", $val)."')"; 
        }


        $sql .= "(".implode(',', $fields).") VALUES ".implode(',', $values).';';

        $this->_sql = $sql;
        $this->_filterSql();
        try {
            $result = $this->_conn->exec($this->_sql);
        } catch (\Exception $e) {
            throw new \Exception('<b style="color:red"> insertAll错误: '.$e->getMessage().'</b>');
        }

        return $this->_conn->lastinsertid();
    }


    /**
     * 插入单条数据
     *
     * @param    string 
     *
     * @return   int
     **/
    public function insert($params, $operate_type = '')
    {
        if (empty($params)) {
            throw new \Exception('<b style="color:red"> insert 错误: 参数为空</b>');
        }

        if (!empty($operate_type) && isset($this->_operate_types[$operate_type])) {
            $sql = "INSERT ".$this->_operate_types[$operate_type]."  INTO `".$this->_table_name."` ";
        } else {
            $sql = "INSERT INTO `".$this->_table_name."` ";
        }
        $fields = array();
        $values_location = array();
        $values = array();
        foreach ($params as $key => $val) {
            $fields[] = $key;
            $values_location[] = ':'.$key;
            $values[":".$key] = $val;
        }


        $sql .= "(".implode(',', $fields).") VALUES(".implode(',', $values_location).')';

        $this->_sql = $sql;
        $this->_filterSql();

        try {
            $cursor = $this->_conn->prepare($sql);
            $cursor->execute($values);
        } catch (\Exception $e) {
            throw new \Exception('<b style="color:red"> insert错误: '.$e->getMessage().'</b>');
        }

        return $this->_conn->lastinsertid();
    }

    /**
     * 执行sql插入 
     *
     * @param    string 
     *
     * @return   int
     **/
    public function insertSql($sql)
    {
        $this->_sql = $sql;
        $this->_filterSql();
        try {
            $result = $this->_conn->exec($this->_sql);
        } catch (\Exception $e) {
            throw new \Exception('<b style="color:red"> insertSql错误: '.$e->getMessage().'</b>');
        }

        return $this->_conn->lastinsertid();
    }

    /**
     * 更新记录
     *
     * @param    string 
     *
     * @return   int
     **/
    public function update($params)
    {
        if (empty($params)) {
            throw new \Exception('<b style="color:red"> update 错误: 参数为空</b>');
        }

        if (empty($this->_where)) {
            throw new \Exception('<b style="color:red"> update 错误: where条件不能为空</b>');
        }
        $this->_sql = "UPDATE `".$this->_table_name."` ";
        
        if ($this->_alias) {
            $this->_sql .= $this->_alias.' ';
        }

        if ($this->_join) {
            $this->_sql .= $this->_join;
        }

        $this->_sql .= " SET ";
        

        $fields = array();
        $values_location = array();
        $values = array();
        foreach ($params as $key => $val) {
            $fields[] = $key;
            $values_location[] = $key .' = :'.$key;
            $values[':'.$key] = $val;
        }

        $this->_sql .= implode (', ',$values_location);

        if ($this->_where) {
            $this->_sql .= " WHERE ".$this->_where;
        }


        $this->_filterSql();
        $this->_clearParams();
        try {
            $cursor = $this->_conn->prepare($this->_sql);
            $cursor->execute($values);
        } catch (\Exception $e) {
            throw new \Exception('<b style="color:red"> update 错误: '.$e->getMessage().'</b>');
        }

        return $cursor->rowcount();
    }
    /**
     * 执行sql 更新
     *
     * @param    string 
     *
     * @return   array
     **/
    public function updateSql($sql, $params)
    {

        if (empty($params)) {
            throw new \Exception('<b style="color:red"> updatesql错误: 参数为空</b>');
        }
        $this->_sql = $sql;
        $this->_filterSql();
        try {
            $cursor = $this->_conn->prepare($this->_sql);
            $cursor->execute($params);
        } catch (\Exception $e) {
            throw new \Exception('<b style="color:red"> updateSql 错误: '.$e->getMessage().'</b>');
        }

        return $cursor->rowcount();
    }

    /**
     * 执行sql
     *
     * @param    string 
     *
     * @return   int
     **/
    public function execute($sql)
    {
        $this->_sql = $sql;
        $this->_filterSql();
        try {
            $result = $this->_conn->exec($this->_sql);
        } catch (\Exception $e) {
            throw new \Exception('<b style="color:red"> execute mysql错误: '.$e->getMessage().'</b>');
        }

        return true;
    }

    /**
     * 删除数据
     *
     *
     * @return   int
     **/
    public function delete()
    {
        $sql = "DELETE FROM `".$this->_table_name."`";
        if (!$this->_where) {
            throw new \Exception('<b style="color:red"> delete错误: 条件不能为空</b>');
        }
        
        $this->_bindQueryParams($sql);
        $this->_filterSql();
        $this->_clearParams();

        try {
            $result = $this->_conn->exec($this->_sql);
        } catch (\Exception $e) {
            throw new \Exception('<b style="color:red"> delete错误: '.$e->getMessage().'</b>');
        }

        return true;
    }
    

    /**
     * 开启事务
     *
     * @return  void 
     **/
    public function startTrans()
    {
        if ($this->_trans_times > 0) {
            $this->_conn->commit();
        }
        if ($this->_trans_times == 0) {
            $this->_conn->beginTransaction();
        }

        $this->_trans_times ++;
    }

    /**
     * 提交事务 
     *
     * @return   void
     **/
    public function commit()
    {
        if ($this->_trans_times > 0) {
            $this->_trans_times = 0;
            $this->_conn->commit();
        }
    }

    /**
     * 回滚 
     *
     * @return   void
     **/
    public function rollback()
    {
        $this->_conn->rollback();
    }

    /**
     * 清除参数
     *
     * @return  void 
     **/
    private function _clearParams()
    {
        $this->_field_name = '*';
        $this->_join       = '';
        $this->_where      = '';
        $this->_group      = '';
        $this->_sort       = '';
        $this->_alias      = '';
    }
    
    /**
     * sql过滤
     *
     * @return  void 
     **/
    private function _filterSql()
    {
        $this->_sql = htmlentities($this->_sql);
    }

    /**
     * 获取sql语句
     *
     * @return  void 
     **/
    public function getSql()
    {
        return $this->_sql;
    }

    public function __destruct()
    {
    }
}

