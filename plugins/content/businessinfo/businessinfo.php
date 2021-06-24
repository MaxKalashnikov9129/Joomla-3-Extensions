<?php 

/**
 *  This plugin is aimed at constructing ld+json object for local business structred data markup
 */

defined('_JEXEC') or die;

class plgContentBusinessinfo extends JPlugin
{

	/**
	 * [$application - property holding Joomla application object]
	 * @var [object]
	 */

	private $application;

	/**
	 * [$document - property holding current document object]
	 * @var [object]
	 */

	private $document;

	/**
	 * [$config - property holding plugin configuration json object]
	 * @var [object]
	 */

	private $config;

	/**
	 * [__construct - basic properties assignments]
	 * @param [object] &$subject [plugin data object]
	 * @param [mixed]   $config  [plugin configuration object]
	 */

	public function __construct(&$subject, $config)
	{
		parent::__construct($subject, $config);
		$this->application = JFactory::getApplication();
		$this->document = JFactory::getDocument();
		$this->config = json_decode($config['params']);
	}

	/**
	 * [onContentPrepare - method to respond to Joomla's event of the same name]
	 * @param  [string]   $context   [The context of the content being passed to the plugin.]
	 * @param  [object]  &$article   [The article object.  Note $article->text is also available]
	 * @param  [mixed]   &$params    [The article params]
	 * @param  [integer]  $page      [The 'page' number]
	 * @return [bool]                [returns true just because function should return something]
	 */

	public function onContentPrepare($context, &$article, &$params, $page)
	{
		if($this->application->isSite()) :
			if($this->config->general_values == 1):
				$customFields = $article->jcfields;
			endif;
			
			if($this->config->general_values == 0):
				$customFields = json_decode($this->config->bi_general_list, true);
			endif;

			$customFieldsItems = $this->dataPrepare($customFields, $this->config);

			$localBusinessItems = $this->assembleDataItems($customFieldsItems);

			$template = $this->assembleStructuredData($localBusinessItems, $this->config);					
		endif;

		$this->document->addCustomTag($template);

		return true;
	}

	/**
	 * [dataPrepare - method to prepare arrays of data coming from either custom fields of Joomla or plugin's params nd make them usable for further processing]
	 * @param  [array]  $customFields [array of data from Joomla custom fields or plugin params]
	 * @param  [object] $config       [plugin's paramas]
	 * @return [array]                [resulting multidimentional array of data]
	 */

	private function dataPrepare($customFields, $config)
	{
		if($config->general_values == 1) :
			foreach ($customFields as $customField) :
				if($customField->name == $config->custom_field_name) :
					$customFieldsItems = json_decode($customField->rawvalue, true);
				endif;
			endforeach;

			return $customFieldsItems;
		endif;

		if($config->general_values == 0) :
			$customFieldsItems = [];
			foreach ($customFields as $k => $values) :
				foreach ($values as $i => $value) :
					$customFieldsItems[$i][$k] = $value;
				endforeach;
			endforeach;

			return $customFieldsItems;
		endif;

		return false;
	}


	/**
	 * [itemProcess - method to explod items holding values separated by comma or dash delimiter]
	 * @param  [string] $item      [item with delimiter to be process]
	 * @param  [string] $delimiter [delimiter to use for converting string into array of values]
	 * @return [array]             [array of parsed values]
	 * @return [bool]              [returns false if array passed as a param is empty or delimiter wasn't passed]
	 */

	private function itemProcess($item, $delimiter = null)
	{
		if(!empty($item) && strpos($item, $delimiter)):
			switch($delimiter):
				case ',':
					$result = explode($delimiter, $item);
					break;

				case '-':
					$result = explode($delimiter, $item);
					break;
			endswitch;

			return $result;
		endif;

		return false;
	}

	/**
	 * [assembleDataItem - method to combine array of local business data with array which values are meant to act as keys for resulting array]
	 * @param  [array] $array [array of local business data to merge with array of keys]
	 * @return [array]        [combined array  holding data prepared to be put into structured data markup]
	 * @return [bool]         [returns false if array passed as a param is empty]
	 */

	private function assembleDataItems($array)
	{
		if(!empty($array)) :
			$localBusinessInfo = ['name', 'address', 'image', 'telePhone', 'email', 'url', 'workingDaysFull', 'workingDaysShort', 'businessHours', 'paymentAccepted', 'priceRange', 'medical_specialty'];

			foreach ($array as $processedCustomFieldsItem) :
				$result[] = array_combine($localBusinessInfo, $processedCustomFieldsItem);
			endforeach;

			return $result;
		endif;

		return false;
	}

	/**
	 * [iterate method - to iterate over array items in case structured data object is supposed to have array of properties]
	 * @param  [array] $array          [array of local business data]
	 * @return [string]                [string of data prepared for structured data markup]
	 * @return [bool]                  [returns false if parameter is empty]
	 */

	private function iterate($array)
	{
		if(!empty($array)):
			$result;

			foreach ($array as $value) :
				$comma = ($value === end($array)) ? "" : ",";
				$result .= '"'.$value.'"'.$comma;
			endforeach;

			return $result;
		endif;

		return false;
	}

	/**
	 * [assembleStructuredData - method to assemble final ld+json object for google structured data]
	 * @param  [array] $localBusinessItems [array of data to be parsed into ld+json object]
	 * @return [string]                    [string to be passed to addCustomTagMethod for render]
	 * @return [bool]                      [return false if initial array of data to be parsed is empty]
	 */

	private function assembleStructuredData($localBusinessItems, $config)
	{
		if(!empty($localBusinessItems)):
			$template = '';
			foreach ($localBusinessItems as $localBusinessInfo) :

				$localBusinessInfoObject = (object) $localBusinessInfo;
				$address = $this->itemProcess($localBusinessInfoObject->address, ',');
				$workingDaysFull = $this->itemProcess($localBusinessInfoObject->workingDaysFull, ',');
				$businessHours = $this->itemProcess($localBusinessInfoObject->businessHours, '-');
				$paymentsAccepted = $this->itemProcess($localBusinessInfoObject->paymentAccepted, ',');

				if($config->schema_type == 0):
					$template .= '
						<script type="application/ld+json">
						{
							"@context":"https://schema.org",
							"type": "LocalBusiness",
							"name": "'.str_replace('"', '\'', $localBusinessInfoObject->medical_specialty).'",
							"address": {
								"@type": "PostalAddress",
								"streetAddress":"'.$address[0].'",
								"addressLocality":"'.$address[1].'",
								"addressRegion":"'.$address[2].'",
								"postalCode":"'.$address[3].'"

							},
							"image": "'.$localBusinessInfoObject->image.'",
							"telePhone": "'.$localBusinessInfoObject->telePhone.'",
							"email": "'.$localBusinessInfoObject->email.'",
							"url": "'.$localBusinessInfoObject->url.'",
							"openingHours": "'.$localBusinessInfoObject->workingDaysShort.' '.$localBusinessInfoObject->businessHours.'",
							"openingHoursSpecification": [{
								"@type":"OpeningHoursSpecification",
								"dayOfWeek": ['.$this->iterate($workingDaysFull).'],
								"opens":"'.$businessHours[0].'",
								"closes":"'.$businessHours[1].'"
							}],
							"paymentAccepted" : ['.$this->iterate($paymentsAccepted).'],
							"priceRange":"'.$localBusinessInfoObject->priceRange.'"';
					$template .= '
						}
						</script>';

					$template .= '
						<script type="application/ld+json">
						{
							"@context":"https://schema.org",
							"type": "MedicalClinic",
							"name": "'.str_replace('"', '\'', $localBusinessInfoObject->name).'",
							"medicalSpecialty": {
								"@type": "MedicalSpecialty",
								"name": "'.str_replace('"', '\'', $localBusinessInfoObject->medical_specialty).'"
							},
							"address": {
								"@type": "PostalAddress",
								"streetAddress":"'.$address[0].'",
								"addressLocality":"'.$address[1].'",
								"addressRegion":"'.$address[2].'",
								"postalCode":"'.$address[3].'"
							},
							"image": "'.$localBusinessInfoObject->image.'",
							"telePhone": "'.$localBusinessInfoObject->telePhone.'",
							"email": "'.$localBusinessInfoObject->email.'",
							"url": "'.$localBusinessInfoObject->url.'",
							"openingHours": "'.$localBusinessInfoObject->workingDaysShort.' '.$localBusinessInfoObject->businessHours.'",
							"openingHoursSpecification": [{
								"@type":"OpeningHoursSpecification",
								"dayOfWeek": ['.$this->iterate($workingDaysFull).'],
								"opens":"'.$businessHours[0].'",
								"closes":"'.$businessHours[1].'"
							}],
							"paymentAccepted" : ['.$this->iterate($paymentsAccepted).'],
							"priceRange":"'.$localBusinessInfoObject->priceRange.'"';
					$template .= '
						}
						</script>';
				endif;

				if($config->schema_type == 1):	
					$template .= '
						<script type="application/ld+json">
						{
							"@context":"https://schema.org",
							"type": "LocalBusiness",
							"name": "'.str_replace('"', '\'', $localBusinessInfoObject->name).'",
							"address": {
								"@type": "PostalAddress",
								"streetAddress":"'.$address[0].'",
								"addressLocality":"'.$address[1].'",
								"addressRegion":"'.$address[2].'",
								"postalCode":"'.$address[3].'"
							},
							"image": "'.$localBusinessInfoObject->image.'",
							"telePhone": "'.$localBusinessInfoObject->telePhone.'",
							"email": "'.$localBusinessInfoObject->email.'",
							"url": "'.$localBusinessInfoObject->url.'",
							"openingHours": "'.$localBusinessInfoObject->workingDaysShort.' '.$localBusinessInfoObject->businessHours.'",
							"openingHoursSpecification": [{
								"@type":"OpeningHoursSpecification",
								"dayOfWeek": ['.$this->iterate($workingDaysFull).'],
								"opens":"'.$businessHours[0].'",
								"closes":"'.$businessHours[1].'"
							}],
							"paymentAccepted" : ['.$this->iterate($paymentsAccepted).'],
							"priceRange":"'.$localBusinessInfoObject->priceRange.'"';
					$template .= '
						}
						</script>';
				endif;

				if($config->schema_type == 2):
					$template .= '
						<script type="application/ld+json">
						{
							"@context":"https://schema.org",
							"type": "MedicalClinic",
							"name": "'.str_replace('"', '\'', $localBusinessInfoObject->name).'",
							"medicalSpecialty": {
								"@type": "MedicalSpecialty",
								"name": "'.str_replace('"', '\'', $localBusinessInfoObject->medical_specialty).'"
							},
							"address": {
								"@type": "PostalAddress",
								"streetAddress":"'.$address[0].'",
								"addressLocality":"'.$address[1].'",
								"addressRegion":"'.$address[2].'",
								"postalCode":"'.$address[3].'"
							},
							"image": "'.$localBusinessInfoObject->image.'",
							"telePhone": "'.$localBusinessInfoObject->telePhone.'",
							"email": "'.$localBusinessInfoObject->email.'",
							"url": "'.$localBusinessInfoObject->url.'",
							"openingHours": "'.$localBusinessInfoObject->workingDaysShort.' '.$localBusinessInfoObject->businessHours.'",

							"openingHoursSpecification": [{
								"@type":"OpeningHoursSpecification",
								"dayOfWeek": ['.$this->iterate($workingDaysFull).'],
								"opens":"'.$businessHours[0].'",
								"closes":"'.$businessHours[1].'"
							}],
							"paymentAccepted" : ['.$this->iterate($paymentsAccepted).'],
							"priceRange":"'.$localBusinessInfoObject->priceRange.'"';
					$template .= '
						}
						</script>';
				endif;
			endforeach;

			return $template;
		endif;

		return false;
	}
}