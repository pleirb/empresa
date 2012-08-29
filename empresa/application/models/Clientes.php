<?php
class Application_Model_Clientes extends Zend_Db_Table_Abstract
{
    protected $_name = 'clientes';
    protected $_primary ='id';    
   
    //public function getItems($filter, $start, $end, $busqueda)
    public function getItems($count,$offset,$spec,$nombre)
    {        
        $select = $this->_db->select();
        //Selecciono la tabla y ajusto la salida del campo nacimiento que es una fecha (MUY IMPORTANTE PARA LA GUI)
        $select->from($this->_name,array("*","nacimiento" => new Zend_Db_Expr("DATE_FORMAT(nacimiento,'%d/%m/%Y')")));
		/*
    	foreach($filter as $key => $val)
    	{
    		if ($val == "null")
    			$select->where($key." IS NULL");
    		else
    			$select->where($key." = ?", $val);
    	}
    	*/
        
        $select->order($spec);
	    $select->limit($count,$offset);
	    $select->where('nombre LIKE ?', "%".$nombre."%");
	    return $select->query()->fetchAll();


    }
    public function getItemsCount($nombre)
    {       
    	$select = $this->_db->select();
    	if($nombre){
    		$select->where('nombre LIKE ?', "%".$nombre."%");
    	}
    	$rows = $select->from($this->_name, array('count(*) as total'))->query()->fetchAll();    	
    	return $rows[0]['total'];
    }
    public function getItemById($id)
    {
    	$rows = $this->_db->select()->from($this->_name)->where("id = ?", $id)->query()->fetchAll();
    	return $rows[0];
    }
    public function create($data)
    {
    	$this->_db->insert($this->_name, $data);    	
    	return $this->_db->lastInsertId($this->_name);
    }
    public function update($id, $data)
    {
    	$this->_db->update($this->_name, $data, array('id = ?' => $id));
    	return $this->getItemById($id);
    }
    public function delete($id)
    {
    	$this->_db->delete($this->_name, array('id = ?' => $id));
    }
    
}
