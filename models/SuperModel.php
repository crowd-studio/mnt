<?php

class SuperModel
{
    protected $database;

    public function __construct($params = false){
        if($params){
            $this->_factory($params);
        }

        $this->database = Database::getInstance();
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
        return $this->database->fetchOne(get_class($this), $id);
    }

    protected function _getAll(){
        $model = get_class($this);
        return $this->database->fetch($this->database->buildQuery($model), $model);
    }
}
