<?php

// No direct access
 
defined( '_JEXEC' ) or die( 'Restricted access' );
 
jimport('joomla.application.component.controller');
 

class ShbesucheControllerconfiguration extends JControllerLegacy
{
	
	function __construct()
	{
	    parent::__construct();

	    // Register Extra tasks
	    $this->registerTask( 'add'  ,     'edit' );
	}
	
    /**
     * Method to display the view
     *
     * @access    public
     */
	function display($cacheable=false,$options=array())    
	{	
		$sql = "select * from #__pbbooking_treatments";
		$db =JFactory::getDBO();
		$db->setQuery( $sql );
		$treatments = $db->loadAssocList();
		JRequest::setVar('treatments',$treatments);
		JRequest::setVar( 'view', 'treatment' );
		JRequest::setVar( 'layout', 'default'  );
		
		
        parent::display();
    }

	function edit()
	{
	    JRequest::setVar( 'view', 'configuration' );
	    JRequest::setVar( 'layout', 'form'  );
	    JRequest::setVar('hidemainmenu', 1);
		
		parent::display();	
	}
	
	function save() 
	{
		$input = JFactory::getApplication()->input;
		$db =JFactory::getDBO();

		//an array of all the fields we need to collect that aren't special sitations
		$fields = array('email_subject'=>'string','block_same_day'=>'integer','show_link'=>'integer','allow_subscribe'=>'integer','allow_publish'=>'integer','use_pb_pub_sec'=>'integer',
						'show_prices'=>'integer','bcc_admin'=>'integer','time_increment'=>'integer','validation'=>'string','calendar_start_day'=>'integer','enable_logging'=>'integer',
						'show_busy_frontend'=>'integer','enable_shifts'=>'integer','currency_symbol_before'=>'integer','admin_validation_confirmed_email_subject'=>'string','admin_validation_pending_email_subject'=>'string');
		

		//get non complex....
		$config = $input->getArray($fields);

		//get standard strings from POST where jinput might mung html / symbols
		foreach(array('email_body','calendar_message','create_message','date_format_heading','date_format_message','date_format_cell','subscribe_secret','publish_username','publish_password','auto_validated_appt_email_subject','auto_validated_appt_body','admin_validation_confirmed_email_body','admin_validation_pending_email_body') as $field) {
			$config[$field] = ($_POST[$field]) ? $_POST[$field] : '';
		}

		//get some specials
		$config['manage_fields'] = (isset($_POST['fields'])) ? json_encode($_POST['fields']) : null;
		

		//check that ints have either saved with a number or 0
		foreach ($fields as $field=>$type) {
			if ($type=='integer' && !(isset($config[$field]))) $config[$field] = 0;
		}

		$id = $input->get('id',0,'integer');		
		
		if ($id !=0) {
			$config['id'] = $id;
			$db->updateObject('#__shbesuche_config',new JObject($config),'id');
		} else {
			$db->insertObject('#__shbesuche_config',new JObject($config));
		}
		
		$this->setRedirect( 'index.php?option=com_shbesuche');
		
	}


	/**
	* a legacy method just to redirect on cancelling a config change
	*/

	public function cancel()
	{
		$this->setRedirect('index.php?option=com_shbesuche');
	}
}