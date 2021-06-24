<?php

defined('_JEXEC') or die('This is where you\'re wrong, kiddo');

JLoader::register('mod_employeesFullListHelper', './modules/mod_employees_full_list/helper/helper.php');

$app = JFactory::getApplication();
$helper = new mod_employeesFullListHelper($params);
$document = JFactory::getDocument();

$activeEmployees = $helper->getActiveEmployees();
$menuLink = JRoute::_($app->getMenu()->getItem($params->get('employee_page_id'))->link);

$document->addScript('/modules/mod_employees_full_list/assets/scripts.js', ['version' => 'auto'], ['defer' => 'defer']);
require JModuleHelper::getLayoutPath('mod_employees_full_list', 'default');