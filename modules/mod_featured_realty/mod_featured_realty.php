<?php

defined('_JEXEC') or die('This is where you\'re wrong, kiddo');

JLoader::register('mod_featuredRealtyHelper', './modules/mod_featured_realty/helper/helper.php');

$app = JFactory::getApplication();
$document = JFactory::getDocument();
$helper = new mod_featuredRealtyHelper($params);

$items = $helper->getFeaturedItems();

$link = JRoute::_( $app->getMenu()->getItem( $params->get('realty_item_link_id'))->link ).'?item=';

require JModuleHelper::getLayoutPath('mod_featured_realty', 'default');