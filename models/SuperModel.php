<?php

class SuperModel
{
    protected $database;
    protected $limit = 6;
    protected $model = null;

    public function __construct($params = false){
        if($params){
            $this->_factory($params);
        }

        $this->database = Database::getInstance();
        $this->model = get_class($this);
    }

    private function _factory($params){
        foreach ($params as $key => $value) {
            $method = 'set' . ucfirst($key);
            if(method_exists($this, $method)){
                $this->$method($value);    
            }
        }
    }

    protected function _getOne($id){
        return $this->database->fetchOne($this->model, $id);
    }

    protected function _getAll(){
        $sql = $this->database->buildQuery($this->model);

        return $this->database->fetch($sql, $this->model);
    }

    protected function _getFiltered($params = []){
        $sql = $this->database->buildQuery($this->model, $params);

        return $this->database->fetch($sql, $this->model);
    }

    protected function _getByPage($page, $params = []){
        $sql = $this->database->buildQuery($this->model, $params);

        return $this->database->fetchPag($sql, $this->model, $page, $this->limit){
    }
}
