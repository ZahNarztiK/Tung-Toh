<?

if(!isset($_IN_SITE)){
	die("Access denied ai sus!!!");
}

require_once("../../inc/db_connect.php");
require_once("../../inc/init_response_func.php");
require_once("../../inc/basic_func.php");
require_once("../../inc/map_func.php");
require_once("../../inc/table_func.php");

$GLOBALS['PLACE_PREFIX'] = "IP";
$__PLACE_DEFAULT = [
	"name" => "",
	"latitude" => "0.0",
	"longitude" => "0.0",
	"logo_image" => "",
	"info" => ""
];
$__PLACE_INFO_QUERY = "place_id, name, X(location) as latitude, Y(location) as longitude, logo_image, info";



function addPlace($place_raw){
	$prefix = $GLOBALS['PLACE_PREFIX'];

	try{
		global $DB_PDO;

		$place = preparePlaceData($place_raw);


		$stmt = $DB_PDO->prepare("SELECT place_id FROM place WHERE name = :name LIMIT 1");
		$stmt->bindParam(':name', $place['name']);
		$stmt->execute();
		
		if($stmt->rowCount() > 0){
			reject($prefix, $GLOBALS['RESPONSE_ERROR_CODE']['DB_DUPLICATED'], "Duplicated name.");
		}


		$stmt = $DB_PDO->prepare("INSERT INTO place (name, location, logo_image, info) VALUES (:name, POINT(:x, :y), :logo_image, :info)");
		$stmt->bindParam(':name', $place['name']);
		$stmt->bindParam(':x', $place['latitude']);
		$stmt->bindParam(':y', $place['longitude']);
		$stmt->bindParam(':logo_image', $place['logo_image']);
		$stmt->bindParam(':info', $place['info']);
		$stmt->execute();
		
		$place_id = $DB_PDO->lastInsertId();
		if($place_id == 0){
			reject($prefix, $GLOBALS['RESPONSE_ERROR_CODE']['DB_FAILED'], "Place add failed.");
		}

		$rs = [
			"place_id" => $place_id
		];

		return $rs;
	}
	catch(PDOException $e){
		reject($prefix, $GLOBALS['RESPONSE_ERROR_CODE']['PDO'], $e->getMessage());
	}
}

function editPlace($place_raw){
	$prefix = $GLOBALS['PLACE_PREFIX'];

	try{
		global $DB_PDO;

		$place = preparePlaceData($place_raw, true);


		$stmt = $DB_PDO->prepare("SELECT place_id FROM place WHERE place_id = :place_id LIMIT 1");
		$stmt->bindParam(':place_id', $place['place_id']);
		$stmt->execute();
		
		if($stmt->rowCount() == 0){
			reject($prefix, $GLOBALS['RESPONSE_ERROR_CODE']['DB_NODATA'], "Place not found.");
		}


		$stmt = $DB_PDO->prepare("SELECT place_id FROM place WHERE name = :name AND place_id != :place_id LIMIT 1");
		$stmt->bindParam(':name', $place['name']);
		$stmt->bindParam(':place_id', $place['place_id']);
		$stmt->execute();
		
		if($stmt->rowCount() > 0){
			reject($prefix, $GLOBALS['RESPONSE_ERROR_CODE']['DB_DUPLICATED'], "Duplicated name.");
		}


		$stmt = $DB_PDO->prepare("UPDATE place SET name = :name, location = POINT(:x, :y), logo_image = :logo_image, info = :info WHERE place_id = :place_id");
		$stmt->bindParam(':name', $place['name']);
		$stmt->bindParam(':x', $place['latitude']);
		$stmt->bindParam(':y', $place['longitude']);
		$stmt->bindParam(':logo_image', $place['logo_image']);
		$stmt->bindParam(':info', $place['info']);
		$stmt->bindParam(':place_id', $place['place_id']);
		$stmt->execute();
		
		
		$rs = [
			"place_id" => $place['place_id']
		];

		return $rs;
	}
	catch(PDOException $e){
		reject($prefix, $GLOBALS['RESPONSE_ERROR_CODE']['PDO'], $e->getMessage());
	}
}

function getPlace($place_id, $getAll = false, $event_id = null){
	global $__PLACE_INFO_QUERY;
	$prefix = $GLOBALS['PLACE_PREFIX'];

	$is_event_param = isPositiveInt($event_id);

	try{
		global $DB_PDO;


		$stmt = $DB_PDO->prepare("SELECT $__PLACE_INFO_QUERY FROM place WHERE place_id = :place_id LIMIT 1");
		$stmt->bindParam(':place_id', $place_id, PDO::PARAM_INT);
		$stmt->execute();
		
		if($stmt->rowCount() == 0){
			reject($prefix, $GLOBALS['RESPONSE_ERROR_CODE']['DB_NODATA'], "Place not found.");
		}

		$rs = $stmt->fetch(PDO::FETCH_ASSOC);


		if($getAll){
			if($is_event_param){
				$rs['map'] = getMapList($rs['place_id'], true, $event_id);
			}
			else{
				$rs['map'] = getMapList($rs['place_id'], true);
			}
		}

		
		return $rs;
	}
	catch(PDOException $e){
		reject($prefix, $GLOBALS['RESPONSE_ERROR_CODE']['PDO'], $e->getMessage());
	}
}

function removePlace($place_id){
	$prefix = $GLOBALS['PLACE_PREFIX'];

	$map_deleted = removeMapList($place_id);
	$event_deleted = removeEventList($place_id);

	try{
		global $DB_PDO;

		$stmt = $DB_PDO->prepare("DELETE FROM place WHERE place_id = :place_id");
		$stmt->bindParam(':place_id', $place_id, PDO::PARAM_INT);
		$stmt->execute();
		
		if($stmt->rowCount() == 0){
			reject($prefix, $GLOBALS['RESPONSE_ERROR_CODE']['DB_NODATA'], "Location not found.");
		}
		
		$rs = [
			"place_id" => $place_id,
			"map_deleted" => $map_deleted,
			"event_deleted" => $event_deleted
		];

		return $rs;
	}
	catch(PDOException $e){
		reject($prefix, $GLOBALS['RESPONSE_ERROR_CODE']['PDO'], $e->getMessage());
	}
}

function preparePlaceData($place_raw, $isEdit = false){
	global $__PLACE_DEFAULT;
	$prefix = $GLOBALS['PLACE_PREFIX'];

	$required_data = [
		"str*" => [ "name" ],
		"float*" => [ "latitude", "longitude" ],
		"str" => [ "logo_image", "info" ]
	];
	if($isEdit){
		$required_data = [ "+int*" => [ "place_id" ] ] + $required_data;
	}

	$place = prepareJSON($prefix, $place_raw, $required_data, $__PLACE_DEFAULT);

	return $place;
}

?>
