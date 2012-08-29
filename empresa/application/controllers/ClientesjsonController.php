<?php

class ClientesjsonController extends Zend_Rest_Controller
{
    
    // LA CLAVE:
    //http://www.codeproject.com/Articles/424665/A-pragmatic-introduction-to-DOJO-for-form-based-CR
    
    public function init()
    {
    	//desactivamos la vista para todos los metodos usando el init
    	$this->_helper->viewRenderer->setNoRender(true);
    }
    
    // PARA USAR CON ENCABEZADO GET (ej: empresa/clientesjson)
    // No es necesario usuar RESTClient (firefox), basta con probar en el navegador
	public function indexAction() {
	    $clientes =new Application_Model_Clientes();
	    // Este codigo es para implemetar busqueda
	    $busqueda = $this->_getParam('nombre');	    
	    //$busqueda = substr($busqueda, 0, -1); //bug, quito * al final del string

	    $sort='id';
	    if($this->_hasParam('sort(-id)')) $sort='id DESC';	    
	    if($this->_hasParam('sort(-nombre)')) $sort='nombre DESC';
	    if($this->_hasParam('sort(+nombre)')) $sort='nombre';
	    if($this->_hasParam('sort(-telefono)')) $sort='telefono DESC';
	    if($this->_hasParam('sort(+telefono)')) $sort='telefono';
	    if($this->_hasParam('sort(-nacimiento)')) $sort='nacimiento DESC';
	    if($this->_hasParam('sort(+nacimiento)')) $sort='nacimiento';	    
	    
		$range = $this->getRequest()->getHeader('Range');//Extraigo el cabezal (Head) del Rango enviado por dojo 
		$offset = 0; 
		$fin = 0;
		sscanf($range, "items=%d-%d", $offset, $fin); //Extraigo la fila de inicio y fin para la paginacion (Ver en firebug)
		
		//$items=$clientes->getItems($this->getRequest()->getQuery(), $start, $end, $busqueda);
		$count=$fin-$offset+1; //Obtengo cantidad de filas por encima del registro de inicio (offset) y le sumo 1 (corrige un bug el 1)
		$items=$clientes->getItems($count,$offset,$sort,$busqueda);		 
		if ($range)
		{
			$total = $clientes->getItemsCount($busqueda);
			$this->getResponse()->setHeader("Content-Range", 'items '.$offset.'-'.$fin.'/'.$total);
		}
		$this->getHelper('json')->sendJson($items);
	}

	// PARA USAR CON ENCABEZADO GET (ej: empresa/clientesjson/2)
	// No es necesario usuar RESTClient (firefox), basta con probar en el navegador	
	// Traemos un solo registro segun el id y convertimos a JSON
	public function getAction() {
	    $clientes =new Application_Model_Clientes();
		$id = $this->_getParam('id');
		$item = $clientes->getItemById($id);
		$this->getHelper('json')->sendJson($item);
	}

	// PARA USAR CON ENCABEZADO POST (AGREGAR) (ej: empresa/clientesjson)
	// UN BODY DE EJEMPLO PARA ENVIAR SERIA: {"nombre":"Gabriel","telefono":"998442681","nacimiento":"03\/10\/1984"}
	//Para probar esto hay que usar RESTClient (firefox) en metodo POST
	//Decodificamos el el nuevo objeto JSON para poder entregarselo al modelo y luego
	//generamos un encabesado de respuesta HTML con codigo "201 Created"
	//Devolvemos tambien le objeto ya creado al cliente
	public function postAction() {
		$item = Zend_Json::decode($this->getRequest()->getRawBody());
		if (!$item) {
			throw new Exception("Debe ingresar un item...");
		}
		
		$clientes =new Application_Model_Clientes();
		$item = $clientes->create($item);
		$location = "/".$this->getRequest()->getControllerName()."/".$item['id'];
			

		
		
		$this->getResponse()->setHttpResponseCode(201);
		$this->getResponse()->setHeader("Location", $location);
		$this->getHelper('json')->sendJson($item);
	}

	// PARA USAR CON ENCABEZADO PUT (ACTUALIZAR) (ej: empresa/clientesjson/2)
	// UN BODY DE EJEMPLO PARA ENVIAR SERIA: {"nombre":"Gabo"}
	//Lo mismo que el anterior pero para actualizar un elemento segun el id
	public function putAction() {
		if (!$id = $this->_getParam('id', false)) {
			throw new Exception("Debe especificar un item para actualizar. Porque el parametro id contiene: ".$id);
		}			
		$item = Zend_Json::decode($this->getRequest()->getRawBody());
		if (!$item) {
			throw new Exception("Debe ingresar un item...");
		}
		$clientes =new Application_Model_Clientes();
		$item = $clientes->update($id, $item);
		$this->getHelper('json')->sendJson($item);
	}

	// PARA USAR CON ENCABEZADO DELETE (BORRAR) (ej: empresa/clientes/2/)
	//Para probar esto hay que usar RESTClient (firefox) en metodo DELETE
	//Solo se borra un elemnto segun el id y se etablece el encabezado HTML a estado “204 No Content”
	public function deleteAction() {
		if (!$id = $this->_getParam('id', false)) {
			throw new Exception("Debe eliminar un item especifico");
		}
		$clientes =new Application_Model_Clientes();
		$clientes->delete($id);
		$this->getResponse()->setHttpResponseCode(204);
	}
}
