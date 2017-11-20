<?

session_start();

$_IN_SITE = true;
require_once("../../inc/init_response_func.php");

if(!isset($_SESSION['member_id'])){
	reject("IT99", "Login gon ai sus!!!");
}

require_once("../../inc/table_func.php");

$table = prepareData($_POST + $__TABLE_DEFAULT);

$rs = addTable($table);
set_response($rs);
success("IT", "Ow pai!");





function prepareData($table){
	global $__TABLE_DEFAULT;

	$error = [];

	if(!isset($table['code']) || $table['code'] == ""){
		$error[] = "Table Code";
	}
	if(!isset($table['map_id']) || is_nan($table['map_id'])){
		$error[] = "Map ID";
	}

	if(!isset($table['x']) || is_nan($table['x'])){
		$table['x'] = $__TABLE_DEFAULT['x'];
	}
	if(!isset($table['y']) || is_nan($table['y'])){
		$table['y'] = $__TABLE_DEFAULT['y'];
	}
	if(!isset($table['table_type']) || is_nan($table['table_type'])){
		$table['table_type'] = $__TABLE_DEFAULT['table_type'];
	}

	if(!empty($error)){
		reject("IT04", "Error parameter(s) - ".implode(", ", $error));
	}

	return $table;
}

?>