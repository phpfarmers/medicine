<?php
/**
 * 测试mysql操作
 *
 *
 *
 */
namespace Pt\Dao;

class TestMysqlModel extends \Pt\Dao\BaseModel
{
    public function __construct() 
    {
        parent::__construct();
        $this->_table = \Db\Mysql::table('ceshi');
    }   
    

    public function testFindAll() 
    {

        $_condition[] = "id=2";
        //$infos = $this->_table->field(array('id', 'title' ))->where($_condition)->sort(array('id desc'))->findAll();
        $infos = $this->_table->alias('c')->field(array('c.id', 'c.title', 'content', 'content1'))->join(array('LEFT JOIN `ceshi2` c2 ON c.id=c2.ceshi_id'))->where(array('c.id=2',))->group('c.id')->findAll();
        return $infos;
    }

    public function testFind()
    {

        $info  = $this->_table->field(array('id', 'title' ))->where($_condition)->sort(array('id desc'))->find();
        echo '获取单条数据:';
        var_print($info);
    }
    
    public function testCount()
    {
        //$count  = $this->_table->alias('c')->field(array('c.id'))->join(array('LEFT JOIN `ceshi2` c2 ON c.id=c2.ceshi_id'))->where(array())->group('c.id')->count();
        //echo $count;
        $count = $this->_table->count("select id from ceshi");
    }

    public function testQuery()
    {
        \Db\Mysql::connect('default')->query("select * from ceshi");
        var_print($infos);
        echo "统计行数是:".$table->count().'<br>';
    }

    public function testInsert()
    {
        /*$this->_table->startTrans();
        $sql = "insert into ceshi(title, content) values('测试2', '内容2')";

        $id = $this->_table->insertSql($sql);
        echo "id is:".$id;
        //$this->_table->commit();*/

        //$insert_id = $this->_table->insert(array('title' => '测试1234', 'content' => '内容2321'), 1);

        //$insert_id  = $this->_table->insertAll(array(array('title' => 'tc1', 'content' => 'c1'), array('title' => 't2', 'content' => 'c2')));
        //echo "最后插入ID：".$insert_id;
    }

    public function testUpdate()
    {
        $count = $this->_table->where(array('id = 2'))->update(array('title' => '更新测试1', 'content' => '更新内容'));

        $sql = "update ceshi set content=:content WHERE id=:id";

        $row_count = $this->_table->updateSql($sql, array(':content' => 'abc2242333333333', ':id' => '13'));

        var_dump($row_count);
    }

    public function testDelete()
    {
       /* $this->_table->startTrans();
        $this->_table->where(array('id=1'))->delete();
        $this->_table->commit();
        */
    }
}
