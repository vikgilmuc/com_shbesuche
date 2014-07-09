<?php

 

 
defined('_JEXEC') or die();
 
jimport( 'joomla.application.component.view' );
 

class ShbesucheViewConfiguration extends ShAdminView
{
    
    function display($tpl = null)
    {
		$task = JRequest::getVar('task');
		if (!$task || $task=="display") {
        	JToolBarHelper::title( JText::_( 'Shbesuche Manager' ), 'generic.png' );
        	JToolBarHelper::title( JText::_( 'Shbesuche Manager' ), 'generic.png' );
			JToolBarHelper::editList();
			JToolBarHelper::addNew();
			JToolBarHelper::deleteList();
	        
	        
			$this->assignRef( 'calendars', JRequest::getVar('cals'));
		}
		
		if ($task == "edit" || $task == "add") {
			//don't like having this outside the controller but JRequest strips html
			$sql = "select * from #__shbesuche_config";
			$db =JFactory::getDBO();
			$db->setQuery( $sql );
			$configuration = $db->loadAssocList();

			//get the custom fields
			$db->setQuery('select * from #__pbbooking_customfields');
			$this->customfields = $db->loadObjectList();
			
	      	JToolBarHelper::title( JText::_( 'SHbesuche Manager' ), 'generic.png' );
			JToolBarHelper::save();
			JToolBarHelper::cancel('cancel');

			$this->assignRef('configuration',$configuration[0]);
		}

        
	 parent::display($tpl);
    }
}