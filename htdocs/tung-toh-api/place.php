<?

session_start();

$_IN_SITE = true;
require_once("../../inc/access_func.php");
require_once("../../inc/place_func.php");


if(!isset($_GET['method'])){
	reject($GLOBALS['PLACE_PREFIX'], $GLOBALS['RESPONSE_ERROR_CODE']['INFO'], "No method");
}

switch($_GET['method']){
	case "add":
		access_check($GLOBALS['PLACE_PREFIX'], $GLOBALS['ACCESS_CONSTANT']['ADMIN'], true);

		$data = data_check($GLOBALS['PLACE_PREFIX']);
		$rs = addPlace($data);

		$success_msg = "Add hai la!";
		break;
	case "edit":
		access_check($GLOBALS['PLACE_PREFIX'], $GLOBALS['ACCESS_CONSTANT']['ADMIN'], true);

		$data = data_check($GLOBALS['PLACE_PREFIX']);
		$rs = editPlace($data);
		
		$success_msg = "Edit laew woi~";
		break;
	case "getall":
		$getAll = true;
	case "get":
		access_check($GLOBALS['PLACE_PREFIX']);

		if(!isset($getAll)){
			$getAll = false;
		}

		if(isset($_GET['place_id']) && isPositiveInt($_GET['place_id'])){
			$rs = getPlace($_GET['place_id'], $getAll);
		}
		else{
			reject($GLOBALS['PLACE_PREFIX'], $GLOBALS['RESPONSE_ERROR_CODE']['INFO'], "Place ID????");
		}

		$success_msg = "Ow pai!";
		break;
	case "remove":
		access_check($GLOBALS['PLACE_PREFIX'], $GLOBALS['ACCESS_CONSTANT']['ADMIN']);

		if(isset($_GET['place_id']) && isPositiveInt($_GET['place_id'])){
			$rs = removePlace($_GET['place_id']);
		}
		else{
			reject($GLOBALS['PLACE_PREFIX'], $GLOBALS['RESPONSE_ERROR_CODE']['INFO'], "Place ID????");
		}

		$success_msg = "Lob la na jaa!";
		break;
	default:
		reject($GLOBALS['PLACE_PREFIX'], $GLOBALS['RESPONSE_ERROR_CODE']['INFO'], "Method KUY RAI SUS!!?!??!?");
		break;
}

set_response($rs);
success($GLOBALS['PLACE_PREFIX'], $success_msg);

?>
