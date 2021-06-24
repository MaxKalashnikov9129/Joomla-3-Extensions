<?php

defined('_JEXEC') or die('This is where you\'re wrong, kiddo');

JLoader::register('mod_employeesShortListHelper', './modules/mod_employees_short_list/helper/helper.php');

$app = JFactory::getApplication();
$helper = new mod_employeesShortListHelper($params);
$document = JFactory::getDocument();

$activeEmployees = $helper->getActiveEmployees();
$menuLink = JRoute::_($app->getMenu()->getItem($params->get('employee_page_id'))->link);
$showMoreLink = JRoute::_($app->getMenu()->getItem($params->get('show_more_button'))->link);

require JModuleHelper::getLayoutPath('mod_employees_short_list', 'default');
