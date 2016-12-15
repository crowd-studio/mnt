<?php

class SuperModel
{
    protected $database;
    protected $limit = 6;
    protected $model = null;

    public function __construct($params = false){
        if($params){
            $this->_mount($params);
        }

        $this->database = Database::getInstance();
        $this->model = get_class($this);
    }

    /**
     * Return one model object stored in database
     * @param  integer $id 
     * @return object
     */
    protected function _getOne($id){
        return $this->database->fetchOne($this->model, $id);
    }

    /**
     * Returns all model objects stored in database
     * @return array of objects
     */
    protected function _getAll(){
        $sql = $this->database->buildQuery($this->model);

        return $this->database->fetch($sql, $this->model);
    }

    /**
     * Returns model objects filtered by gived params
     * @param  array  $params [OPTIONAL] associative array with pair values column_name => value
     * @return array of objects
     */
    protected function _getFiltered($params = []){
        $sql = $this->database->buildQuery($this->model, $params);

        return $this->database->fetch($sql, $this->model);
    }

    /**
     * Returns model objects limited by limit and offset by page * limit
     * @param  [type]  $page   Page requested
     * @param  array   $params [OPTIONAL] filter params
     * @param  integer $limit  [OPTIONAL] page size
     * @return array            You get array with object contents, boolean haveNewPages and integer limit
     */
    protected function _getByPage($page, $params = [], $limit = false){
        $sql = $this->database->buildQuery($this->model, $params);

        return $this->database->fetchPag($sql, $this->model, $page, ($limit) ? $limit : $this->limit){
    }

    /**
     * Inserts the current object to the database
     * @return integer New Id
     */
    protected function _insert(){
        return $this->database->insert($this->model, $this->_dismount());
    }

    /**
     * Modifies the current object on database
     * @param  array  $col [optional] associative array with pair values column_name => value
     * @return boolean Returns true if object is correctly modified
     */
    protected function _update($col = []){
        if(count($col) == 0){
            $col = $this->_dismount();
        } 

        return $this->database->modify($this->model, $col, ['id' => $this->getId()]);
    }

    /**
     * Deletes the current object on database
     * @return boolean Returns true if object is correctly deleted
     */
    protected function _delete(){
        return $this->database->delete($this->model, ['id' => $this->getId()]);
    }

    /**
     * Converts the current object to associative array
     * @return array
     */
    protected function _dismount() {
        $reflectionClass = new ReflectionClass(get_class($this));
        $array = [];
        foreach ($reflectionClass->getProperties() as $property) {
            $property->setAccessible(true);
            $array[$property->getName()] = $property->getValue($this);
            $property->setAccessible(false);
        }
        return $array;
    }

    /**
     * Sets the object with the given params
     * @param  array $params associative array with pair values column_name => value
     */
    private function _mount($params){
        foreach ($params as $key => $value) {
            $method = 'set' . ucfirst($key);
            if(method_exists($this, $method)){
                $this->$method($value);    
            }
        }
    }
}
