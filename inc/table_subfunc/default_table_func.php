<?

if(!isset($_IN_SITE)){
	die("Access denied ai sus!!!");
}

function addTable($table_raw){
	global $__TABLE_DEFAULT, $__TABLE_DATA_REQUIRED;
	$prefix = $GLOBALS['TABLE_PREFIX'];

	try{
		global $DB_PDO;

		$table = prepareJSON($prefix, $table_raw, $__TABLE_DATA_REQUIRED['addTable'], $__TABLE_DEFAULT['defaultTable']);


		$stmt = $DB_PDO->prepare("SELECT place_id FROM map WHERE map_id = :map_id LIMIT 1");
		$stmt->bindParam(':map_id', $table['map_id'], PDO::PARAM_INT);
		$stmt->execute();
		
		if($stmt->rowCount() == 0){
			reject($prefix, $GLOBALS['RESPONSE_ERROR_CODE']['DB_NODATA'], "Location not found.");
		}
		$table['place_id'] = $stmt->fetchColumn();


		$stmt = $DB_PDO->prepare("SELECT code FROM `table` WHERE map_id = :map_id AND code = :code LIMIT 1");
		$stmt->bindParam(':map_id', $table['map_id'], PDO::PARAM_INT);
		$stmt->bindParam(':code', $table['code']);
		$stmt->execute();
		
		if($stmt->rowCount() > 0){
			reject($prefix, $GLOBALS['RESPONSE_ERROR_CODE']['DB_DUPLICATED'], "Duplicated table code.");
		}


		$stmt = $DB_PDO->prepare("INSERT INTO `table` (map_id, place_id, code, location, rotation, table_type_id) VALUES (:map_id, :place_id, :code, POINT(:x, :y), :rotation, :table_type_id)");
		$stmt->bindParam(':map_id', $table['map_id'], PDO::PARAM_INT);
		$stmt->bindParam(':place_id', $table['place_id'], PDO::PARAM_INT);
		$stmt->bindParam(':code', $table['code']);
		$stmt->bindParam(':x', $table['x'], PDO::PARAM_INT);
		$stmt->bindParam(':y', $table['y'], PDO::PARAM_INT);
		$stmt->bindParam(':rotation', $table['rotation'], PDO::PARAM_INT);
		$stmt->bindParam(':table_type_id', $table['table_type_id'], PDO::PARAM_INT);
		$stmt->execute();
		
		$table_id = $DB_PDO->lastInsertId();
		if($table_id == 0){
			reject($prefix, $GLOBALS['RESPONSE_ERROR_CODE']['DB_FAILED'], "Table add failed.");
		}
		
		$rs = getTable($table_id);

		return $rs;
	}
	catch(PDOException $e){
		reject($prefix, $GLOBALS['RESPONSE_ERROR_CODE']['PDO'], $e->getMessage());
	}
}

function editTable($table_raw){
	global $__TABLE_DEFAULT, $__TABLE_DATA_REQUIRED;
	$prefix = $GLOBALS['TABLE_PREFIX'];

	try{
		global $DB_PDO;

		$table = prepareJSON($prefix, $table_raw, $__TABLE_DATA_REQUIRED['editTable'], $__TABLE_DEFAULT['defaultTable']);


		$stmt = $DB_PDO->prepare("SELECT map_id FROM `table` WHERE table_id = :table_id LIMIT 1");
		$stmt->bindParam(':table_id', $table['table_id'], PDO::PARAM_INT);
		$stmt->execute();
		
		if($stmt->rowCount() == 0){
			reject($prefix, $GLOBALS['RESPONSE_ERROR_CODE']['DB_NODATA'], "Table not found.");
		}
		$table['map_id'] = $stmt->fetchColumn();

		
		$stmt = $DB_PDO->prepare("SELECT code FROM `table` WHERE map_id = :map_id AND code = :code AND table_id != :table_id LIMIT 1");
		$stmt->bindParam(':map_id', $table['map_id'], PDO::PARAM_INT);
		$stmt->bindParam(':code', $table['code']);
		$stmt->bindParam(':table_id', $table['table_id'], PDO::PARAM_INT);
		$stmt->execute();
		
		if($stmt->rowCount() > 0){
			reject($prefix, $GLOBALS['RESPONSE_ERROR_CODE']['DB_DUPLICATED'], "Duplicated table code.");
		}


		$stmt = $DB_PDO->prepare("UPDATE `table` SET location = POINT(:x, :y), rotation = :rotation, table_type_id = :table_type_id WHERE table_id = :table_id");
		$stmt->bindParam(':code', $table['code']);
		$stmt->bindParam(':x', $table['x'], PDO::PARAM_INT);
		$stmt->bindParam(':y', $table['y'], PDO::PARAM_INT);
		$stmt->bindParam(':rotation', $table['rotation'], PDO::PARAM_INT);
		$stmt->bindParam(':table_type_id', $table['table_type_id'], PDO::PARAM_INT);
		$stmt->bindParam(':table_id', $table['table_id'], PDO::PARAM_INT);
		$stmt->execute();
		

		$rs = [
			"table_id" => $table['table_id']
		];

		return $rs;
	}
	catch(PDOException $e){
		reject($prefix, $GLOBALS['RESPONSE_ERROR_CODE']['PDO'], $e->getMessage());
	}
}

function getTable($table_id){
	global  $__TABLE_QUERY;
	$prefix = $GLOBALS['TABLE_PREFIX'];
	$query =  $__TABLE_QUERY['defaultTable'];

	try{
		global $DB_PDO;


		$stmt = $DB_PDO->prepare("SELECT $query FROM `table` WHERE table_id = :table_id LIMIT 1");
		$stmt->bindParam(':table_id', $table_id, PDO::PARAM_INT);
		$stmt->execute();
		
		if($stmt->rowCount() == 0){
			reject($prefix, $GLOBALS['RESPONSE_ERROR_CODE']['DB_NODATA'], "Table not found.");
		}
		
		$rs = $stmt->fetch(PDO::FETCH_ASSOC);
		
		return $rs;
	}
	catch(PDOException $e){
		reject($prefix, $GLOBALS['RESPONSE_ERROR_CODE']['PDO'], $e->getMessage());
	}
}

function getTableList($map_id){
	global $__TABLE_QUERY;
	$prefix = $GLOBALS['TABLE_PREFIX'];
	$query =  $__TABLE_QUERY['defaultTable'];

	try{
		global $DB_PDO;


		$stmt = $DB_PDO->prepare("SELECT map_id FROM map WHERE map_id = :map_id LIMIT 1");
		$stmt->bindParam(':map_id', $map_id, PDO::PARAM_INT);
		$stmt->execute();
		
		if($stmt->rowCount() == 0){
			reject($prefix, $GLOBALS['RESPONSE_ERROR_CODE']['DB_NODATA'], "Map not found.");
		}


		$stmt = $DB_PDO->prepare("SELECT $query FROM `table` WHERE map_id = :map_id");
		$stmt->bindParam(':map_id', $map_id, PDO::PARAM_INT);
		$stmt->execute();
				
		$rs = [
			"quantity" => $stmt->rowCount(),
			"table_list" => $stmt->fetchAll(PDO::FETCH_CLASS)
		];

		return $rs;
	}
	catch(PDOException $e){
		reject($prefix, $GLOBALS['RESPONSE_ERROR_CODE']['PDO'], $e->getMessage());
	}
}

function removeTable($table_id){
	$prefix = $GLOBALS['TABLE_PREFIX'];

	try{
		global $DB_PDO;

		
		$stmt = $DB_PDO->prepare("DELETE FROM `table` WHERE table_id = :table_id");
		$stmt->bindParam(':table_id', $table_id, PDO::PARAM_INT);
		$stmt->execute();
		
		if($stmt->rowCount() == 0){
			reject($prefix, $GLOBALS['RESPONSE_ERROR_CODE']['DB_NODATA'], "Table not found.");
		}
		
		$rs = [
			"table_id" => $table_id
		];

		return $rs;
	}
	catch(PDOException $e){
		reject($prefix, $GLOBALS['RESPONSE_ERROR_CODE']['PDO'], $e->getMessage());
	}
}

function removeTableList($identifier, $identifier_id){
	$prefix = $GLOBALS['TABLE_PREFIX'];
	$identifier_list = ["map_id", "place_id"];

	if(!in_array($identifier, $identifier_list) || notPositiveInt($identifier_id)){
		reject($prefix, $GLOBALS['RESPONSE_ERROR_CODE']['PHP'], "Identifier error.");
	}

	try{
		global $DB_PDO;

		
		$stmt = $DB_PDO->prepare("DELETE FROM `table` WHERE $identifier = :$identifier");
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

?>
