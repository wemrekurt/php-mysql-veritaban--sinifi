<?php

namespace Globally;

// error_reporting(E_ALL);
// ini_set('display_errors', 1);
date_default_timezone_set('Europe/Istanbul');

/**
 * Class Database for Database works
 * Insert, Select vs. all the things what you need.
 */
class Database {

    /**
     * @var PDO
     */
    public $pdo;

    /**
     * Database constructor.
     * @param $username
     * @param $password
     * @param $options
     */
    public function __construct($host, $dbname, $username, $password){

        $dsn = "mysql:host=$host;dbname=$dbname";
        $options = array(
            PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8'
        );

        try {
            $db = new PDO($dsn, $username, $password, $options);
            $this->pdo = $db;

        } catch (PDOException $e) {
            echo 'Connection failed: ' . $e->getMessage()."\n";
        }

    }

    // Update Time (timestamp)
    /**
     * @return bool|string
     * if update now, use now() function
     */
    public function now(){
        $now=date("Y-m-d H:i:s");
        return $now;
    }


    /**
     * This is using inside of class
     * @param $sql (Sql word)
     * @return array|bool
     *
     */
    function query($sql){
        $results = $this->pdo->query($sql,PDO::FETCH_ASSOC);
        $data = array();

        if($results->rowCount()) {
            foreach ($results as $key => $result) {
                $data[$key] = $result;
            }
            return $data;
        }else{
            return false;
        }

    }


    /**
     * Using inside class
     * Son işlem burası.
     * @param $datas
     * @param $sqlword
     * @return bool|string
     */
    function execute($datas, $sqlword){
        $doit = $sqlword->execute($datas);

        if ($doit) {
            if(!empty($datas['id'])){
                return $datas['id'];
            }else {
                return $this->pdo->lastInsertId();
            }
        }else{
            return $doit;
        }

    }


    /**
     *  Count All records from $table table.
     *  Girdi olarak verilen tablodaki kayıt sayısını döndürür.
     * @param $table
     * @return string
     */
    function countAll($table)
    {

        $sql = 'SELECT COUNT(*) FROM '.$table.'';
        $result = $this->pdo->prepare($sql);
        $result->execute();
        return $result->fetchColumn();

    }


    /**
     * Count one specific with id
     * Tek sorgu, tek kayıt getirir
     * @param $table
     * @param $where
     * @return string
     */
    function countOne($table, $where){

        $sql = 'SELECT COUNT(*) FROM '.$table.' WHERE '.$where[0].'='.$where[1];
        $result = $this->pdo->prepare($sql);
        $result->execute();
        return $result->fetchColumn();

    }

    function count_wParams($table,$params){
        $word = PHP_EOL;
        foreach ($params as $key=>$param){
            $word.= $param[0].'='.$param[1];
            if(count($params)-1 == $key){}else{ $word.=' AND ';}
        }
        $sql = 'SELECT COUNT(*) FROM '.$table.' WHERE '.$word;
        $result = $this->pdo->prepare($sql);
        $result->execute();
        return $result->fetchColumn();
    }

    function selectMonthYear($table,$where,$month,$year,$params = null){
        $wordy = PHP_EOL;
        if(!is_null($params)){
            foreach($params as $key=>$select){
                $wordy.= $select;
                if($key<count($params)-1){ $wordy.=',';}
            }
        }else{
            $wordy.='*';
        }
        $sql = "SELECT $wordy FROM $table WHERE MONTH($where) = $month AND YEAR($where) = $year";
        return $this->query($sql);
        //return $sql;
    }


    /**
     * Select all records from $table order by $order
     * @param $table
     * @param $order
     * @return array|bool
     */
    function selectAll($table, $order){
        $sql = 'SELECT * FROM '.$table.' ORDER BY '.$order[0].' '.$order[1];
        return $this->query($sql);
    }

    /**
     * @param $table
     * @param $order
     * @param $params
     * @return array|bool
     */
    function selectwParams($table, $order, $params){
        $word = PHP_EOL;

        foreach($params as $key=>$param){
            $word.=$param;
            if($key<count($params)-1){ $word.=',';}
        }

        $sql = 'SELECT '.$word.' FROM '.$table.' ORDER BY '.$order[0].' '.$order[1];
        return $this->query($sql);
    }


    /**
     * Select Where
     * @param $table - Table Name
     * @param $order - Order array(by,type)
     * @param $where - Table Where
     * @return array|bool
     */
    function selectWhere($table, $order, $where){
        $wh = '';
        foreach($where as $key=>$wer){
            $wh.= $wer[0].'='.$wer[1];
            if(count($where)-1 == $key){}else{ $wh.=' AND ';}
        }
        $sql = 'SELECT * FROM '.$table.' WHERE '.$wh.' ORDER BY '.$order[0].' '.$order[1];
        return $this->query($sql);

    }

    /**
     * @param $table
     * @param $where
     * @param $order
     * @param $nextday
     * @param null $params
     * @return array|bool
     */
    function selectFromToday($table, $where, $order, $nextday, $params=null){
        $wordy = PHP_EOL;
        if(!is_null($params)){
            foreach($params as $key=>$select){
                $wordy.= $select;
                if($key<count($params)-1){ $wordy.=',';}
            }
        }else{
            $wordy.='*';
        }
        $sql = "SELECT $wordy FROM $table WHERE $where BETWEEN CURRENT_TIMESTAMP() AND DATE_ADD(CURRENT_TIMESTAMP(), INTERVAL $nextday DAY) ORDER BY $order[0] $order[1]";
        return $this->query($sql);
    }


    /**
     * Join two connected tables
     * @param $tables (two tables)
     * @param $iliski (tables' relation)
     * @return array|bool
     */
    function selectJoin($tables, $iliski){
        $sql = 'SELECT * FROM '.$tables[0].' INNER JOIN '.$tables[1].' ON '.$tables[0].'.'.$iliski[0].' = '.$tables[1].'.'.$iliski[1];
        return $this->query($sql);
    }


    /**
     * Insert $datas into $table
     * @param $table
     * @param $datas
     * @return bool|string
     */
    function insert($table, $datas){
        // Create sql word
        $word = ''.PHP_EOL;
        foreach($datas as $key=>$data){
            $word .= $key.' =:'.$key.', ';
        }
        $word .= 'updated_at =:updated_at,created_at=:created_at';

        $sqlword = 'INSERT INTO '.$table.' SET '.$word;
        $insert = $this->pdo->prepare($sqlword);
        $datas['updated_at'] = $this->now();
        $datas['created_at'] = $this->now();

        return $this->execute($datas,$insert);

    }

    /**
     * Delete one record from table with id
     * @param $table
     * @param $id
     * @return bool
     */
    public function delete($table, $id){
        $sql = "DELETE FROM $table WHERE id=:id";
        $delete = $this->pdo->prepare($sql);
        $do = $delete->execute(array('id'=>$id));
        if($do){
            return true;
        }else{
            return false;
        }
    }

    /**
     * @param $table
     * @param $params
     * @return bool
     */
    public function deleteParams($table, $params){
        $word = PHP_EOL;
        $rebuild = array();
        foreach ($params as $key=>$param){
            $rebuild[$param[0]] = $param[1];
            $word.= $param[0].'='.$param[1];
            if(count($params)-1 == $key){}else{ $word.=' AND ';}
        }
        $sql = "DELETE FROM $table WHERE ".$word;
        $delete = $this->pdo->prepare($sql);
        $do = $delete->execute($rebuild);
        if($do){
            return true;
        }else{
            return false;
        }
    }

    /**
     * You just need the tell table name and send datas (only the are inside database)
     * @param $table
     * @param $datas
     * @return bool|string
     */
    public function updateWithId($table, $datas){
        $word = ''.PHP_EOL;
        $word .= "UPDATE $table SET ";
        foreach($datas as $key=>$data){
            if($key != 'id') {
                $word .= $key . ' =:' . $key . ', ';
            }
        }
        $word .= "updated_at=:updated_at WHERE id=:id";
        $datas['updated_at'] = $this->now();
        $update = $this->pdo->prepare($word);
        return $this->execute($datas,$update);
        //return [$word,$datas];

    }


    /**
     * Select records which where
     * @param $table - Select * from $table
     * @param $word - where = $word
     * @param $where - where $where=
     * @return bool|mixed
     */
    function selectOne($table, $word, $where, $selects = null){
        $wordy = PHP_EOL;
        if(!is_null($selects)){
            foreach($selects as $key=>$select){
                $wordy.= $select;
                if($key<count($selects)-1){ $wordy.=',';}
            }
        }else{
            $wordy.='*';
        }
        $sql = 'SELECT '.$wordy.' FROM '.$table.' WHERE '.$where.' = \''.$word.'\'';
        $one = $this->pdo->query($sql)->fetch(PDO::FETCH_ASSOC);

        if ($one){
            return $one;
        }else{
            return false;
        }
    }

    function sumwParams($table,$where,$params=null){
        $word = PHP_EOL;
        if(!is_null($params)) {
            foreach ($params as $key => $param) {
                $word .= $param[0] . '=' . $param[1];
                if (count($params) - 1 == $key) {
                } else {
                    $word .= ' AND ';
                }
            }
            $word = 'WHERE '.$word;
        }
        $sql="SELECT SUM($where) as toplam FROM $table $word";
        return $this->query($sql)[0]['toplam'];


    }

}
