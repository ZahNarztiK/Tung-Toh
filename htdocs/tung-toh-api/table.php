<?

session_start();

$_IN_SITE = true;
require_once("../../inc/access_func.php");
require_once("../../inc/table_func.php");

if(!isset($_GET['method'])){
	reject($__TABLE_PREFIX, "04", "No method");
}

switch($_GET['method']){
	case "add":
		access_check($__TABLE_PREFIX, $__ACCESS_CONSTANT['ADMIN'], true);

		$data = data_check($__TABLE_PREFIX);
		$rs = addTable($data);

		$success_msg = "Add hai la!";
		break;
	case "edit":
		access_check($__TABLE_PREFIX, $__ACCESS_CONSTANT['ADMIN'], true);

		$data = data_check($__TABLE_PREFIX);
		$rs = editTable($data);
		
		$success_msg = "Edit laew woi~";
		break;
	case "get":
		access_check($__TABLE_PREFIX);

		if(isset($_GET['table_id']) && isPositiveInt($_GET['table_id'])){
			$rs = getTable($_GET['table_id']);
		}
		elseif(isset($_GET['map_id']) && isPositiveInt($_GET['map_id'])){
			$rs = getTableList($_GET['map_id']);
		}
		else{
			reject($__TABLE_PREFIX, "04", "Table/Map ID????");
		}

		$success_msg = "Ow pai!";
		break;
	case "remove":
		access_check($__TABLE_PREFIX, $__ACCESS_CONSTANT['ADMIN']);

		if(isset($_GET['table_id']) && isPositiveInt($_GET['table_id'])){
			$rs = removeTable($_GET['table_id']);
		}
		elseif(isset($_GET['map_id']) && isPositiveInt($_GET['map_id'])){
			$rs = removeTableList("map_id", $_GET['map_id']);
		}
		elseif(isset($_GET['place_id']) && isPositiveInt($_GET['place_id'])){
			$rs = removeTableList("place_id", $_GET['place_id']);
		}
		else{
			reject($__TABLE_PREFIX, "04", "Table/Map ID/Place ID????");
		}

		$success_msg = "Lob la na jaa!";
		break;
	default:
		reject($__TABLE_PREFIX, "04", "Method KUY RAI SUS!!?!??!?");
		break;
}

set_response($rs);
success($__TABLE_PREFIX, $success_msg);

?>
