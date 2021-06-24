<?php

defined('_JEXEC') or die('This is where you\'re wrong, kiddo');

JLoader::register('mod_infoListHelper', './modules/mod_info_list/helper/helper.php');

$helper = new mod_infoListHelper($params);

$elements = $helper->getModuleParam('list_elements');

require JModuleHelper::getLayoutPath('mod_info_list', 'default');