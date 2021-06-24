<?php 

defined('_JEXEC') or die('This is where you\'re wrong, kiddo');

class modEmployeesFullListHelper
{
	/**
	 * [getActiveEmployeesInfoAjax - method to fetch data of 'active' employees for further render]
	 * @return [array] [array containing markup with data to append on request and flag if batch returning in response is last one]
	 */
	public static function getActiveEmployeesAjax()
	{
		$app = JFactory::getApplication();
		$jinput = $app->input;
		$menuLink = JRoute::_( $app->getMenu()->getItem( $jinput->get->get('id') )->link );
		$offset = $jinput->get->get('offset');
		$limit = $jinput->get->get('limit');

		$employeesList = self::getData(['*'], ['table-name' => 'spr_sotrudnikw', 'table-alias' => 'str']);

		$activeEmployees = [];

		foreach ($employeesList as $employee) :

			$images = scandir('images/'.$employee->id.'/');
			$trash = ['.', '..', 'logo.jpg','index.html'];
			$images = array_diff($images, $trash);
			$images = array_values($images);

			if(count($images) == 1):
				$image = implode(',', $images);
				$employee->image = '/images/'.$employee->id.'/'.$image;
				$activeEmployees[] = $employee;
			endif;
		endforeach;

		$result = [];

		$activeEmployeesBatch = array_slice($activeEmployees, $offset, $limit);

		foreach ($activeEmployeesBatch as $activeEmployee) :
			if ($activeEmployee == end($activeEmployees)) :
				$result['last-batch'] = true;
			endif;
			ob_start();
			include(dirname(__FILE__).'/tmpl/default-list-item.php');
				$result['html'] .= ob_get_contents();
			ob_end_clean();
		endforeach;

		return $result;
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
	public static function getData($columns, $table, $where = false, $ordering = false, $limit = false, $offset = fasle, $single = false, $join = false, $joinRules = false)
	{
		$db = JFactory::getDBO();

		$query = $db->getQuery(true);

		$query->select($columns);
		$query->from($db->quoteName($table['table-name'], $table['table-alias']));
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
		else:
			$query->setLimit($limit);
		endif;
		
		$db->setQuery($query);

		if($single):
			$results = $db->loadObject();
		else:
			$results = $db->loadObjectList();
		endif;

		return $results;	
	}

	
}