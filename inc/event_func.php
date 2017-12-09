<?

if(!isset($_IN_SITE)){
	die("Access denied ai sus!!!");
}

require_once("../../inc/basic_func.php");
require_once("../../inc/place_func.php");
require_once("../../inc/map_func.php");
require_once("../../inc/table_func.php");

$GLOBALS['EVENT_PREFIX'] = "IE";
$__EVENT_DEFAULT = [
	"name" => "",
	"info" => "",
	"image" => ""
];
$__EVENT_DATA_REQUIRED = [
	"addEvent" => [
		"str*" => [ "name" ],
		"+int*" => [ "place_id", "date" ],
		"str" => [ "info", "image" ]
	],
	"editEvent" => [
		"+int*" => [ "event_id", "place_id", "date" ],
		"str*" => [ "name" ],
		"str" => [ "info", "image" ]
	]
];
$__EVENT_INFO_QUERY = "event_id, name, place_id, date, info, image, active";



function addEvent($event_raw){
	global $__EVENT_DEFAULT, $__EVENT_DATA_REQUIRED;
	$prefix = $GLOBALS['EVENT_PREFIX'];

	try{
		global $DB_PDO;

		$event = prepareJSON($prefix, $event_raw, $__EVENT_DATA_REQUIRED['addEvent'], $__EVENT_DEFAULT);


		$stmt = $DB_PDO->prepare("SELECT place_id FROM place WHERE place_id = :place_id LIMIT 1");
		$stmt->bindParam(':place_id', $event['place_id'], PDO::PARAM_INT);
		$stmt->execute();
		
		if($stmt->rowCount() == 0){
			reject($prefix, $GLOBALS['RESPONSE_ERROR_CODE']['DB_NODATA'], "Location not found.");
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
			reject($prefix, $GLOBALS['RESPONSE_ERROR_CODE']['DB_FAILED'], "Event add failed.");
		}

		setupEventTables($event_id, $event['place_id']);

		
		$rs = getEvent($event_id, true);


		return $rs;
	}
	catch(PDOException $e){
		reject($prefix, $GLOBALS['RESPONSE_ERROR_CODE']['PDO'], $e->getMessage());
	}
}

function editEvent($event_raw){
	global $__EVENT_DEFAULT, $__EVENT_DATA_REQUIRED;
	$prefix = $GLOBALS['EVENT_PREFIX'];

	try{
		global $DB_PDO;

		$event = prepareJSON($prefix, $event_raw, $__EVENT_DATA_REQUIRED['editEvent'], $__EVENT_DEFAULT);


		$stmt = $DB_PDO->prepare("SELECT event_id FROM event WHERE event_id = :event_id LIMIT 1");
		$stmt->bindParam(':event_id', $event['event_id'], PDO::PARAM_INT);
		$stmt->execute();
		
		if($stmt->rowCount() == 0){
			reject($prefix, $GLOBALS['RESPONSE_ERROR_CODE']['DB_NODATA'], "Event not found.");
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
		reject($prefix, $GLOBALS['RESPONSE_ERROR_CODE']['PDO'], $e->getMessage());
	}
}

function getEvent($event_id, $getAll = false){
	global $__EVENT_INFO_QUERY;
	$prefix = $GLOBALS['EVENT_PREFIX'];

	try{
		global $DB_PDO;


		$stmt = $DB_PDO->prepare("SELECT $__EVENT_INFO_QUERY FROM event WHERE event_id = :event_id LIMIT 1");
		$stmt->bindParam(':event_id', $event_id, PDO::PARAM_INT);
		$stmt->execute();
		
		if($stmt->rowCount() == 0){
			reject($prefix, $GLOBALS['RESPONSE_ERROR_CODE']['DB_NODATA'], "Event not found.");
		}

		$rs = $stmt->fetch(PDO::FETCH_ASSOC);
		$rs['date'] = strtotime($rs['date']);


		if($getAll){
			$rs['place'] = getPlace($rs['place_id'], true, $event_id);
		}

		
		return $rs;
	}
	catch(PDOException $e){
		reject($prefix, $GLOBALS['RESPONSE_ERROR_CODE']['PDO'], $e->getMessage());
	}
}

function getEventList($place_id, $getAll = false){
	global $__EVENT_INFO_QUERY;
	$prefix = $GLOBALS['EVENT_PREFIX'];

	try{
		global $DB_PDO;


		$stmt = $DB_PDO->prepare("SELECT place_id FROM place WHERE place_id = :place_id LIMIT 1");
		$stmt->bindParam(':place_id', $place_id, PDO::PARAM_INT);
		$stmt->execute();
		
		if($stmt->rowCount() == 0){
			reject($prefix, $GLOBALS['RESPONSE_ERROR_CODE']['DB_NODATA'], "Location not found.");
		}


		$stmt = $DB_PDO->prepare("SELECT $__EVENT_INFO_QUERY FROM event WHERE place_id = :place_id");
		$stmt->bindParam(':place_id', $place_id, PDO::PARAM_INT);
		$stmt->execute();
		
		$n = $stmt->rowCount();
		$event_list = $stmt->fetchAll(PDO::FETCH_CLASS);

		foreach ($event_list as &$event){
			$event->date = strtotime($event->date);
		}
		if($getAll){
			foreach($event_list as &$event){
				$event->table = getPlace($event->place_id);	//, true, $event->event_id);
			}
		}


		$rs = [
			"quantity" => $n,
			"event_list" => $event_list
		];
		
		return $rs;
	}
	catch(PDOException $e){
		reject($prefix, $GLOBALS['RESPONSE_ERROR_CODE']['PDO'], $e->getMessage());
	}
}

function removeEvent($event_id){
	$prefix = $GLOBALS['EVENT_PREFIX'];

	$table_booking_deleted = removeEventTableList($event_id);

	try{
		global $DB_PDO;

		
		$stmt = $DB_PDO->prepare("DELETE FROM event WHERE event_id = :event_id");
		$stmt->bindParam(':event_id', $event_id, PDO::PARAM_INT);
		$stmt->execute();
		
		if($stmt->rowCount() == 0){
			reject($prefix, $GLOBALS['RESPONSE_ERROR_CODE']['DB_NODATA'], "Event not found.");
		}
		
		$rs = [
			"event_id" => $event_id,
			"table_booking_deleted" => $table_booking_deleted
		];

		return $rs;
	}
	catch(PDOException $e){
		reject($prefix, $GLOBALS['RESPONSE_ERROR_CODE']['PDO'], $e->getMessage());
	}
}

function removeEventList($place_id){
	$prefix = $GLOBALS['EVENT_PREFIX'];

	try{
		global $DB_PDO;

		
		$stmt = $DB_PDO->prepare("DELETE FROM event_table WHERE place_id = :place_id");
		$stmt->bindParam(":place_id", $place_id, PDO::PARAM_INT);
		$stmt->execute();
		
		$table_booking_deleted = $stmt->rowCount();


		$stmt = $DB_PDO->prepare("DELETE FROM event WHERE place_id = :place_id");
		$stmt->bindParam(":place_id", $place_id, PDO::PARAM_INT);
		$stmt->execute();
				
		$rs = [
			"place_id" => $place_id,
			"quantity" => $stmt->rowCount(),
			"table_booking_deleted" => [
				"place_id" => $place_id,
				"quantity" => $table_booking_deleted
			]
		];

		return $rs;
	}
	catch(PDOException $e){
		reject($prefix, $GLOBALS['RESPONSE_ERROR_CODE']['PDO'], $e->getMessage());
	}
}

?>
