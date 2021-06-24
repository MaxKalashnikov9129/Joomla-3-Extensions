<?php 

defined('_JEXEC') or die('This is where you\'re wrong, kiddo');

class mod_featuredRealtyHelper
{
	/**
	 * [$db - property to hold Joomla DB Object]
	 * @var [object]
	 */

	private $db;

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

	public function __construct($params) {
		$this->app = JFactory::getApplication();
		$this->db = JFactory::getDBO();
		$this->moduleParams = $params;
	}

	/**
	 * [getItemImage - method to get images of currently iterated realty item]
	 * @param  [object]  	$item   [object of currently iterated realty item]
	 * @param  [string]    	$path   [base path to folder containing list of folders with images]
	 * @return [string]          	[path to image of currently iterated realty item]
	 */

	public function getItemImage($item, $path) {
		$rent = ($item->typr_op_id == 1) ? "A" : "";
		
		$itemImages = scandir('./'.$path.'/'.$item->unik_kode_type.$rent);
		$itemImages = array_diff($itemImages, ['.', '..', 'index.html']);
		$itemImages = array_values($itemImages);

		$imagesCount = count($itemImages);

		return $path.$item->unik_kode_type.$rent.'/'.$itemImages[rand(0, $imagesCount-1)];
	}

	/**
	 * [getFeaturedItems - method to get list of 'featured' items to iterate over. 'Featued' is rows of DB with hot = 1]
	 * @return [array] [array of 'featured' items objects]
	 */

	public function getFeaturedItems() {
		return $this->getData(['ob.unik_kode_type', 'ob.typr_op_id', 'tp.id', 'ob.type_object_id', 'ob.reklama', 'ob.date_v', 'ob.room_count', 'ob.ploshad', 'ob.typr_op_id', 'ob.price', 'ob.valuta'], ['table-name' => 'objectsw', 'table-alias' => 'ob'], ['ob.hot' => ' = 1'], 'RAND()', false, false, true, 'inner', ['type_op tp' => 'ob.typr_op_id = tp.id']);
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
				case 'valuta':
					$needle = $item->$itemField;
					break;
				
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

	/**
	 * [getData - method to fetch data from DB]
	 * @param  [array]    	$columns    [array holding name of the columns to fetch from DB from specific tables]
	 * @param  [array]    	$table      [array holding name of the table to fetch data from and its alias]
	 * @param  [array]    	$where?     [array holding constraints for data fetch]
	 * @param  [string]   	$ordering?  [string holding name of the column to sort by and sort direction]
	 * @param  [integer]  	$limit?     [integer to determine limit for number of rows to fetch]
	 * @param  [boolean]    $single?    [boolean value determining whether only a single object or list of objects should be fetched]
	 * @param  [integer]  	$offset?    [integer to determine offset for data to fetch]
	 * @param  [boolean]    $join?      [boolean value to determine whether any type of join operation should be used in query]
	 * @param  [array]    	$joinRules? [array of $join rules to use in query]
	 * @return [array]                	[array of objects or a single object depending on $single parameter]
	 */

	private function getData($columns, $table, $where = false, $ordering = false, $limit = false, $single = false, $offset = false, $join = false, $joinRules = false) {

		$query = $this->db->getQuery(true);

		$query->select($this->db->quoteName($columns));
		$query->from($this->db->quoteName($table['table-name'], $table['table-alias']));

		if($join && $joinRules) :
			foreach ($joinRules as $table => $rule) :
				$query->join("INNER", $table.' ON '.$rule);
			endforeach;
		endif;

		if($where) :
			foreach ($where as $column => $rule) :
				$query->where($column.' '.$rule);
			endforeach;
		endif;

		if($ordering):
			$query->order($ordering);
		endif;

		if($limit && $offset):
			$query->setLimit($limit, $offset);
		elseif($limit):
			$query->setLimit($limit);
		endif;

		$this->db->setQuery($query);

		if($single):
			$results = $this->db->loadObject();
		else:
			$results = $this->db->loadObjectList();
		endif;

		return $results;	
	}
}