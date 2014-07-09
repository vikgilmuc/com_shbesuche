<?php

defined('_JEXEC') or die;


jimport('joomla.application.component.modellist');


class ShBesucheModelShBesuche extends JModelList
{
  
  protected function getListQuery()
  {
   
    $db = $this->getDbo();
    $query = $db->getQuery(true);

    
    $query->from('#__shbesuche');

    
    $query->select('*');

    
    return $query;
  }

}
