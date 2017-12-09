<?

session_start();

$_IN_SITE = true;
require_once("../../inc/basic_func.php");
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

	case "addtable":
		$data = access_check($GLOBALS['EVENT_PREFIX'], $GLOBALS['ACCESS_CONSTANT']['ADMIN'], true);

		$rs = addEventTable($data);
		
		$success_msg = "Add hai la!";
		break;

	case "closetable":
		access_check($GLOBALS['EVENT_PREFIX']);

		if(screenData($_GET, [ "+int*" => "event_table_id" ])){
			$rs = setEventTableActive($_GET['event_table_id'], "closed");
		}
		else{
			reject($GLOBALS['EVENT_PREFIX'], $GLOBALS['RESPONSE_ERROR_CODE']['INFO'], "Event Table ID, KAK");
		}

		$success_msg = "Changed osas!";
		break;

	case "edit":
		$data = access_check($GLOBALS['EVENT_PREFIX'], $GLOBALS['ACCESS_CONSTANT']['ADMIN'], true);

		$rs = editEvent($data);
		
		$success_msg = "Edit laew woi~";
		break;

	case "edittable":
		$data = access_check($GLOBALS['EVENT_PREFIX'], $GLOBALS['ACCESS_CONSTANT']['ADMIN'], true);

		$rs = editEventTable($data);
		
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

		if(screenData($_GET, [ "+int*" => "event_table_id" ])){
			$rs = getEventTable($_GET['event_table_id']);
		}
		elseif(screenData($_GET, [ "+int*" => [ "event_id", "map_id" ] ])){
			$rs = getEventTableList($_GET['event_id'], $_GET['map_id']);
		}
		else{
			reject($GLOBALS['EVENT_PREFIX'], $GLOBALS['RESPONSE_ERROR_CODE']['INFO'], "Event Table ID / Event+Map ID, mai mee ror?");
		}

		$success_msg = "Ow pai!";
		break;

	case "hidetable":
		access_check($GLOBALS['EVENT_PREFIX']);

		if(screenData($_GET, [ "+int*" => "event_table_id" ])){
			$rs = setEventTableActive($_GET['event_table_id'], "hidden");
		}
		else{
			reject($GLOBALS['EVENT_PREFIX'], $GLOBALS['RESPONSE_ERROR_CODE']['INFO'], "Event Table ID, KAK");
		}

		$success_msg = "Changed osas!";
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

	case "removetable":
		access_check($GLOBALS['EVENT_PREFIX'], $GLOBALS['ACCESS_CONSTANT']['ADMIN']);

		if(screenData($_GET, [ "+int*" => "event_table_id" ])){
			$rs = removeEventTable($_GET['event_table_id']);
		}
		elseif(screenData($_GET, [ "+int*" => "event_id", "+int" => "map_id" ])){
			if(isset($_GET['map_id'])){
				$rs = removeEventTableList($_GET['event_id'], $_GET['map_id']);
			}
			else{
				$rs = removeEventTableList($_GET['event_id']);
			}
		}
		else{
			reject($GLOBALS['EVENT_PREFIX'], $GLOBALS['RESPONSE_ERROR_CODE']['INFO'], "Event Table ID / Event ID / Event+Map ID, mai mee ror?");
		}

		$success_msg = "Lob la na jaa!";
		break;

	case "showtable":
		access_check($GLOBALS['EVENT_PREFIX']);

		if(screenData($_GET, [ "+int*" => "event_table_id" ])){
			$rs = setEventTableActive($_GET['event_table_id']);
		}
		else{
			reject($GLOBALS['EVENT_PREFIX'], $GLOBALS['RESPONSE_ERROR_CODE']['INFO'], "Event Table ID, KAK");
		}

		$success_msg = "Changed osas!";
		break;

	default:
		reject($GLOBALS['EVENT_PREFIX'], $GLOBALS['RESPONSE_ERROR_CODE']['INFO'], "Method KUY RAI SUS!!?!??!?");
		break;
}

set_response($rs);
success($GLOBALS['EVENT_PREFIX'], $success_msg);

?>
