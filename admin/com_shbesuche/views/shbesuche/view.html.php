<?php

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die();
 
jimport( 'joomla.application.component.view' );
 

class ShbesucheViewShbesuche extends JViewLegacy
{
   
    function display($tpl = null)
    {
        JToolBarHelper::title( JText::_( 'PSHBesuche Manager' ), 'generic.png' );
 
  
        parent::display($tpl);
    }
}