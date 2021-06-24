<?php 

class modSearchByCodeHelper
{
	public static function getRealtyItemAjax()
	{
		$app = JFactory::getApplication();
		$jinput = $app->input;

		$link = $app->getMenu()->getItem($jinput->get->get('id'))->route;

		return JRoute::_($link.'?item='.$jinput->get->get('code'));
	}
}