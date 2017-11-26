<?php
namespace Db;

/**
 * mongo
 *
 * @return   void
 **/
class Mysql
{
    private static $_conn_arrs = array();

    private function __construct()
    {
    }

    public static function conn($table_name, $table_prefix = false)
    {
        $config = \Yaf\Registry::get('config');
        $db_config = $config->db['mysql']['default'];
        
        if ($table_prefix) {
            $table_name = $db_config['table_prefix'].$table_name;
        }


        list($conn, $link_id) = self::connectDb($db_config);

        $table_conn = \Db\Driver\Mysqldb::table($table_name, $conn, $link_id);
        return $table_conn;
    }

    public static function table($table_name)
    {
       return self::conn($table_name); 
    }

    public static function name($table_name)
    {
       return self::conn($table_name, true); 
    }

    public static function connect($link_config)
    {
        $config = \Yaf\Registry::get('config');
        
        $db_config = $config->db['mysql'][$link_config];
        if (empty($db_config)) {
            throw new \Exception('<b color="red">没有定义手动连接数据库的配置</b>');
        }

        list($conn, $link_id) = self::connectDb($db_config);

        return \Db\Driver\Mysqldb::table(null, $conn, $link_id);
        
    }

    public static function connectDb($db_config)
    {
        $link_id = $db_config['host'].'_'.$db_config['dbname']; 
        if (!isset(self::$_conn_arrs[$link_id])) {
            $options = array(
                \PDO::ATTR_ERRMODE            => \PDO::ERRMODE_EXCEPTION,
                \PDO::ATTR_DEFAULT_FETCH_MODE => \PDO::FETCH_ASSOC,
                \PDO::ATTR_PERSISTENT         => true,
            );
            try {
                self::$_conn_arrs[$link_id] = new \PDO("mysql:host=".$db_config['host'].";dbname=".$db_config['dbname'], $db_config['user'], $db_config['password'], $options);
            } catch (\PDOException $e) {
                throw new \Exception('<b style="color:red">数据库连接失败:'.$e->getMessage().'</b>');
                exit;
            }
        }

        return array(self::$_conn_arrs[$link_id], $link_id);
    }
}

