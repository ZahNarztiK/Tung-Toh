<?

if(!isset($_IN_SITE)){
	die("Access denied ai sus!!!");
}

require_once("../../inc/db_connect.php");

$__MAP_DEFAULT = [
	//"width" => 0,
	//"height" => 0,
	"name" => "",
	"bg_image" => ""
];



function addMap($map_raw){
	try{
		global $DB_PDO;

		$map = prepareData($map_raw);


		$stmt = $DB_PDO->prepare("SELECT TOP 1 place_id FROM place WHERE place_id = :place_id");
		$stmt->bindParam(':place_id', $map['place_id'], PDO::PARAM_INT);
		$stmt->execute();
		
		if($stmt->rowCount() == 0){
			reject("IM14", "Location not found.");
		}


		$stmt = $DB_PDO->prepare("INSERT INTO map (place_id, width, height, name, bg_image) VALUES (:place_id, :width, :height, :name, :bg_image)");
		$stmt->bindParam(':place_id', $map['place_id'], PDO::PARAM_INT);
		$stmt->bindParam(':width', $map['width'], PDO::PARAM_INT);
		$stmt->bindParam(':height', $map['height'], PDO::PARAM_INT);
		$stmt->bindParam(':name', $map['name']);
		$stmt->bindParam(':bg_image', $map['bg_image']);
		$stmt->execute();
		
		$map_id = $DB_PDO->lastInsertId();
		if($map_id == 0){
			reject("IM19", "Map add failed.");
		}

		$rs = [
			"map_id" => $map_id
		]

		return $rs;
	}
	catch(PDOException $e){
		reject("IM10", $e->getMessage());
	}
}

function prepareData($map){
	global $__MAP_DEFAULT;

	$error = [];
	$map += $__MAP_DEFAULT;

	if(!isset($map['place_id']) || $map['place_id'] == ""){
		$error[] = "Place ID";
	}
	if(!isset($map['width']) || is_nan($map['width'])){
		$error[] = "Width";
	}
	if(!isset($map['height']) || is_nan($map['height'])){
		$error[] = "Height";
	}

	//if(!isset($map['width']) || is_nan($map['width'])){
	//	$map['width'] = $__MAP_DEFAULT['width'];
	//}
	//if(!isset($map['height']) || is_nan($map['height'])){
	//	$map['height'] = $__MAP_DEFAULT['height'];
	//}
	if(!isset($map['bg_image']) || is_nan($map['bg_image'])){
		$map['bg_image'] = $__MAP_DEFAULT['bg_image'];
	}

	if(!empty($error)){
		reject("IM04", "Error parameter(s) - ".implode(", ", $error));
	}

	return $map;
}

?>