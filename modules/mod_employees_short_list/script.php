<?php

defined('_JEXEC') or die('This is where you\'re wrong, kiddo');

class mod_employees_short_listInstallerScript {
	
	/**
	 * [$mod_name - property to hold extension name (based on folder name)]
	 * @var [string]
	 */
	private $mod_name;
	/**
	 * [$extension_folder - extension folder content presented as array of folders and file names]
	 * @var [array]
	 */
	private $extension_folder;

	/**
	 * [$languages_folder_index - index of language folder among other files in extension folder]
	 * @var [integer]
	 */
	private $languages_folder_index;

	/**
	 * [$languages_list array of known languages of the current CMS setup]
	 * @var [array]
	 */
	private $languages_list;

	/**
	 * [$extension_languages array of folder named after language tags]
	 * @var [array]
	 */
	private $extension_languages;

	/**
	 * [$language_overrides_path - path to folder with language constants overrides]
	 * @var [string]
	 */
	private $language_overrides_path;

	public function __construct(){
		define('DS', '\\');	
		$this->mod_name = basename( dirname(__FILE__) );
		$this->languages_list = JLanguageHelper::getKnownLanguages();
		$this->extension_folder = scandir( dirname(__FILE__) );
		$this->languages_folder_index = array_search('language', $this->extension_folder);
		$this->extension_languages = scandir(dirname(__FILE__).DS.$this->extension_folder[$this->languages_folder_index]);
		$this->language_overrides_path = DS.$this->extension_folder[$this->languages_folder_index].DS.'overrides'.DS;
	}

	/**
     * This method is called after a component is uninstalled.
     *
     * @param  \stdClass $parent - Parent object calling this method.
     *
     */
    public function uninstall($parent) 
    {
    	/**
		 * if language folder was found withing extension folder
		 */
		if(false !== $this->languages_folder_index):
			$this->removeLanguageTranslationOverride();
		endif; 
    }
	
	/**
	 * Method to run after an install/update/uninstall method
	 * $parent is the class calling this method
	 * $type is the type of change (install, update or discover_install)
	 *
	 */
	public function postflight($type, $parent) 
	{
		if(false !== $this->languages_folder_index):
			$this->addLanguageTranslationOverride();
		endif;
	}

	/**
	 * [addLanguageTranslationOverride - method to add language contants to override file providing user with interface to change translation to his/her liking]
	 */
	private function addLanguageTranslationOverride() {
		$this->getLanguageConstants($this->language_overrides_path, $this->extension_languages);	
	}

	/**
	 * [removeLanguageTranslationOverride - method to remove language constants on module complete removal from CMS]
	 */
	private function removeLanguageTranslationOverride() {
		$this->getLanguageConstants($this->language_overrides_path, $this->extension_languages, 'uninstall');
	}

	private function getLanguageConstants($language_overrides_path, $extension_languages, $action = false) {
		foreach($this->languages_list as $language):
			if( in_array($language['tag'], $this->extension_languages) ):

				$extension_language_constants = file_get_contents( dirname(__FILE__).DS.$this->extension_folder[$this->languages_folder_index].DS.$language['tag'].DS.$language['tag'].'.'.$this->mod_name.'.ini' );

				$language_overrides_constants = file_get_contents( JPATH_SITE.$this->language_overrides_path.$language['tag'].'.override.ini' );

				$this->processLanguageConstants($language, $extension_language_constants, $language_overrides_constants, $this->language_overrides_path, $action);
			endif;
		endforeach;
	}

	private function processLanguageConstants($language, $extension_language_constants, $language_overrides_constants, $language_overrides_path, $action = false) {

		$language_constants = array_filter(explode('-', $extension_language_constants));

		foreach (array_values($language_constants) as $index => $language_constant ) :
			switch ($action):
				case 'uninstall':
					if( !empty($language_constant) && stripos($language_overrides_constants, $language_constant) !== false ):
						
						$purged_constants_list = str_replace($language_constant, '', file_get_contents(JPATH_SITE.$this->language_overrides_path.$language['tag'].'.override.ini') );
							
						file_put_contents(JPATH_SITE.$this->language_overrides_path.$language['tag'].'.override.ini', $purged_constants_list);
					endif;
				break;
					
				default:
					if( !empty($language_constant) && stripos($language_overrides_constants, $language_constant) === false ):
							
						$divider = ($index == 0) ? PHP_EOL : null;
							
						file_put_contents(JPATH_SITE.$this->language_overrides_path.$language['tag'].'.override.ini', $divider.$language_constant, FILE_APPEND);
					endif;
				break;
			endswitch;
		endforeach;
	}
}