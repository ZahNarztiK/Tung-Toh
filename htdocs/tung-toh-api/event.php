<?

session_start();

$_IN_SITE = true;
require_once("../../inc/access_func.php");
require_once("../../inc/event_func.php");

if(!isset($_GET['method'])){
	reject($__EVENT_PREFIX, "04", "No method");
}

switch($_GET['method']){
	case "add":
		access_check($__EVENT_PREFIX, $__ACCESS_ADMIN, true);

		$rs = addEvent($_POST['data']);

		$success_msg = "Add hai la!";
		break;
	case "edit":
		access_check($__EVENT_PREFIX, $__ACCESS_ADMIN, true);

		$rs = editEvent($_POST['data']);
		
		$success_msg = "Edit laew woi~";
		break;
	case "getall":
		$getAll = true;
	case "get":
		access_check($__EVENT_PREFIX);

		if(!isset($getAll)){
			$getAll = false;
		}

		if(isset($_GET['event_id']) && isPositiveInt($_GET['event_id'])){
			$rs = getEvent($_GET['event_id'], $getAll);
		}
		elseif (isset($_GET['place_id']) && isPositiveInt($_GET['place_id'])) {
			$rs = getEventList($_GET['place_id'], $getAll);
		}
		else{
			reject($__EVENT_PREFIX, "04", "Event/Place ID????");
		}

		$success_msg = "Ow pai!";
		break;
	case "remove":
		access_check($__EVENT_PREFIX, $__ACCESS_ADMIN);

		if(isset($_GET['event_id']) && isPositiveInt($_GET['event_id'])){
			$rs = removeEvent($_GET['event_id']);
		}
		elseif (isset($_GET['place_id']) && isPositiveInt($_GET['place_id'])) {
			$rs = removeEventList($_GET['place_id']);
		}
		else{
			reject($__EVENT_PREFIX, "04", "Event/Place ID????");
		}

		$success_msg = "Lob la na jaa!";
		break;
	default:
		reject($__EVENT_PREFIX, "04", "Method KUY RAI SUS!!?!??!?");
		break;
}

set_response($rs);
success($__EVENT_PREFIX, $success_msg);

?>
