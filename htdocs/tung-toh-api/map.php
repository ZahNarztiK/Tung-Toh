<?

session_start();

$_IN_SITE = true;
require_once("../../inc/access_func.php");
require_once("../../inc/map_func.php");

if(!isset($_GET['method'])){
	reject($GLOBALS['MAP_PREFIX'], $GLOBALS['RESPONSE_ERROR_CODE']['INFO'], "No method");
}

switch($_GET['method']){
	case "add":
		access_check($GLOBALS['MAP_PREFIX'], $GLOBALS['ACCESS_CONSTANT']['ADMIN'], true);

		$data = data_check($GLOBALS['MAP_PREFIX']);
		$rs = addMap($data);

		$success_msg = "Add hai la!";
		break;

	case "edit":
		access_check($GLOBALS['MAP_PREFIX'], $GLOBALS['ACCESS_CONSTANT']['ADMIN'], true);

		$data = data_check($GLOBALS['MAP_PREFIX']);
		$rs = editMap($data);
		
		$success_msg = "Edit laew woi~";
		break;

	case "getall":
		$getAll = true;
	case "get":
		access_check($GLOBALS['MAP_PREFIX']);

		if(!isset($getAll)){
			$getAll = false;
		}

		if(screenData($_GET, [ "+int*" => "map_id" ])){
			$rs = getMap($_GET['map_id'], $getAll);
		}
		elseif(screenData($_GET, [ "+int*" => "place_id" ])){
			$rs = getMapList($_GET['place_id'], $getAll);
		}
		else{
			reject($GLOBALS['MAP_PREFIX'], $GLOBALS['RESPONSE_ERROR_CODE']['INFO'], "Map/Place ID????");
		}

		$success_msg = "Ow pai!";
		break;

	case "remove":
		access_check($GLOBALS['MAP_PREFIX'], $GLOBALS['ACCESS_CONSTANT']['ADMIN']);

		if(screenData($_GET, [ "+int*" => "map_id" ])){
			$rs = removeMap($_GET['map_id']);
		}
		elseif(screenData($_GET, [ "+int*" => "place_id" ])){
			$rs = removeMapList($_GET['place_id']);
		}
		else{
			reject($GLOBALS['MAP_PREFIX'], $GLOBALS['RESPONSE_ERROR_CODE']['INFO'], "Map/Place ID????");
		}

		$success_msg = "Lob la na jaa!";
		break;
		
	default:
		reject($GLOBALS['MAP_PREFIX'], $GLOBALS['RESPONSE_ERROR_CODE']['INFO'], "Method KUY RAI SUS!!?!??!?");
		break;
}

set_response($rs);
success($GLOBALS['MAP_PREFIX'], $success_msg);

?>
