<?php



defined( '_JEXEC' ) or die( 'Restricted access' );
 
jimport('joomla.application.component.controller');
 

class ShbesucheController extends JControllerLegacy
{
    
    function display(){
        
         $db = JFactory::getDbo();
        
        $config = $db->setQuery('select * from #__shbesuche_config')->loadObject();

        $view = $this->getView('shbesuche','html');
        $query = $db->getQuery(true);
        $query->select('*')->from('#__shbesuche_events');
        $query->setLimit(10,0);
        $view->upcoming_events = $db->setQuery($query)->loadObjectList();
        
        $query = $db->getQuery(true);
        $query->select('*')->from('#__shbesuche_pending');
        $view->pending_events = $db->setQuery($query)->loadObjectList();
        

        $view->config = $config;

        $view->display();
        
        
        
    }
    
    
}