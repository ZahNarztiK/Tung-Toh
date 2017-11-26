<?

session_start();

$_IN_SITE = true;
require_once("../../inc/access_func.php");
require_once("../../inc/place_func.php");


if(!isset($_GET['method'])){
	reject($__PLACE_PREFIX, "04", "No method");
}

switch($_GET['method']){
	case "add":
		access_check($__PLACE_PREFIX, $__ACCESS_CONSTANT['ADMIN'], true);

		$rs = addPlace($_POST['data']);

		$success_msg = "Add hai la!";
		break;
	case "edit":
		access_check($__PLACE_PREFIX, $__ACCESS_CONSTANT['ADMIN'], true);

		$rs = editPlace($_POST['data']);
		
		$success_msg = "Edit laew woi~";
		break;
	case "getall":
		$getAll = true;
	case "get":
		access_check($__PLACE_PREFIX);

		if(!isset($getAll)){
			$getAll = false;
		}

		if(isset($_GET['place_id']) && isPositiveInt($_GET['place_id'])){
			$rs = getPlace($_GET['place_id'], $getAll);
		}
		else{
			reject($__PLACE_PREFIX, "04", "Place ID????");
		}

		$success_msg = "Ow pai!";
		break;
	case "remove":
		access_check($__PLACE_PREFIX, $__ACCESS_CONSTANT['ADMIN']);

		if(isset($_GET['place_id']) && isPositiveInt($_GET['place_id'])){
			$rs = removePlace($_GET['place_id']);
		}
		else{
			reject($__PLACE_PREFIX, "04", "Place ID????");
		}

		$success_msg = "Lob la na jaa!";
		break;
	default:
		reject($__PLACE_PREFIX, "04", "Method KUY RAI SUS!!?!??!?");
		break;
}

set_response($rs);
success($__PLACE_PREFIX, $success_msg);

?>
