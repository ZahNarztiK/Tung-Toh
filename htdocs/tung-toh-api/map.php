<?

session_start();

$_IN_SITE = true;
require_once("../../inc/access_func.php");
require_once("../../inc/map_func.php");

if(!isset($_GET['method'])){
	reject($__MAP_PREFIX, "04", "No method");
}

switch($_GET['method']){
	case "add":
		access_check($__MAP_PREFIX, $__ACCESS_ADMIN, true);

		$rs = addMap($_POST['data']);

		$success_msg = "Add hai la!";
		break;
	case "edit":
		access_check($__MAP_PREFIX, $__ACCESS_ADMIN, true);

		$rs = editMap($_POST['data']);
		
		$success_msg = "Edit laew woi~";
		break;
	case "getall":
		$getAll = true;
	case "get":
		access_check($__MAP_PREFIX);

		if(!isset($getAll)){
			$getAll = false;
		}

		if(isset($_GET['map_id']) && isPositiveInt($_GET['map_id'])){
			$rs = getMap($_GET['map_id'], $getAll);
		}
		elseif (isset($_GET['place_id']) && isPositiveInt($_GET['place_id'])) {
			$rs = getMapList($_GET['place_id'], $getAll);
		}
		else{
			reject($__MAP_PREFIX, "04", "Map/Place ID????");
		}

		$success_msg = "Ow pai!";
		break;
	case "remove":
		access_check($__MAP_PREFIX, $__ACCESS_ADMIN);

		if(isset($_GET['map_id']) && isPositiveInt($_GET['map_id'])){
			$rs = removeMap($_GET['map_id']);
		}
		elseif (isset($_GET['place_id']) && isPositiveInt($_GET['place_id'])) {
			$rs = removeMapList($_GET['place_id']);
		}
		else{
			reject($__MAP_PREFIX, "04", "Map/Place ID????");
		}

		$success_msg = "Lob la na jaa!";
		break;
	default:
		reject($__MAP_PREFIX, "04", "Method KUY RAI SUS!!?!??!?");
		break;
}

set_response($rs);
success($__MAP_PREFIX, $success_msg);

?>
