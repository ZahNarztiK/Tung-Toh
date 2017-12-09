<?

if(!isset($_IN_SITE)){
	die("Access denied ai sus!!!");
}

$__TABLE_EVENT_CONSTANT = [
	"hidden" => -1,
	"closed" => 0,
	"open" => 1,
	"booked" => 2
];

function addEventTable($event_table_raw){
	global $__TABLE_DATA_REQUIRED;
	$prefix = $GLOBALS['TABLE_PREFIX'];
	$query = $__TABLE_QUERY['defaultTable_db'];

	try{
		global $DB_PDO;

		$event_table = prepareJSON($prefix, $event_table_raw, $__TABLE_DATA_REQUIRED['addEventTable']);


		$stmt = $DB_PDO->prepare("SELECT event_id FROM event WHERE event_id = :event_id AND place_id = (SELECT place_id FROM `table` WHERE table_id = :table_id LIMIT 1) LIMIT 1");
		$stmt->bindParam(':event_id', $event_table['event_id'], PDO::PARAM_INT);
		$stmt->execute();
		
		if($stmt->rowCount() == 0){
			reject($prefix, $GLOBALS['RESPONSE_ERROR_CODE']['DB_NODATA'], "Event/Table not found or mismatched.");
		}


		$stmt = $DB_PDO->prepare("SELECT event_table_id FROM event_table WHERE event_id = :event_id AND table_id = :table_id LIMIT 1");
		$stmt->bindParam(':event_id', $event_table['event_id'], PDO::PARAM_INT);
		$stmt->bindParam(':table_id', $event_table['table_id'], PDO::PARAM_INT);
		$stmt->execute();
		
		if($stmt->rowCount() > 0){
			reject($prefix, $GLOBALS['RESPONSE_ERROR_CODE']['DB_DUPLICATED'], "Table is already exist in the event.");
		}


		$stmt = $DB_PDO->prepare("INSERT INTO event_table (event_id, $query) SELECT :event_id, $query FROM `table` WHERE table_id = :table_id");
		$stmt->bindParam(':event_id', $event_table['event_id'], PDO::PARAM_INT);
		$stmt->bindParam(':table_id', $event_table['table_id'], PDO::PARAM_INT);
		$stmt->execute();
		
		$event_table_id = $DB_PDO->lastInsertId();
		if($event_table_id == 0){
			reject($prefix, $GLOBALS['RESPONSE_ERROR_CODE']['DB_FAILED'], "Event Table add failed.");
		}

		$rs = getEventTable($event_table_id);

		return $rs;
	}
	catch(PDOException $e){
		reject($prefix, $GLOBALS['RESPONSE_ERROR_CODE']['PDO'], $e->getMessage());
	}
}

function clearEventTableList($identifier, $identifier_id){
	$prefix = $GLOBALS['TABLE_PREFIX'];
	$identifier_list = ["table_id", "map_id", "place_id"];

	if(!in_array($identifier, $identifier_list) || notPositiveInt($identifier_id)){
		reject($prefix, $GLOBALS['RESPONSE_ERROR_CODE']['PHP'], "Identifier error.");
	}

	try{
		global $DB_PDO;

		
		$stmt = $DB_PDO->prepare("DELETE FROM event_table WHERE $identifier = :$identifier");
		$stmt->bindParam(":$identifier", $identifier_id, PDO::PARAM_INT);
		$stmt->execute();
		
		$rs = [
			"$identifier" => $identifier_id,
			"quantity" => $stmt->rowCount()
		];

		return $rs;
	}
	catch(PDOException $e){
		reject($prefix, $GLOBALS['RESPONSE_ERROR_CODE']['PDO'], $e->getMessage());
	}
}

function editEventTable($event_table_raw){
	global $__TABLE_DEFAULT, $__TABLE_DATA_REQUIRED;
	$prefix = $GLOBALS['TABLE_PREFIX'];

	try{
		global $DB_PDO;

		$event_table = prepareJSON($prefix, $event_table_raw, $__TABLE_DATA_REQUIRED['editEventTable'], $__TABLE_DEFAULT['eventTable']);

		
		$stmt = $DB_PDO->prepare("UPDATE event_table SET location = POINT(:x, :y), rotation = :rotation, table_type_id = :table_type_id WHERE event_table_id = :event_table_id");
		$stmt->bindParam(':x', $event_table['x'], PDO::PARAM_INT);
		$stmt->bindParam(':y', $event_table['y'], PDO::PARAM_INT);
		$stmt->bindParam(':rotation', $event_table['rotation'], PDO::PARAM_INT);
		$stmt->bindParam(':table_type_id', $event_table['table_type_id'], PDO::PARAM_INT);
		$stmt->bindParam(':event_table_id', $event_table['event_table_id'], PDO::PARAM_INT);
		$stmt->execute();

		if($stmt->rowCount() == 0){
			reject($prefix, $GLOBALS['RESPONSE_ERROR_CODE']['DB_NODATA'], "Event Table not found.");
		}
		

		$rs = [
			"event_table_id" => $event_table['event_table_id']
		];

		return $rs;
	}
	catch(PDOException $e){
		reject($prefix, $GLOBALS['RESPONSE_ERROR_CODE']['PDO'], $e->getMessage());
	}
}

function getEventTable($event_table_id){
	global  $__TABLE_QUERY;
	$prefix = $GLOBALS['TABLE_PREFIX'];
	$query = $__TABLE_QUERY['eventTable'];

	try{
		global $DB_PDO;


		$stmt = $DB_PDO->prepare("SELECT $query FROM event_table WHERE event_table_id = :event_table_id LIMIT 1");
		$stmt->bindParam(':event_table_id', $event_table_id, PDO::PARAM_INT);
		$stmt->execute();
		
		if($stmt->rowCount() == 0){
			reject($prefix, $GLOBALS['RESPONSE_ERROR_CODE']['DB_NODATA'], "Event Table not found.");
		}
		
		$rs = $stmt->fetch(PDO::FETCH_ASSOC);
		
		return $rs;
	}
	catch(PDOException $e){
		reject($prefix, $GLOBALS['RESPONSE_ERROR_CODE']['PDO'], $e->getMessage());
	}
}

function getEventTableList($event_id, $map_id){
	global $__TABLE_QUERY;
	$prefix = $GLOBALS['TABLE_PREFIX'];
	$query = $__TABLE_QUERY['eventTable'];

	try{
		global $DB_PDO;


		$stmt = $DB_PDO->prepare("SELECT place_id FROM map WHERE map_id = :map_id AND place_id = (SELECT place_id FROM event WHERE event_id = :event_id LIMIT 1) LIMIT 1");
		$stmt->bindParam(':map_id', $map_id, PDO::PARAM_INT);
		$stmt->bindParam(':event_id', $event_id, PDO::PARAM_INT);
		$stmt->execute();
		
		if($stmt->rowCount() == 0){
			reject($prefix, $GLOBALS['RESPONSE_ERROR_CODE']['DB_NODATA'], "Event/Table not found or mismatched.");
		}


		$stmt = $DB_PDO->prepare("SELECT $query FROM event_table WHERE event_id = :event_id AND map_id = :map_id");
		$stmt->bindParam(':event_id', $event_id, PDO::PARAM_INT);
		$stmt->bindParam(':map_id', $map_id, PDO::PARAM_INT);
		$stmt->execute();
				
		$rs = [
			"quantity" => $stmt->rowCount(),
			"event_table_list" => $stmt->fetchAll(PDO::FETCH_CLASS)
		];

		return $rs;
	}
	catch(PDOException $e){
		reject($prefix, $GLOBALS['RESPONSE_ERROR_CODE']['PDO'], $e->getMessage());
	}
}

function removeEventTable($event_table_id){
	$prefix = $GLOBALS['TABLE_PREFIX'];

	try{
		global $DB_PDO;

		
		$stmt = $DB_PDO->prepare("DELETE FROM event_table WHERE event_table_id = :event_table_id");
		$stmt->bindParam(':event_table_id', $event_table_id, PDO::PARAM_INT);
		$stmt->execute();
		
		if($stmt->rowCount() == 0){
			reject($prefix, $GLOBALS['RESPONSE_ERROR_CODE']['DB_NODATA'], "Event Table not found.");
		}
		
		$rs = [
			"event_table_id" => $event_table_id
		];

		return $rs;
	}
	catch(PDOException $e){
		reject($prefix, $GLOBALS['RESPONSE_ERROR_CODE']['PDO'], $e->getMessage());
	}
}

function removeEventTableList($event_id, $map_id = null){
	$prefix = $GLOBALS['TABLE_PREFIX'];

	if($map_id != null){
		$fromMap = true;
		$add_cond = "AND map_id = :map_id";
	}
	else{
		$fromMap = false;
		$add_cond = "";
	}

	try{
		global $DB_PDO;

		
		$stmt = $DB_PDO->prepare("SELECT place_id FROM event WHERE event_id = :event_id LIMIT 1");
		$stmt->bindParam(':event_id', $event_id, PDO::PARAM_INT);
		$stmt->execute();

		if($stmt->rowCount() == 0){
			reject($prefix, $GLOBALS['RESPONSE_ERROR_CODE']['DB_NODATA'], "Event not found.");
		}
		$event_place_id = $stmt->fetchColumn();


		if($fromMap){
			$stmt = $DB_PDO->prepare("SELECT place_id FROM map WHERE map_id = :map_id LIMIT 1");
			$stmt->bindParam(':map_id', $map_id, PDO::PARAM_INT);
			$stmt->execute();

			if($stmt->rowCount() == 0){
				reject($prefix, $GLOBALS['RESPONSE_ERROR_CODE']['DB_NODATA'], "Map not found.");
			}
			$place_id = $stmt->fetchColumn();


			if($event_place_id != $place_id){
				reject($prefix, $GLOBALS['RESPONSE_ERROR_CODE']['DB_NODATA'], "Map & Event does not match.");
			}
		}


		$stmt = $DB_PDO->prepare("DELETE FROM event_table WHERE event_id = :event_id $add_cond");
		$stmt->bindParam(":event_id", $event_id, PDO::PARAM_INT);
		if($fromMap){
			$stmt->bindParam(":map_id", $map_id, PDO::PARAM_INT);
		}
		$stmt->execute();
		
		$rs = [
			"event_id" => $event_id,
			"quantity" => $stmt->rowCount()
		];

		return $rs;
	}
	catch(PDOException $e){
		reject($prefix, $GLOBALS['RESPONSE_ERROR_CODE']['PDO'], $e->getMessage());
	}
}

function setEventTableActive($event_table_id, $status = "open"){
	global $__TABLE_EVENT_CONSTANT;
	$prefix = $GLOBALS['TABLE_PREFIX'];

	try{
		global $DB_PDO;

		
		$stmt = $DB_PDO->prepare("SELECT active FROM event_table WHERE event_table_id = :event_table_id LIMIT 1");
		$stmt->bindParam(':event_table_id', $event_table_id, PDO::PARAM_INT);
		$stmt->execute();

		if($stmt->rowCount() == 0){
			reject($prefix, $GLOBALS['RESPONSE_ERROR_CODE']['DB_NODATA'], "Event Table not found.");
		}
		$active = $stmt->fetchColumn();

		if($active == $__TABLE_EVENT_CONSTANT[$status]){
			reject($prefix, $GLOBALS['RESPONSE_ERROR_CODE']['DB_FAILED'], "Same status.");
		}

		
		$stmt = $DB_PDO->prepare("UPDATE event_table SET active = :active WHERE event_table_id = :event_table_id");
		$stmt->bindParam(':active', $__TABLE_EVENT_CONSTANT[$status], PDO::PARAM_INT);
		$stmt->bindParam(':event_table_id', $event_table_id, PDO::PARAM_INT);
		$stmt->execute();

		if($stmt->rowCount() == 0){
			reject($prefix, $GLOBALS['RESPONSE_ERROR_CODE']['DB_FAILED'], "Event Table active status change failed.");
		}
		

		$rs = [
			"event_table_id" => $event_table_id
		];

		return $rs;
	}
	catch(PDOException $e){
		reject($prefix, $GLOBALS['RESPONSE_ERROR_CODE']['PDO'], $e->getMessage());
	}
}

function setupEventTables($event_id, $place_id){
	global $__TABLE_QUERY;
	$prefix = $GLOBALS['TABLE_PREFIX'];
	$query = $__TABLE_QUERY['defaultTable_db'];

	try{
		global $DB_PDO;


		$stmt = $DB_PDO->prepare("SELECT map_id FROM map WHERE place_id = :place_id");
		$stmt->bindParam(':place_id', $place_id, PDO::PARAM_INT);
		$stmt->execute();
		
		if($stmt->rowCount() == 0){
			reject($prefix, $GLOBALS['RESPONSE_ERROR_CODE']['DB_NODATA'], "Map not found.");
		}
		$map_ids = $stmt->fetchAll(PDO::FETCH_COLUMN, 0);

		foreach($map_ids as $map_id){
			$stmt = $DB_PDO->prepare("INSERT INTO event_table (event_id, $query) SELECT :event_id, $query FROM `table` WHERE map_id = :map_id");
			$stmt->bindParam(':event_id', $event_id, PDO::PARAM_INT);
			$stmt->bindParam(':map_id', $map_id, PDO::PARAM_INT);
			$stmt->execute();
		}


		$rs = [
			"event_id" => $event_id,
			"place_id" => $place_id,
			"map_ids" => $map_ids
		];


		return $rs;
	}
	catch(PDOException $e){
		reject($prefix, $GLOBALS['RESPONSE_ERROR_CODE']['PDO'], $e->getMessage());
	}
}

?>
