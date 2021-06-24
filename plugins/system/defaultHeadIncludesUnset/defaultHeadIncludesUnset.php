<?php 


defined('_JEXEC') or die('You don\'t belong in here, stranger');

class plgSystemDefaultHeadIncludesUnset extends JPlugin
{

	private $application;
	private $document;
	private $config;

	public function __construct(&$subject, $config)
	{
		parent::__construct($subject, $config);
		$this->application = JFactory::getApplication();
		$this->document = JFactory::getDocument();
		$this->config = json_decode($config['params']);
	}

	public function onBeforeCompileHead()
	{
		if($this->application->isSite()):
			$unsetValues = explode(',', $this->config->unset_values);

			foreach ($unsetValues as $value):
				$value = trim($value);

				if(preg_match_all('/\/\w*\.js.*|\/\w*.\.\w*\.js.*/i', $value)):
					unset($this->document->_scripts[JURI::root(true).$value]);
				endif;

				if(preg_match_all('/\/\w*\.css.*|\/\w*.\.\w*\.css.*/i', $value)):
					unset($this->document->_scripts[JURI::root(true).$value]);
				endif;
			endforeach;			
		endif;
	}
}