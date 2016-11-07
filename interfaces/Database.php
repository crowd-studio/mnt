<?php

/**
 * Singleton class
 *
 */
final class Database
{
	protected static $instance = null;
    protected $conn = null;
    /**
     * Call this method to get singleton
     *
     * @return UserFactory
     */
    public static function Instance()
    {
        if ($this->inst === null) {
            $this->inst = new Database();
        }
        return $this->inst;
    }

    /**
     * Private ctor so nobody else can instance it
     *
     */
    protected function __construct(){}

    /**
     * Private ctor so nobody else can instance it
     *
     */
    protected function __clone(){}

    public function setConnection($conn){
        $this->conn = $conn;
    }

    public function fetch($sql, $model, $params = []){
        $res = $this->conn->fetchAssoc($sql, $params);

        $final = [];
        foreach ($res as $obj) {
            $final[] = new $model($obj);
        }

        return $final;
    }

    public function fetchOne($model, $id){
        $sql = 'SELECT * FROM ' . $model . ' WHERE id = :id';
        $params = [(int) $id];

        $res = $this->fetch($sql, $model, $params);

        return (isset($res[0])) ? $res[0] : null;
    }

    public function fetchPag($sql, $model, $page, $pageSize, $params){
        $total = $this->fetch($sql, $model, $params);

        $offset = $page * $pageSize;
        $sql .= ' LIMIT ' . $pageSize . ' OFFSET ' . $offset;

        $content = array_slice($total, $offset, $pageSize, true);

        $hasNewPages = count($total) / $pageSize - $params['page'] > 1;

        return ['content' => $content, 'hasNewPages' => $hasNewPages, 'size' => $pageSize];
    }

    public function buildQuery($model, $params = []){
        $sql = 'SELECT * FROM ' . $model . ' WHERE 1=1';

        if(isset($params['where'])){
            foreach ($params['where'] as $where) {
                 $sql .= ' AND ' . $where['column'] . ' ' . $where['operator'] . ' ' . $where['value'];
             } 
        }

        if(isset($params['order'])){
            $sql .= ' ORDER BY ';
            foreach ($params['order'] as $order) {
                 $sql .= $order['order'].' '. $order['orderDir'].', ';
             } 

             $sql = trim($sql, ', ');
        }

    }
}