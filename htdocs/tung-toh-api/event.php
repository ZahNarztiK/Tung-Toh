<?

session_start();

$_IN_SITE = true;
require_once("../../inc/access_func.php");
require_once("../../inc/event_func.php");

if(!isset($_GET['method'])){
	reject($GLOBALS['EVENT_PREFIX'], $GLOBALS['RESPONSE_ERROR_CODE']['INFO'], "No method");
}

switch($_GET['method']){
	case "add":
		access_check($GLOBALS['EVENT_PREFIX'], $GLOBALS['ACCESS_CONSTANT']['ADMIN'], true);

		$data = data_check($GLOBALS['EVENT_PREFIX']);
		$rs = addEvent($data);

		$success_msg = "Add hai la!";
		break;

	case "edit":
		access_check($GLOBALS['EVENT_PREFIX'], $GLOBALS['ACCESS_CONSTANT']['ADMIN'], true);

		$data = data_check($GLOBALS['EVENT_PREFIX']);
		$rs = editEvent($data);
		
		$success_msg = "Edit laew woi~";
		break;

	case "edittable":
		access_check($GLOBALS['EVENT_PREFIX'], $GLOBALS['ACCESS_CONSTANT']['ADMIN'], true);

		$data = data_check($GLOBALS['EVENT_PREFIX']);
		$rs = editTable($data, null, true);
		
		$success_msg = "Edit laew woi~";
		break;

	case "getall":
		$getAll = true;
	case "get":
		access_check($GLOBALS['EVENT_PREFIX']);

		if(!isset($getAll)){
			$getAll = false;
		}

		if(screenData($_GET, [ "+int*" => "event_id" ])){
			$rs = getEvent($_GET['event_id'], $getAll);
		}
		elseif(screenData($_GET, [ "+int*" => "place_id" ])){
			$rs = getEventList($_GET['place_id'], $getAll);
		}
		else{
			reject($GLOBALS['EVENT_PREFIX'], $GLOBALS['RESPONSE_ERROR_CODE']['INFO'], "Event/Place ID????");
		}

		$success_msg = "Ow pai!";
		break;

	case "gettable":
		access_check($GLOBALS['EVENT_PREFIX']);

		if(!screenData($_GET, [ "+int*" => "event_id" ])){
			reject($GLOBALS['EVENT_PREFIX'], $GLOBALS['RESPONSE_ERROR_CODE']['INFO'], "Event ID mai mee ror?");
		}

		if(screenData($_GET, [ "+int*" => "table_id" ])){
			$rs = getTable($_GET['table_id'], $_GET['event_id']);
		}
		elseif(screenData($_GET, [ "+int*" => "map_id" ])){
			$rs = getTableList($_GET['map_id'], $_GET['event_id']);
		}
		else{
			reject($__TABLE_PREFIX, $GLOBALS['RESPONSE_ERROR_CODE']['INFO'], "Table/Map ID????");
		}

		$success_msg = "Ow pai!";
		break;
	case "remove":
		access_check($GLOBALS['EVENT_PREFIX'], $GLOBALS['ACCESS_CONSTANT']['ADMIN']);

		if(screenData($_GET, [ "+int*" => "event_id" ])){
			$rs = removeEvent($_GET['event_id']);
		}
		elseif(screenData($_GET, [ "+int*" => "place_id" ])){
			$rs = removeEventList($_GET['place_id']);
		}
		else{
			reject($GLOBALS['EVENT_PREFIX'], $GLOBALS['RESPONSE_ERROR_CODE']['INFO'], "Event/Place ID????");
		}

		$success_msg = "Lob la na jaa!";
		break;
	default:
		reject($GLOBALS['EVENT_PREFIX'], $GLOBALS['RESPONSE_ERROR_CODE']['INFO'], "Method KUY RAI SUS!!?!??!?");
		break;
}

set_response($rs);
success($GLOBALS['EVENT_PREFIX'], $success_msg);

?>
