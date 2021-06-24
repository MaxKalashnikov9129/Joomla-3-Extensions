<?php 

defined('_JEXEC') or die('This is where you\'re wrong, kiddo');

class mod_employeesFullListHelper
{
	/**
	 * [$db - property to store DB object]
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
	 * [__construct - method to instantiate object properties]
	 */
	
	public function __construct($params = false) {
		$this->app = JFactory::getApplication();
		$this->db = JFactory::getDBO();
		$this->moduleParams = $params;
	}

	/**
	 * [getActiveEmployees - method to get employee data]
	 * @return [array] [description]
	 */
	public function getActiveEmployees() {
			$employeesList = $this->getAllEmployeesList();
				
			$activeEmployees = [];

			foreach ($employeesList as $employee) :

				$images = scandir('images/'.$employee->id.'/');

				$trash = ['.', '..', 'logo.jpg','index.html'];
				$images = array_diff($images, $trash);
				$images = array_values($images);

				if(count($images) == 1) :
					$image = implode(',', $images);
					$employee->image = '/images/'.$employee->id.'/'.$image;
					$activeEmployees[] = $employee;
				endif;
			endforeach;

			return array_slice($activeEmployees, 0, $this->moduleParams->get('display_limit'));
	}

	/**
	 * [getAllEmployeesList - method to get list of employees to display. Data is fetched from #__contact_details table]
	 * @param  [integer] $categoryId [category of contacts to fetch]
	 * @param  [integer] $limit      [number of entries to fetch]
	 * @return [array]               [array containing list of objects with employees data]
	 */
	private function getAllEmployeesList() {
		return $this->getData(['sw.*'], ["table-name" => "spr_sotrudnikw", "table-alias" => "sw"]);
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
	 * @param  [array]    $columns    [array holding name of the columns to fetch from DB from specific tables]
	 * @param  [array]    $table      [array holding name of the table to fetch data from and its alias]
	 * @param  [array]    $where?     [array holding constraints for data fetch]
	 * @param  [string]   $ordering?  [string holding name of the column to sort by and sort direction]
	 * @param  [integer]  $limit?     [integer to determine limit for number of rows to fetch]
	 * @param  boolean    $single?    [boolean value determining whether only a single object or list of objects should be fetched]
	 * @param  [integer]  $offset?    [integer to determine offset for data to fetch]
	 * @param  boolean    $join?      [boolean value to determine whether any type of join operation should be used in query]
	 * @param  [array]    $joinRules? [array of $join rules to use in query]
	 * @return [array]                [array of objects or a single object depending on $single parameter]
	 */
	private function getData($columns, $table, $where = false, $ordering = false, $limit = false, $single = false, $offset = false, $join = false, $joinRules = false) {

		$query = $this->db->getQuery(true);

		$query->select($columns);
		$query->from($this->db->quoteName($table['table-name'], $table['table-alias']));
		if($join && $joinRules) :
			foreach ($joinRules as $table => $rule) :
				$query->join("INNER", $this->db->quoteName($table).' ON '.$this->db->quoteName($rule));
			endforeach;
		endif;

		if($where) :
			foreach ($where as $column => $rule) :
				$query->where($this->db->quoteName($column).' '.$this->db->quoteName($rule));
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
		// var_dump($this->db->setQuery($query)); die();

		if($single):
			$results = $this->db->loadObject();
		else:
			$results = $this->db->loadObjectList();
		endif;

		return $results;	
	}
}