<?php

// No direct access
 
defined( '_JEXEC' ) or die( 'Restricted access' );
 
jimport('joomla.application.component.controller');
 

class ShbesucheControllermanage extends JControllerLegacy
{
	
	function __construct()
	{
	    parent::__construct();
	    
	    $config =JFactory::getConfig();
    	date_default_timezone_set($config->get('offset'));	


		//load the helper method. may not need it but it has good functions for events.
		require_once JPATH_SITE.DS.'components'.DS.'com_shbesuche'.DS.'helpers'.DS.'shbesuchehelper.php';
	}
	
    /**
     * Method to display the view
     *
     * @access    public
     */
    function display($cacheable=false,$options=array())
    {		
		$db = JFactory::getDbo();
		$view = $this->getView('manage','html');
		
		//stuff params
		$view->date = (JRequest::getVar('date')) ? date_create(JRequest::getVar('date'),new DateTimeZone(SHBESUCHE_TIMEZONE)) : date_create("now",new DateTimeZone(SHBESUCHE_TIMEZONE));
		$db->setQuery('select * from #__shbesuche_config');
		$view->config = $db->loadObject();
		$db->setQuery('select * from #__pbbooking_cals');
		$view->cals = $db->loadObjectList();
		$view->cal_objs = array();
		foreach ($view->cals as $cal) {
			$view->cal_objs[$cal->id] = new calendar;
			$view->cal_objs[$cal->id]->loadCalendarFromDbase(array($cal->id));
		}

		//sort out openign and closing times
		$opening_hours = json_decode($view->config->trading_hours,true);
		$opening_time_arr = str_split($opening_hours[$view->date->format('w')]['open_time'],2);
		$closing_time_arr = str_split($opening_hours[$view->date->format('w')]['close_time'],2);
		$view->dt_start = date_create($view->date->format(DATE_ATOM),new DateTimeZone(SHBESUCHE_TIMEZONE));
		$view->dt_end = date_create($view->date->format(DATE_ATOM),new DateTimeZone(SHBESUCHE_TIMEZONE));
		$view->dt_start->setTime((int)$opening_time_arr[0],(int)$opening_time_arr[1]);
		$view->dt_end->setTime((int)$closing_time_arr[0],(int)$closing_time_arr[1]);
		
		//display the view.
		JToolbarHelper::addNew('create');
		$view->display();
    }
    
    
    /**
	* edit - a method to edit an existing event in the database
	*
	* @param int the event id
	*/
	
	function edit()
	{
		$db = JFactory::getDbo();
		
		if ($_SERVER['REQUEST_METHOD'] == "GET") {
			
			//render the edit form
			$view = $this->getView('manage','html');
			
			//load data
			$db->setQuery('select * from #__shbesuche_config');
			$view->config = $db->loadObject();
			$db->setQuery('select * from #__pbbooking_events where id = '.$db->escape(JRequest::getInt('id')));
			$view->event = $db->loadObject();
			$db->setQuery('select * from #__pbbooking_treatments');
			$view->services = $db->loadObjectList();
			$db->setQuery('select * from #__pbbooking_cals');
			$view->cals = $db->loadObjectList();
			if ($view->config->consolidated_view == 1) { 
				$db->setQuery('select * from #__pbbooking_cals where out_cal = 1');
				$view->outcal = $db->loadObject();
			}
			//sort out openign and closing times
			$opening_hours = json_decode($view->config->trading_hours,true);
			$opening_time_arr = str_split($opening_hours[date_create($view->event->dtstart, new DateTimeZone(SHBESUCHE_TIMEZONE))->format('w')]['open_time'],2);
			$closing_time_arr = str_split($opening_hours[date_create($view->event->dtstart, new DateTimeZone(SHBESUCHE_TIMEZONE))->format('w')]['close_time'],2);
			$view->dt_start = date_create($view->event->dtstart,new DateTimeZone(SHBESUCHE_TIMEZONE));
			$view->dt_end = date_create($view->event->dtstart,new DateTimeZone(SHBESUCHE_TIMEZONE));
			$view->dt_start->setTime($opening_time_arr[0],$opening_time_arr[1]);
			$view->dt_end->setTime($closing_time_arr[0],$closing_time_arr[1]);
			
			//display the view
			$view->setLayout('edit_event');
			JToolbarHelper::save('edit',Jtext::_('COM_SHBESUCHE_SAVE_CHANGES'));
			JToolbarHelper::trash('delete',Jtext::_('COM_SHBESUCHE_DELETE_EVENT'),false);
			JToolbarHelper::custom('view_ical','view_ics','',Jtext::_('COM_SHBESUCHE_VIEW_ICS'),false);
			JToolbarHelper::cancel();
			$view->display();
		}
		
		if ($_SERVER['REQUEST_METHOD'] == "POST") {
			$db->setQuery('select * from #__pbbooking_events where id = '.$db->escape(JRequest::getInt('id')));
			$event = $db->loadObject();
			$db->setQuery('select * from #__pbbooking_treatments where id = '.$db->escape($event->service_id));
			$service = $db->loadObject();
			
			$treatment_time = JRequest::getVar('treatment-time');
			$times_arr = str_split($treatment_time,2);
			$dtstart = date_create(JRequest::getVar('date'),new DateTimeZone(SHBESUCHE_TIMEZONE));
			$dtstart->setTime((int)ltrim($times_arr[0],'0'),(int)ltrim($times_arr[1],'0'),0);
			$dtend = date_create($dtstart->format(DATE_ATOM),new DateTimeZone(SHBESUCHE_TIMEZONE));
			$dtend->modify('+'.$service->duration.' minutes');
			
			$event->summary = JRequest::getVar('summary');
			$event->description = JRequest::getVar('description');
			$event->dtstart = $dtstart->format('Y-m-d H:i:s');
			$event->dtend = $dtend->format('Y-m-d H:i:s');


			if ((int)$_POST['reccur'] == 1) {
				$event->r_int = JRequest::getVar('interval');
				$event->r_freq = JRequest::getVar('frequency');
				$event->r_end = JRequest::getVar('recur_end');
 			}	

			if (isset($_POST['cal_id'])) $event->cal_id = JRequest::getInt('cal_id');
			if ($db->updateObject('#__pbbooking_events',$event,'id')) {
				$this->setRedirect('index.php?option=com_pbbooking&controller=manage&date='.JRequest::getVar('date'),Jtext::_('COM_SHBESUCHE_EDIT_SUCCESS'));
			} else {
				echo $db->getErrorMsg();
			}
			
		}
	
	}

	/**
	* create - a method to insert a new record into the database
	*
	*/
	function create()
	{
		$db = JFactory::getDbo();
		
		if ($_SERVER['REQUEST_METHOD'] == "GET") {
			//render the create form
			$view = $this->getView('manage','html');
			
			//load data
			$db->setQuery('select * from #__pbbooking_customfields');
			$view->customfields = $db->loadObjectList();
			$db->setQuery('select* from #__pbbooking_treatments');
			$view->services = $db->loadObjectList();
			$db->setQuery('select * from #__shbesuche_config');
			$view->config = $db->loadObject();
			$view->date = date_create(JRequest::getVar('date'),new DateTimeZone(SHBESUCHE_TIMEZONE));
			$db->setQuery('select * from #__pbbooking_cals');
			$view->cals = $db->loadObjectList();

			//sort out openign and closing times
			$opening_hours = json_decode($view->config->trading_hours,true);
			$opening_time_arr = str_split($opening_hours[$view->date->format('w')]['open_time'],2);
			$closing_time_arr = str_split($opening_hours[$view->date->format('w')]['close_time'],2);
			$view->dt_start = date_create($view->date->format(DATE_ATOM),new DateTimeZone(SHBESUCHE_TIMEZONE));
			$view->dt_end = date_create($view->date->format(DATE_ATOM),new DateTimeZone(SHBESUCHE_TIMEZONE));
			$view->dt_start->setTime($opening_time_arr[0],$opening_time_arr[1]);
			$view->dt_end->setTime($closing_time_arr[0],$closing_time_arr[1]);
	
			//display the view
			$view->setLayout('create_event');
			JToolbarHelper::save('create');
			$view->display();
		}
		if ($_SERVER['REQUEST_METHOD'] == "POST") {
			$_POST['treatment_time'] = $_POST['treatment-time'];
			$event_id = Pbbookinghelper::save_pending_event($_POST);
			if ($event_id) {
				$db->setQuery('select * from #__pbbooking_pending where id='.$event_id);
				$event = $db->loadObject();
				$validated_id = Pbbookinghelper::validate_pending($event_id,$event->email);
				if ($validated_id) {
					if ((int)$_POST['reccur'] == 1) {
						Pbbookinghelper::make_recurring($validated_id,$_POST);
					}
					$this->setRedirect('index.php?option=com_pbbooking&controller=manage&date='.JRequest::getVar('date'),"Event ".$validated_id." has been added.");
				} else {
					$this->setRedirect('index.php?option=com_pbbooking&controller=manage&date='.JRequest::getVar('date'),JText::_('COM_SHBESUCHE_VALIDATION_ERROR').' '.$db->getErrorMsg());
				}
			} else {
				$this->setRedirect('index.php?option=com_pbbooking&controller=manage&date='.JRequest::getVar('date'),JText::_('COM_SHBESUCHE_CREATE_ERROR').' '.$db->getErrorMsg());
			}
		}	
	}
	
	
	
	function delete()
	{
		echo print_r($_POST);
		$db = JFactory::getDbo();
		$db->setQuery('delete from #__pbbooking_events where id = '.$db->escape(JRequest::getVar('id')));
		if ($db->query()) {
			$this->setRedirect('index.php?option=com_pbbooking&controller=manage&date='.JRequest::getVar('date'),JText::_('COM_SHBESUCHE_DELETE_SUCCESFUL'));
		} else {
			$this->setRedirect('index.php?option=com_pbbooking&controller=manage&date='.JRequest::getVar('date'),JText::_('COM_SHBESUCHE_DELETE_PROBLEM').' '.$db->getErrorMsg());
		}
	}
	
	function blockdays()
	{
		$db = JFactory::getDbo();
		
		//get the view
		$view = $this->getView('manage','html');
		$view->setLayout('block_days');
		
		//load up data in the view
		$db->setQuery('select * from #__pbbooking_block_days');
		$view->blocked_days = $db->loadObjectList();
		$db->setQuery('select * from #__shbesuche_config');
		$config=$db->loadObject();
		$view->trading_hours = json_decode($config->trading_hours,true);
		$db->setQuery('select * from #__shbesuche_cals');
		$view->cals = $db->loadObjectList();
						
		//display the view
		JToolBarHelper::save('save_block_days');
		$view->display();
	}
	
	function save_block_days()
	{
		$db = JFactory::getDBO();
		$is_open_arr = JRequest::getVar('is-open');
		$shift_end_arr = JRequest::getVar('shift-end');
		$shift_start_arr = JRequest::getVar('shift-start');
		$shift_label_arr = JRequest::getVar('shift-label');
		$shift_display_label = JRequest::getVar('shift-display-label');

		$opening_hours = array();
		$i=0;
		foreach (array('sunday','monday','tuesday','wednesday','thursday','friday','saturday') as $day) {
			if (in_array($i,$is_open_arr)) {
				$open_time = JRequest::getVar('open-time-'.$i);
				$close_time = JRequest::getVar('close-time-'.$i);
				$opening_hours[$i] = array('status'=>'open','open_time'=>$open_time,'close_time'=>$close_time);
			} else {
				$opening_hours[$i] = array('status'=>'closed');
			}
			$i++;
		}
		
		//dump to opening hours var in config
		$db->setQuery('update #__shbesuche_config set trading_hours = "'.$db->escape(json_encode($opening_hours)).'" where id =1');
		if (!$db->query()) {
			error_log($db->getErrorMsg());
		}
		
		
		//sort out the shifts....
		$i=0;
		$shifts_arr = array();
		foreach ($shift_label_arr as $shift_label) {
			$shifts_arr[$shift_label] = array('display_label'=>$shift_display_label[$i],'shift_start'=>$shift_start_arr[$i],'shift_end'=>$shift_end_arr[$i]);
			$i++;
		}
		$db->setQuery('update #__shbesuche_config set time_groupings = "'.$db->escape(json_encode($shifts_arr)).'" where id=1');
		$db->query();
		
				
		//have i got any block date ranges to deal with?
		if ($_POST['block-from-date'] && $_POST['block-end-date']) {
			$input = JFactory::getApplication()->input;
			$block_start_date = $input->get('block-from-date',null,'string');
			$block_end_date = $input->get('block-end-date',null,'string');
			$block_note = $input->get('block-comment',"No Comment",'string');
			$block_calendars = implode(',',$input->get('block_calendars',null,'array'));
			$make_recurring = $input->get('reccur',0,'integer');
			$r_int = $input->get('interval',0,'integer');
			$r_freq = $input->get('frequency',null,'string');
			$r_end = $input->get('recur_end',null,'string');

			$block = new JObject(array('block_start_date'=>date_create($block_start_date,new DateTimeZone(SHBESUCHE_TIMEZONE))->format(DATE_ATOM),'block_end_date'=>date_create($block_end_date,new DateTimeZone(SHBESUCHE_TIMEZONE))->format(DATE_ATOM),
										'block_note'=>$block_note,'calendars'=>$block_calendars));
			if ($make_recurring == 1) $block->setProperties(array('r_int'=>$r_int,'r_freq'=>$r_freq,'r_end'=>date_create($r_end,new DateTimeZone(SHBESUCHE_TIMEZONE))->format(DATE_ATOM)));
			$db->insertObject('#__pbbooking_block_days',$block);
			
		}
		$this->setRedirect('index.php?option=com_pbbooking&controller=manage&task=blockdays','Settings Saved');
	}
	
	/**
	* Deletes a blocked day fromm the database
	*
	*/
	
	function delete_blocked_day()
	{
		$db = JFactory::getDBO();
		$sql = sprintf('delete from #__pbbooking_block_days where id = %s',$db->escape(JRequest::getVar('id')));
		$db->setQuery($sql);
		$db->query();
		
		$this->setRedirect('index.php?option=com_pbbooking&controller=manage&task=blockdays');
	}
	
	/**
	* view_ical - used to render an ics file that the user can dump into their diary app
	*
	*/
		
	function view_ical()
	{
		
		if (!JRequest::getVar('format') == 'raw') {
			$this->setRedirect(JURI::root(false).'administrator/index.php?option=com_pbbooking&controller=manage&task=view_ical&id='.JRequest::getInt('id').'&format=raw');
			return;
		}
		
		$view=$this->getView('manage','raw');
		$view->setLayout('ics_view');
		
		$db = JFactory::getDbo();

		$db->setQuery('select * from #__pbbooking_events where id='.$db->escape((int)JRequest::getInt('id')));		
		$view->event = $db->loadObject();
		
		$view->display();
	}
	




}