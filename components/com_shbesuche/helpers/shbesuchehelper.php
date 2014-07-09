<?php
defined( '_JEXEC' ) or die( 'Restricted access' );
  

class Shbesuchehelper
{
    static function save_pending_event($data) 
    {
           $db = JFactory::getDbo();
       $config =JFactory::getConfig();
        date_default_timezone_set($config->get('offset'));    
       $dtstart = date_create($data['date'],new DateTimeZone(SHBESUCHE_TIMEZONE));
       $dtend= date_create($data['date'],new DateTimeZone(SHBESUCHE_TIMEZONE));
       $dtend->modify('+30 minutes');
       $now = date_create("now",new DateTimeZone(SHBESUCHE_TIMEZONE));
        
       $sql = sprintf('insert into #__shbesuche_pending (name,nachname,tel,email,dtstart,dtend,dtpending,cal_id ) values  
                    ("%s","%s","%s","%s","%s","%s","%s", 1)',
                        JRequest::getVar('name'),
                        JRequest::getVar('nachname'),
                        JRequest::getVar('tel'),
                        JRequest::getVar('email'),
                        $dtstart->format(DATE_ATOM),
                        $dtend->format(DATE_ATOM),
                        $now->format("Y-m-d H:i:s")
                        );
       $db->setQuery($sql);
       $result = $db->query();
       echo ("saved pending data");
     
          $pending_id = $db->insertid(); 
            
       return $pending_id;
      
    }
    
    
    
    static function validate_pending($pending_id,$email=null,$token=null)
    {
       
       $db = JFactory::getDbo();
       $db->setQuery('UPDATE #__shbesuche_pending SET validated=1 where id = '.$db->escape($pending_id));
        $db->query();
        
        
        $db->setQuery('select * from #__shbesuche_pending where id = '.$db->escape($pending_id));
        $pending = $db->loadObject();
        $appt = new JObject;
        $appt->setProperties(array('name'=>$pending->name,
        'nachname'=>$pending->nachname, 
        'dtstart'=>$pending->dtstart, 
        'dtend'=>$pending->dtend, 
        'cal_id'=>$pending->cal_id,
        'tel'=>$pending->tel)
        );
       
            
         $db = JFactory::getDbo();  
               if ($db->insertObject('#__shbesuche_events',$appt)) {
                    $event_id = $db->insertid();
                   
                    
                    return $event_id;
                    
                    
                }
               
               
               
               }
            
            
            
       
    
    
    
    
   static function email_user($data)
    {
       $db = JFactory::getDbo();
        $db->setQuery('select * from #__shbesuche_config');
        $config = $db->loadObject();
        $db->setQuery('select * from #__pbbooking_customfields where is_email = 1');
        $emailfield = $db->loadObject();
        $db->setQuery('select cf.varname,cfd.data from #__pbbooking_customfields cf,#__pbbooking_customfields_data cfd where cf.id = cfd.customfield_id and cfd.pending_id = 1');
        $customfields = $db->loadObjectList();
        
        $mailer =JFactory::getMailer();
        $mailer_config =JFactory::getConfig();

        $recipient = $data['email'];
        $bcc = null;
        if ($config->bcc_admin == 1) {
            $bcc = array($mailer_config->get('mailfrom'));
            if (isset($cal->email)) $bcc[] = $cal->email;
        }
        if (JURI::base(true) != '')
            $url = str_replace(JURI::base(true).'/','',JURI::base()).JRoute::_('index.php?option=com_shbesuche&task=validate&id='.$data['pending_id'].'&email='.$data['email']);
        else
            $url = preg_replace('/(.*)\/$/','$1',JURI::base()).JRoute::_('index.php?option=com_shbesuche&task=validate&id='.$data['pending_id'].'&email='.$data['email']); 
        
        $urlstring = '<a href="'.$url.'">'.JTEXT::_('COM_SHBESUCHE_VALIDATE_ANCHOR_TEXT')."</a>";
        
        //send email to client to let them know what is going on
        $body = self::_prepare_email('email_body',array('service_id'=>1,'dtstart'=>030303,'url'=>$urlstring),(array)$customfields);
        self::send_email($config->email_subject,$body,$recipient,$bcc);
    } 
    
    private static function _prepare_email($template,$details,$customfields)
    {
        
        $db = JFactory::getDbo();
        $config = $db->setQuery('select * from #__shbesuche_config')->loadObject();
        $service = $db->setQuery('select * from #__pbbooking_treatments where id = '.$db->escape($details['service_id']))->loadObject();

        
        $body = $config->$template;

        //parse custom fields tags
        foreach ($customfields as $customfield) {
            $body = preg_replace('/\|\*'.$customfield->varname.'\*\|/',$customfield->data,$body);
        }
        
        //append service details if required
        $booking_details = '';
        $booking_details .= "<p><table>";
        $booking_details .= "<tr><th>".JTEXT::_('COM_SHBESUCHE_SUCCESS_DATE')."</th><td>".JHtml::_('date',date_create($details['dtstart'],new DateTimeZone(SHBESUCHE_TIMEZONE)),$config->date_format_message);
        $booking_details .= "<tr><th>".JTEXT::_('COM_SHBESUCHE_SUCCESS_TIME')."</th><td>".JHtml::_('date',date_create($details['dtstart'],new DateTimeZone(SHBESUCHE_TIMEZONE)),JText::_('COM_SHBESUCHE_SUCCESS_TIME_FORMAT'))."</td></tr>";
        $booking_details .= "<tr><th>".JTEXT::_('COM_SHBESUCHE_BOOKINGTYPE')."</th><td>".$service->name."</td></tr></table></p>";
        $body = preg_replace('/\|\*booking_details\*\|/',$booking_details,$body);

        //append url string if we have it.....
        $body = preg_replace('/\|\*URL\*\|/',$details['url'],$body);

        //Pbdebug::log_msg('_prepare_email() template after all replacements '.$body,'com_pbbooking');

        //return completed string
        return $body;
    }
        public static function send_email($subject,$body,$recipient,$bcc=null)
    
    {
        //Pbdebug::log_msg('send_email() send email to '.$recipient,'com_pbbooking');
        //Pbdebug::log_msg('send_email() email body = '.$body,'com_pbbooking');

        $mailer =JFactory::getMailer();
        $config =JFactory::getConfig();
        $mailer->setSender(array($config->get('mailfrom'),$config->get('fromname')));
        
        $mailer->addRecipient($recipient);
        $mailer->addBCC($bcc);
        $mailer->setSubject($subject);
        $mailer->isHTML(true);
        
        $mailer->setBody($body);
        $mailer->Send();

        return true;

    }
    
    
    public static function free_appointments_for_day($date)
    {
        $db = JFactory::getDbo();
        $config = $db->setQuery('select * from #__shbesuche_config')->loadObject();
        $cals = $db->setQuery('select * from #__shbesuche_cals')->loadObjectList();
        
        $dt_start = date_create($date->format(DATE_ATOM),new DateTimeZone(SHBESUCHE_TIMEZONE));
        $dt_end = date_create($date->format(DATE_ATOM), new DateTimeZone(SHBESUCHE_TIMEZONE));

        $opening_hours = json_decode($config->trading_hours,true);
        $check_cals = array();
        foreach ($cals as $i=>$cal) {
            $check_cals[$i] = new calendar();
            $check_cals[$i]->loadCalendarFromDbase(array($cal->id));
        }

        //check maybe it's a no trade day?
        if($opening_hours[$date->format('w')]['status']=="closed") { 
            return false;
        }

        $open_times_arr = str_split($opening_hours[$date->format('w')]['open_time'],2);
        $close_times_arr = str_split($opening_hours[$date->format('w')]['close_time'],2);
        $dt_start->setTime($open_times_arr[0],$open_times_arr[1]);
        $dt_end->setTime($close_times_arr[0],$close_times_arr[1]);

        $times_free = false;
        while (($dt_start< $dt_end) && (!$times_free)) {
            $dt_slot_end = date_create($dt_start->format(DATE_ATOM),new DateTimeZone(SHBESUCHE_TIMEZONE));
            $dt_slot_end->modify('+ '.$config->time_increment.' minutes');
            foreach ($check_cals as $cal) {
                if (!$cal->is_free_from_to($dt_start,$dt_slot_end)) {
                    $times_free = true;
                    return true;
                }
            }
            $dt_start->modify('+ '.$config->time_increment.' minutes');
        }

    }
    
    public static function get_calendar_name_for_id($cal_id)
    {
        $db = JFactory::getDbo();
        $db->setQuery('select * from #__pbbooking_cals where id = '.$db->escape($cal_id));
        $calendar = $db->loadObject();

        return $calendar->name;
    }
    public static function booking_for_day($check_date = null)
    {
        $db = JFactory::getDbo();

        //load up all the calendars
        $db->setQuery("select id from #__shbesuche_cals");
        $cals = $db->loadColumn();
        $cal = new calendar();
        $cal->loadCalendarFromDbase($cals); 
        
        $events = $cal->number_of_bookings_for_date($check_date);
        
        return $events;     
    }
    
    public static function is_blocked_date($date)
    {
        $db = JFactory::getDbo();
        $config =JFactory::getConfig();
        date_default_timezone_set($config->get('offset'));  

        $db->setQuery('select * from #__pbbooking_block_days');
        $block_ranges = $db->loadObjectList();
        foreach ($block_ranges as $range) {
            $block_start = date_create($range->block_start_date,new DateTimeZone(SHBESUCHE_TIMEZONE))->setTime(0,0,0);
            $block_end = date_create($range->block_end_date,new DateTimeZone(SHBESUCHE_TIMEZONE))->setTime(23,59,59);
            if ($date >= $block_start && $date<= $block_end) {
                return true;
            }
        }
    }
    }
    
