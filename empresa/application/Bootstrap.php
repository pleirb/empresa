<?php

class Bootstrap extends Zend_Application_Bootstrap_Bootstrap
{
    protected function _initDoctype()
    {
        $this->bootstrap('view');
        $view = $this->getResource('view');
        $view->doctype('XHTML1_STRICT');
    }
    
    //Inicializamos los Helpers
    protected function _initViewHelpers()
    {
    	$this->bootstrap('view');
    	$view = $this->getResource('view'); //no hacemos del objeto view primero
    	Zend_Dojo::enableView($view); //Este inicializa los ayudtes de dojo (imprescindible !)
    }   
    
    protected function _initRest()
    {
        $front = Zend_Controller_Front::getInstance();        
        $front->registerPlugin(new Zend_Controller_Plugin_PutHandler());
        $router = $front->getRouter();
        
        //Specifying all controllers as RESTful:        
    	$restRoute = new Zend_Rest_Route($front);
    	$router->addRoute('rest', $restRoute);
    }
}

