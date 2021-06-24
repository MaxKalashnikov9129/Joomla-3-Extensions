<?php 

defined('_JEXEC') or die('This is where you\'re wrong, kiddo');

class mod_infoListHelper
{
	/**
	 * [$app - property to store application object]
	 * @var [object]
	 */
	private $app;

	/**
	 * [$moduleParams - property to store module parameters]
	 * @var [object]
	 */
	private $moduleParams;

	/**
	 * [__construct - method to initial data assigning to properties]
	 */
	public function __construct($params)
	{
		$this->app = JFactory::getApplication();
		$this->moduleParams = $params;
	}

	/**
	 * [getModuleParam - method to get specific module parameter]
	 * @param  [string]  	$field     		[name of the module parameter field]
	 * @param  [object] 	$item?      	[current featured item]
	 * @param  [string] 	$itemField? 	[name of the field of featured item to get]
	 * @return [object]             		[object containing processed module parameter ready for use]
	 */
	public function getModuleParam($field, $item = false, $itemField = false) {
		return $this->processModuleParams($field, $item, $itemField);
	}


	/**
	 * [processModuleParams - method to process modules parameters to get necessary one]
	 * @param  [string]  	$field     		[name of the module parameter field]
	 * @param  [object] 	$item?      	[current featured item]
	 * @param  [string] 	$itemField? 	[name of the field of featured item to get]
	 * @return [object]             		[object containing processed module parameter ready for use]
	 */
	private function processModuleParams($field, $item = false, $itemField = false) {

		if($field):
			$moduleParamsArray = json_decode($this->moduleParams->get($field), true);
		endif;

		if(array_filter($moduleParamsArray)):
			$container = [];
			foreach ($moduleParamsArray as $paramKey => $param) :
				foreach ($param as $key => $value) :
					$container[$key][$paramKey] = $value;
				endforeach;
			endforeach;

			switch ($itemField):				
				default:
					$needle = $this->app->getLanguage()->getTag();
					break;
			endswitch;

			foreach ($container as $key => $value) :
				if(in_array($needle, $container[$key])):
					return (object) $container[$key];
				endif;		
			endforeach;
		endif;

		return $container;
	}
}