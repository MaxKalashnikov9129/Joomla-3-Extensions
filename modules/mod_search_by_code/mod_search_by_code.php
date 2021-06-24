<?php 

defined("_JEXEC") or die('This is where you\'re wrong, kiddo');

$document = JFactory::getDocument();

$document->addScript('/modules/mod_search_by_code/assets/scripts.js', ['version' => 'auto'], ['defer' => 'defer']);

require JModuleHelper::getLayoutPath('mod_search_by_code', 'default');