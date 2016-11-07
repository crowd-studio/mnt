<?php

/**
 * Singleton class
 *
 */
final class Database
{
    /**
     * Class instance
     * @var null
     */
	protected static $inst = null;

    /**
     * \Doctrine\DBAL\Connection instance
     * @var null
     */
    protected static $conn = null;
    
    /**
     * Instance Database static class and sets the connection
     * @param \Doctrine\DBAL\Connection $conn 
     * @return Database
     */
    public static function Instance($conn = false)
    {
        if (self::$inst === null) {
            self::$inst = new Database();
            self::$conn = $conn;
        }
        return self::$inst;
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

    /**
     * Fetch array of objects filtered by provided SQL sentence
     * @param  String $sql    SQL want retrieve from database
     * @param  String $model  Model object 
     * @param  Array  $params Params of SQL
     * @return Array         Array of result objects
     */
    public function fetch($sql, $model, $params = []){
        $res = $this->conn->fetchAssoc($sql, $params);

        $final = [];
        foreach ($res as $obj) {
            $final[] = new $model($obj);
        }

        return $final;
    }

    /**
     * Fetch object 
     * @param  String $model 
     * @param  String $id    
     * @return Object   
     */
    public function fetchOne($model, $id){
        $sql = 'SELECT * FROM ' . $model . ' WHERE id = :id';
        $params = [(int) $id];

        $res = $this->fetch($sql, $model, $params);

        return (isset($res[0])) ? $res[0] : null;
    }

    /**
     * Fetch array of objects filtered by sql and sliced by page and pageSize
     * @param  String $sql      
     * @param  String $model    
     * @param  int    $page     
     * @param  int    $pageSize 
     * @param  array  $params   
     * @return array            
     */
    public function fetchPag($sql, $model, $page, $pageSize, $params = []){
        $total = $this->fetch($sql, $model, $params);

        $offset = $page * $pageSize;
        $sql .= ' LIMIT ' . $pageSize . ' OFFSET ' . $offset;

        $content = array_slice($total, $offset, $pageSize, true);

        $hasNewPages = count($total) / $pageSize - $page > 1;

        return ['content' => $content, 'hasNewPages' => $hasNewPages, 'size' => $pageSize];
    }

    /**
     * Builds a SQL query with provided param array
     * @param  string $model  
     * @param  array  $params Array with optional 'where' and 'order' options.
     * @return string         Builded query
     */
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