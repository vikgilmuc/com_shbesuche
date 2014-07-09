<?php
defined('_JEXEC') or die;
jimport('joomla.application.component.controller');
require_once((dirname(__FILE__)).DS.'helpers'.DS.'shbesuchehelper.php');

class ShBesucheController extends JControllerLegacy{
    
    
    function __construct()
    {
        parent::__construct();
        JRequest::setVar('view','shbesuche');
        
        $config =JFactory::getConfig();
        date_default_timezone_set($config->get('offset'));  

        $db = JFactory::getDbo();
        $db->setQuery('select * from #__shbesuche_config');
        $this->config = $db->loadObject();


        //set locale as well...
        //$language = JFactory::getLanguage();
        //$locale = $language->getLocale();       
       // $str_locale = preg_replace(array('/-/','/\.utf8/'),array('_',''),$locale[0]);
       // setlocale(LC_ALL,$str_locale);  

       // Pbdebug::log_msg('Joomla offset = '.$config->get('offset').' PBBooking timezone = '.$this->config->timezone,'com_pbbooking');   
    }
    
    function display($cacheable=false,$options=array()) {
        
        
        $view = $this->getView('SHBesuche', 'html');
       
        $view->setLayout('calendar');
        
        $db = JFactory::getDBO();
        
        $db->setQuery("select * from #__shbesuche_config");
        $view->config = $db->loadObject();
        $view->calendar_message = $view->config->calendar_message;
        $view->now = date_create("now",new DateTimeZone(SHBESUCHE_TIMEZONE));
        if (JRequest::getVar('dateparam')) {
            $view->dateparam = date_create(JRequest::getVar('dateparam'),new DateTimeZone(SHBESUCHE_TIMEZONE));
        } else {
            $view->dateparam = date_create("now",new DateTimeZone(SHBESUCHE_TIMEZONE));
        }
        $config =JFactory::getConfig();
        $view->dateparam->setTimezone(new DateTimezone($config->get('offset')));
        
        if ($view->config->consolidated_view == 0) {
            $db->setQuery("select * from #__shbesuche_cals");
            $cals = $db->loadObjectList();
            foreach ($cals as $cal) {
                $new_cal = new calendar();
                $new_cal->loadCalendarFromDbase(array($cal->id));
                $new_cal->cal_id = $cal->id;
                $new_cal->name = $cal->name;
                $view->cals[]=$new_cal;
            }
        } else {
            $db->setQuery("select id from #__pshbesuche_cals");
            $cals = $db->loadResultArray();
            $view->cal = new calendar();
            $view->cal->loadCalendarFromDbase($cals);   
        }
        
        if ($view->config->block_same_day == 1 && date_create("now",new DateTimeZone(SHBESUCHE_TIMEZONE))->format("z") == $view->dateparam->format("z") ) {
            $view->dateparam->modify("+1 day");
        }
        
        
        $view->display();
        
        
    }

    public function dayview()
    {
        
            $input = JFactory::getApplication()->input;
        $db = JFactory::getDbo();
        $view = $this->getView('shbesuche','html'); 
         
        
        $dateparam = $input->get('dateparam',date_create("now",new DateTimeZone(SHBESUCHE_TIMEZONE))->format(DATE_ATOM),'string');
        $view->dateparam = date_create($dateparam,new DateTimezone(SHBESUCHE_TIMEZONE));
        $cals = $db->setQuery('select * from #__shbesuche_cals')->loadObjectList();
        $config = $db->setQuery('select * from #__shbesuche_config')->loadObject();
        $opening_hours = json_decode($config->trading_hours,true);
        $start_time_arr = str_split($opening_hours[(int)$view->dateparam->format('w')]['open_time'],2);
        $end_time_arr = str_split($opening_hours[(int)$view->dateparam->format('w')]['close_time'],2);

        $view->cals = array();
        $view->opening_hours = $opening_hours[(int)$view->dateparam->format('w')];
        $view->day_dt_start = date_create($dateparam,new DateTimezone(SHBESUCHE_TIMEZONE));
        $view->day_dt_end = date_create($dateparam,new DateTimezone(SHBESUCHE_TIMEZONE));
        $view->day_dt_start->setTime($start_time_arr[0],$start_time_arr[1],0);
        $view->day_dt_end->setTime($end_time_arr[0],$end_time_arr[1],0);
        $view->config = $config;

        //step back one time slot on $view->day_dt_end
        $view->dt_last_slot = clone $view->day_dt_end;
        $view->dt_last_slot->modify('- '.$config->time_increment.' minutes');

        foreach ($cals as $i=>$cal) {
            $view->cals[$i] = new Calendar();
            $view->cals[$i]->loadCalendarFromDbase(array($cal->id)); 
        }

        $view->setLayout('dayview');
        $view->display();
        
    }
    
    public function create()
    {
        $input = JFactory::getApplication()->input;
        $db = JFactory::getDbo();
        $view = $this->getView('shbesuche','html');
        $config = $db->setQuery('select * from #__shbesuche_config')->loadObject();
        
        $dateparam = $input->get('dtstart',date_create('now',new DateTimeZone(SHBESUCHE_TIMEZONE))->format('YmdHi'),'string');
        $cal_id = $input->get('cal_id',0,'integer');
        $opening_hours = json_decode($config->trading_hours,true);
        $closing_time_arr = str_split( $opening_hours[date_create($dateparam,new DateTimezone(SHBESUCHE_TIMEZONE))->format('w')]['close_time'],2 );

        $view->dateparam = date_create($dateparam,new DateTimeZone(SHBESUCHE_TIMEZONE));
        $view->customfields = $db->setQuery('select * from #__pbbooking_customfields')->loadObjectList();
        $view->treatments = $db->setQuery('select * from #__pbbooking_treatments')->loadObjectList();
        $view->cal = new Calendar();
        $view->cal->loadCalendarFromDbase(array((int)$cal_id));
        $view->closing_time = clone $view->dateparam;
        $view->closing_time->setTime((int)$closing_time_arr[0],(int)$closing_time_arr[1],0);
        $view->config = $config;



        $view->setLayout('create');
        $view->display();
    }
    
    function save()
    {

      
        $db =JFactory::getDBO();
        $db->setQuery('select * from #__pbbooking_customfields');
        $customfields = $db->loadObjectList();      
        $db->setQuery('select * from #__shbesuche_config');
        $config = $db->loadObject();
         $data = array();
         
         $data['date'] = JRequest::getVar('date');
        $data['treatment_time'] = JRequest::getVar('treatment_time');
        $data['cal_id'] = JRequest::getInt('cal_id');
        $data['email'] = JRequest::getVar('email');
        
         $pending_id = Shbesuchehelper::save_pending_event($data);
       
        //load up the appointment data in an array.
        $data['pending_id'] = $pending_id;
       
       echo ($data['date']);
        //verify the appointment is actually available 
      
     
      
      Shbesuchehelper::email_user($data);
      
   
      
      
        $view = $this->getView('Shbesuche','html');
                $view->setLayout('success');
                $view->config = $config;
                $db->setQuery('select * from #__shbesuche_pending where id = '.$db->escape($pending_id));
                $view->pending = $db->loadObject();

                //display the view
                $view->display();                    
           
                     
            
            }
    
    function validate() {
        
        $pendingid = JRequest::getVar('id');
        $email = JRequest::getVar('email',null);
        $token = JRequest::getVar('token',null);
         
        $event_id = Shbesuchehelper::validate_pending($pendingid,$email,$token);
        
        
        
        if ($event_id) {
           
           
            //load up the view
            $view = $this->getView('Shbesuche','html');
            $view->setLayout('validated');
            
            //populate the view with data
            $db=JFactory::getDBO();
           
            $db->setQuery('select * from #__shbesuche_events where id= '.$event_id);
            $view->event = $db->loadObject();

            //check if the appt is sent to auto validate as if it is we need to email the user
           $db->setQuery('select * from #__shbesuche_pending where id = '.$pendingid);
                $view->pending = $db->loadObject();
           $db->setQuery('select * from #__shbesuche_config');
            $view->config = $db->loadObject();
            //display the view
            $view->display();
        } else {
            $this->setRedirect('index.php?option=com_pbbooking',JText::_('COM_SHBESUCHE_VALIDATION_PROBLEM'));
        }
    }
    
    

}