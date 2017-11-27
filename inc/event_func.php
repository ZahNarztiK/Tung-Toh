<?

if(!isset($_IN_SITE)){
	die("Access denied ai sus!!!");
}

require_once("../../inc/db_connect.php");
require_once("../../inc/init_response_func.php");
require_once("../../inc/basic_func.php");
require_once("../../inc/place_func.php");
require_once("../../inc/map_func.php");
require_once("../../inc/table_func.php");

$__EVENT_PREFIX = "IE";
$__EVENT_DEFAULT = [
	"name" => "",
	"info" => "",
	"image" => ""
];
$__EVENT_INFO_QUERY = "event_id, name, place_id, date, info, image";



function addEvent($event_raw){
	global $__EVENT_PREFIX;
	$prefix = $__EVENT_PREFIX;

	try{
		global $DB_PDO;

		$event = prepareEventData($event_raw);


		$stmt = $DB_PDO->prepare("SELECT place_id FROM place WHERE place_id = :place_id LIMIT 1");
		$stmt->bindParam(':place_id', $event['place_id'], PDO::PARAM_INT);
		$stmt->execute();
		
		if($stmt->rowCount() == 0){
			reject($prefix, "14", "Location not found.");
		}


		$stmt = $DB_PDO->prepare("INSERT INTO event (name, place_id, date, info, image) VALUES (:name, :place_id, :date, :info, :image)");
		$stmt->bindParam(':name', $event['name']);
		$stmt->bindParam(':place_id', $event['place_id'], PDO::PARAM_INT);
		$stmt->bindParam(':date', $event['date']);
		$stmt->bindParam(':info', $event['info']);
		$stmt->bindParam(':image', $event['image']);
		$stmt->execute();
		
		$event_id = $DB_PDO->lastInsertId();
		if($event_id == 0){
			reject($prefix, "19", "Event add failed.");
		}

		$rs = [
			"event_id" => $event_id
		];

		return $rs;
	}
	catch(PDOException $e){
		reject($prefix, "10", $e->getMessage());
	}
}

function editEvent($event_raw){
	global $__EVENT_PREFIX;
	$prefix = $__EVENT_PREFIX;

	try{
		global $DB_PDO;

		$event = prepareEventData($event_raw, true);


		$stmt = $DB_PDO->prepare("SELECT event_id FROM event WHERE event_id = :event_id LIMIT 1");
		$stmt->bindParam(':event_id', $event['event_id'], PDO::PARAM_INT);
		$stmt->execute();
		
		if($stmt->rowCount() == 0){
			reject($prefix, "14", "Event not found.");
		}


		$stmt = $DB_PDO->prepare("UPDATE event SET name = :name, place_id = :place_id, date = :date, info = :info, image = :image WHERE event_id = :event_id");
		$stmt->bindParam(':name', $event['name']);
		$stmt->bindParam(':place_id', $event['place_id'], PDO::PARAM_INT);
		$stmt->bindParam(':date', $event['date']);
		$stmt->bindParam(':info', $event['info']);
		$stmt->bindParam(':image', $event['image']);
		$stmt->bindParam(':event_id', $event['event_id'], PDO::PARAM_INT);
		$stmt->execute();
		
		
		$rs = [
			"event_id" => $event['event_id']
		];

		return $rs;
	}
	catch(PDOException $e){
		reject($prefix, "10", $e->getMessage());
	}
}

function getEvent($event_id, $getAll = false){
	global $__EVENT_PREFIX, $__EVENT_INFO_QUERY;
	$prefix = $__EVENT_PREFIX;

	try{
		global $DB_PDO;


		$stmt = $DB_PDO->prepare("SELECT $__EVENT_INFO_QUERY FROM event WHERE event_id = :event_id LIMIT 1");
		$stmt->bindParam(':event_id', $event_id, PDO::PARAM_INT);
		$stmt->execute();
		
		if($stmt->rowCount() == 0){
			reject($prefix, "14", "Event not found.");
		}

		$rs = $stmt->fetch(PDO::FETCH_ASSOC);
		$rs['date'] = strtotime($rs['date']);


		if($getAll){
			//$rs['map'] = getMapList($rs['place_id'], true);
		}

		
		return $rs;
	}
	catch(PDOException $e){
		reject($prefix, "10", $e->getMessage());
	}
}

function getEventList($place_id, $getAll = false){
	global $__EVENT_PREFIX, $__EVENT_INFO_QUERY;
	$prefix = $__EVENT_PREFIX;

	try{
		global $DB_PDO;


		$stmt = $DB_PDO->prepare("SELECT place_id FROM place WHERE place_id = :place_id LIMIT 1");
		$stmt->bindParam(':place_id', $place_id, PDO::PARAM_INT);
		$stmt->execute();
		
		if($stmt->rowCount() == 0){
			reject($prefix, "14", "Location not found.");
		}


		$stmt = $DB_PDO->prepare("SELECT $__EVENT_INFO_QUERY FROM event WHERE place_id = :place_id");
		$stmt->bindParam(':place_id', $place_id, PDO::PARAM_INT);
		$stmt->execute();
		
		$n = $stmt->rowCount();
		$event_list = $stmt->fetchAll(PDO::FETCH_CLASS);

		foreach ($event_list as &$event){
			$event->date = strtotime($event->date);
		}
		/*if($getAll){
			foreach ($event_list as &$event) {
				$event->table = getTableList($event->event_id);
			}
		}*/


		$rs = [
			"quantity" => $n,
			"event_list" => $event_list
		];
		
		return $rs;
	}
	catch(PDOException $e){
		reject($prefix, "10", $e->getMessage());
	}
}

function removeEvent($event_id){
	global $__EVENT_PREFIX;
	$prefix = $__EVENT_PREFIX;

	//$map_deleted = removeMapList($event_id);

	try{
		global $DB_PDO;

		$stmt = $DB_PDO->prepare("DELETE FROM event WHERE event_id = :event_id");
		$stmt->bindParam(':event_id', $event_id, PDO::PARAM_INT);
		$stmt->execute();
		
		if($stmt->rowCount() == 0){
			reject($prefix, "14", "Event not found.");
		}
		
		$rs = [
			"event_id" => $event_id
			//"map_deleted" => $map_deleted
		];

		return $rs;
	}
	catch(PDOException $e){
		reject($prefix, "10", $e->getMessage());
	}
}

function removeEventList($place_id){
	global $__EVENT_PREFIX;
	$prefix = $__EVENT_PREFIX;

	//$table_deleted = removeTableList("place_id", $place_id);

	try{
		global $DB_PDO;

		$stmt = $DB_PDO->prepare("DELETE FROM event WHERE place_id = :place_id");
		$stmt->bindParam(":place_id", $place_id, PDO::PARAM_INT);
		$stmt->execute();
				
		$rs = [
			"place_id" => $place_id,
			"quantity" => $stmt->rowCount()
			//"table_deleted" => $table_deleted
		];

		return $rs;
	}
	catch(PDOException $e){
		reject($prefix, "10", $e->getMessage());
	}
}

function prepareEventData($event_raw, $isEdit = false){
	global $__EVENT_PREFIX, $__EVENT_DEFAULT;

	$error = [];
	$prefix = $__EVENT_PREFIX;

	$event = prepareJSON($prefix, $event_raw, $__EVENT_DEFAULT);


	if($isEdit && (!isset($event['event_id']) || notPositiveInt($event['event_id']))){
		$error[] = "Event ID";
	}
	$event['name'] = trim($event['name']);
	if($event['name'] == ""){
		$error[] = "Name";
	}
	if(!isset($event['place_id']) || notPositiveInt($event['place_id'])){
		$error[] = "Place ID";
	}
	if(!isset($event['date']) || notPositiveInt($event['date'])){
		$error[] = "Date";
	}
	$event['date'] = date("Y-m-d H:i:s", $event['date']);

	if(!empty($error)){
		reject($prefix, "04", "Error parameter(s) - ".implode(", ", $error));
	}


	$event['info'] = trim($event['info']);
	$event['image'] = trim($event['image']);


	return $event;
}

?>
