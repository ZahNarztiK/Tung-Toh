<?

session_start();

$_IN_SITE = true;
require_once("../../inc/access_func.php");
require_once("../../inc/table_func.php");

$prefix = $GLOBALS['TABLE_PREFIX'];

if(!isset($_GET['method'])){
	reject($prefix, $GLOBALS['RESPONSE_ERROR_CODE']['INFO'], "No method");
}

switch($_GET['method']){
	case "add":
		access_check($prefix, $GLOBALS['ACCESS_CONSTANT']['ADMIN'], true);

		$data = data_check($prefix);
		$rs = addTable($data);

		$success_msg = "Add hai la!";
		break;
		
	case "edit":
		access_check($prefix, $GLOBALS['ACCESS_CONSTANT']['ADMIN'], true);

		$data = data_check($prefix);
		$rs = editTable($data);
		
		$success_msg = "Edit laew woi~";
		break;

	case "get":
		access_check($prefix);

		if(screenData($_GET, [ "+int*" => "table_id" ])){
			$rs = getTable($_GET['table_id']);
		}
		elseif(screenData($_GET, [ "+int*" => "map_id" ])){
			$rs = getTableList($_GET['map_id']);
		}
		else{
			reject($prefix, $GLOBALS['RESPONSE_ERROR_CODE']['INFO'], "Table/Map ID????");
		}

		$success_msg = "Ow pai!";
		break;

	case "remove":
		access_check($prefix, $GLOBALS['ACCESS_CONSTANT']['ADMIN']);

		if(screenData($_GET, [ "+int*" => "table_id" ])){
			$rs = removeTable($_GET['table_id']);
		}
		elseif(screenData($_GET, [ "+int*" => "map_id" ])){
			$rs = removeTableList("map_id", $_GET['map_id']);
		}
		elseif(screenData($_GET, [ "+int*" => "place_id" ])){
			$rs = removeTableList("place_id", $_GET['place_id']);
		}
		else{
			reject($prefix, $GLOBALS['RESPONSE_ERROR_CODE']['INFO'], "Table/Map ID/Place ID????");
		}

		$success_msg = "Lob la na jaa!";
		break;

	default:
		reject($prefix, $GLOBALS['RESPONSE_ERROR_CODE']['INFO'], "Method KUY RAI SUS!!?!??!?");
		break;
}

set_response($rs);
success($prefix, $success_msg);

?>
