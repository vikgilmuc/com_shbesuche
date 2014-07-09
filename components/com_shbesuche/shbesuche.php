<?php

defined('_JEXEC') or die;

jimport('joomla.application.component.controller');

$controller = JControllerLegacy::getInstance('shbesuche');
$config = JFactory::getConfig();
$app= JFactory::getApplication();
//$input = $app->input;
define('SHBESUCHE_TIMEZONE',$config->get('offset')); 
//$controller->execute($input->get('task'));

require_once(JPATH_COMPONENT.DS.'helpers'.DS.'shfrontview.php');
require_once( JPATH_COMPONENT.DS.'controller.php' );
require_once( JPATH_COMPONENT.DS.'models'.DS.'calendar.php' );
require_once( JPATH_COMPONENT.DS.'models'.DS.'event.php' );
require_once( JPATH_COMPONENT.DS.'views'.DS.'shbesuche'.DS.'view.html.php' );
//require_once(JPATH_COMPONENT.DS.'helpers'.DS.'pbdebug.php');


$task = JRequest::getWord('task');


if(!$task) {
    $controller->display();
}

if ($task =="dayview") {
    $controller->dayview();
}

if($task=="create") {
    $controller->create();
}

if($task=="save") {
    $controller->save();
}

if($task=="validate") {
    $controller->validate();
}

