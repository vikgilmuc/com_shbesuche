<?php


class ShAdminView extends JViewLegacy {

    function display($tpl = null)
    {
    	$jversion = new JVersion();

    	//(version_compare($jversion->getShortVersion(),'3.1.5') == -1)
    	if (version_compare($jversion->getShortVersion(),'3.1.5') == -1) {
    		//load mootools normally.
    		Jhtml::_('behavior.framework');
			Jhtml::_('behavior.framework','more');

            //also need to push boostrap style sheets and load jQuery and set jQuery noConflict mode.
            $doc = JFactory::getDocument();
            $doc->addStyleSheet(JURI::root(false).'components/com_pbbooking/pbframe/bootstrap-customised-2.5.css');
            $doc->addScript(JURI::root(false).'components/com_pbbooking/pbframe/jquery-1.9.1.original.min.js');
            $doc->addScript(JURI::root(false).'components/com_pbbooking/pbframe/jquery-noconflict.js');
            $doc->addScript(JURI::root(false).'components/com_pbbooking/pbframe/bootstrap/js/bootstrap.min.js');
            /*$doc->addStyleSheet(JURI::root(false).'components/com_pbbooking/pbframe/bootstrap-overrides-2.5.css');*/



    	} else {
    		//version >= 3.1.5 load the jscript frameworks in the normal fashion.
			JHtml::_('jquery.framework');
			Jhtml::_('behavior.framework');
			Jhtml::_('behavior.framework','more');
		}
		parent::display($tpl);
    }


}


?>