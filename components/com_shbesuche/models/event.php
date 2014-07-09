<?php

/**
* @package		PurpleBeanie.PBBooking
* @license		GNU General Public License version 2 or later; see LICENSE.txt
* @link		http://www.purplebeanie.com
*/

//class definition for event
class event

{
	public $summary;
	public $dtend;
	public $dtstart;
	public $kTimeslots;
	public $recurring;
	public $description;
	public $id;
	public $r_int;
	public $r_end;
	public $r_freq;
	
	/**
	* Default constructor for event.
	*
	* @optional int An event_id to load from the database
	* @return Event the event
	*/
	
	function __construct($id = null)
	{		
		//load and set timezone
		$db = JFactory::getDBO();
		$config =JFactory::getConfig();
    	date_default_timezone_set($config->get('offset'));			
		if ($id) {
			$db->setQuery("select * from #__shbesuche_events where id = ".$db->escape($id));
			$event = $db->loadObject();
			if ($event) {
				$this->summary = $event->summary;
				$this->dtend = date_create(date(DATE_ATOM,$event->dtend),new DateTimeZone(SHBESUCHE_TIMEZONE));
				$this->dtstart = date_create(date(DATE_ATOM,$event->dtstart),new DateTimeZone(SHBESUCHE_TIMEZONE));
				$this->description = $event->description;
				$this->id = $event->id;
				$this->booking_id = $event->booking_id; //specific for TDG
				$this->r_int = $event->r_int;
				$this->r_freq = $event->r_freq;
				$this->r_end = $event->r_end;
			}
		}
	}
	
	/**
	* Method to delete the current event
	*
	* @return bool succesful or not...
	*/
	
	function delete()
	{
		$db = JFactory::getDBO();
		$db->setQuery("delete from #__pbbooking_events where id = ".$db->escape($this->id));
		
		//specfic for TDG, need to update link to booking
		if ($db->query()) {
			$db->setQuery('select * from #__pbbooking_pending where id = '.$db->escape($this->booking_id));
			$booking = $db->loadObject();
			$linked_bookings_arr = explode(',',$booking->linked_bookings);
			$new_linked_bookings_arr = array();
			foreach($linked_bookings_arr as $linked_booking) {
				if ($linked_booking != $this->id) {
					array_push($new_linked_bookings_arr,$linked_booking);
				}
			}
			$db->setQuery("update #__pbbooking_pending set linked_bookings = '".implode(',',$new_linked_bookings_arr)."' where id = ".$booking->id);
			$db->query();
			
			return true;
		} 
	}
	
	function setProperties($date,$timeslot,$duration,$summary) {
		
		$db = JFactory::getDBO();
		$db->setQuery("select * from #__pbbooking_slots where id=".$timeslot);
		$booked_slot = $db->loadObject();
		
		$this->summary = $summary;
		$dtstart = date_create($date->format(DATE_ATOM),new DateTimeZone(SHBESUCHE_TIMEZONE));
		$dtstart->setTime($booked_slot->start_hour,$booked_slot->start_min);
		$dtend = date_create($dtstart->format(DATE_ATOM),new DateTimeZone(SHBESUCHE_TIMEZONE));
		$dtend->modify('+'.$duration.' minutes');
		$this->dtend = $dtend;
		$this->dtstart = $dtstart;
	}
	
	function setDescription($customfields) {
		#function to set the description to contact a list of custom fields
		if (count($customfields>0)) {
			foreach ($customfields as $field) {
				$this->description .= sprintf('%s = %s\n',$field['varname'],$field['data']);
			}
		}
	}
	
	function getEvent($event_id) {
		$db = JFactory::getDBO();
		$db->setQuery("select * from #__pbbooking_events where id=".$db->escape($event_id));
		$event = $db->loadObject();
		return $event;
	}

	/**
	* returns the events summary as defined by the admin config
	* 
	* @return string
	* @since 2.3
	*/

	public function admin_summary()
	{
		$db = JFactory::getDbo();
		$config = $db->setQuery('select * from #__shbesuche_config')->loadObject();

		if ($config->manage_fields != '') {
			//user wants a custom summary
			$event = $db->setQuery('select * from #__pbbooking_events where id = '.(int)$this->id)->loadObject();

			$admin_array = array();						//holds the admin fields
			if ($event->customfields_data && $event->customfields_data != '') {
				foreach (json_decode($event->customfields_data,true) as $field) {
					if (in_array($field['varname'],json_decode($config->manage_fields))) {
						$admin_array[] = $field['data'];
					}
				}
			} else {
				//no custom fields, stuck with the default
				return $this->summary;
			}

			//check if the they want the service details in the manage diaries.
			if (in_array('_service_',json_decode($config->manage_fields))) {
				//we need to add the service
				$service = $db->setQuery('select * from #__pbbooking_treatments where id = '.(int)$event->service_id)->loadObject();
				$admin_array[] = '<br/>'.strtoupper($service->name);
			}

			return implode(' ', $admin_array);
		
		} else {

			//we're happy with the standard summary
			return $this->summary;

		}
	}

}

?>