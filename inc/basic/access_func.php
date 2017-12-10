<?

if(!isset($_IN_SITE)){
	die("Access denied ai sus!!!");
}
if(!isset($_SESSION)){
	session_start();
}

$GLOBALS['ACCESS_CONSTANT'] = [
	"LOGGEDIN" => 0,
	"VERIFIED" => 1,
	"OWNER" => 5,
	"MOD" => 8,
	"ADMIN" => 9
];
$GLOBALS['ACCESS_CONSTANT']['ENUM'] = [
	"Not Verified" => $GLOBALS['ACCESS_CONSTANT']['LOGGEDIN'],
	"Verified" => $GLOBALS['ACCESS_CONSTANT']['VERIFIED'],
	"Owner" => $GLOBALS['ACCESS_CONSTANT']['OWNER'],
	"Moderator" => $GLOBALS['ACCESS_CONSTANT']['MOD'],
	"Administrator" => $GLOBALS['ACCESS_CONSTANT']['ADMIN']
];

function access_check($prefix, $previlege = 0, $DataRequired = false){
	if(!isset($_SESSION['member_id'])){
		reject($prefix, $GLOBALS['RESPONSE_ERROR_CODE']['AC_LOGGED'], "Login gon ai sus!!!");
	}

	$_SESSION['status'] = getAccess($_SESSION['member_id']);
	
	if($_SESSION['status'] < $previlege){
		reject($prefix, $GLOBALS['RESPONSE_ERROR_CODE']['AC_LOW'], "Access denied, eiei olo.");
	}
	if($DataRequired){
		return data_check($prefix);
	}
}

function data_check($prefix){
	$data = prepareJSON($prefix, file_get_contents("php://input"));
	
	if(empty($data)){
		reject($prefix, $GLOBALS['RESPONSE_ERROR_CODE']['INFO'], "No data KUYKUYKUYKUY!!!");
	}

	return $data;
}

function checkProfileAccess($member_id){
	if($_SESSION['status'] < $GLOBALS['ACCESS_CONSTANT']['ADMIN'] && $_SESSION['member_id'] != $member_id){
		reject($GLOBALS['PROFILE_PREFIX'], $GLOBALS['RESPONSE_ERROR_CODE']['AC_LOW'], "Not yours. olo");
	}
}

function checkPlaceAccess($place_id){
	if($_SESSION['status'] < $GLOBALS['ACCESS_CONSTANT']['ADMIN']){
		if($_SESSION['status'] == $GLOBALS['ACCESS_CONSTANT']['OWNER']){
			$place_list = getPlaceAccess($_SESSION['member_id']);
			if(!in_array($place_id, $place_list)){
				reject($GLOBALS['PROFILE_PREFIX'], $GLOBALS['RESPONSE_ERROR_CODE']['AC_LOW'], "Not yours. olo");
			}
		}
		else{
			reject($GLOBALS['PROFILE_PREFIX'], $GLOBALS['RESPONSE_ERROR_CODE']['AC_LOW'], "SEUKKKKKKKKKKKKKKKKKKK");
		}
	}
}

function getAccess($member_id){
	try{
		global $DB_PDO;

		$stmt = $DB_PDO->prepare("SELECT status FROM member WHERE member_id = :member_id LIMIT 1");
		$stmt->bindParam(':member_id', $_SESSION['member_id'], PDO::PARAM_INT);
		$stmt->execute();
		
		if($stmt->rowCount() == 0){
			reject($prefix, $GLOBALS['RESPONSE_ERROR_CODE']['DB_NODATA'], "User not found.");
		}

		return $GLOBALS['ACCESS_CONSTANT']['ENUM'][$stmt->fetchColumn()];
	}
	catch(PDOException $e){
		reject($prefix, $GLOBALS['RESPONSE_ERROR_CODE']['PDO'], $e->getMessage());
	}
}

function getPlaceAccess($member_id){
	try{
		global $DB_PDO;

		$stmt = $DB_PDO->prepare("SELECT place FROM member WHERE member_id = :member_id LIMIT 1");
		$stmt->bindParam(':member_id', $_SESSION['member_id'], PDO::PARAM_INT);
		$stmt->execute();
		
		if($stmt->rowCount() == 0){
			reject($prefix, $GLOBALS['RESPONSE_ERROR_CODE']['DB_NODATA'], "User not found.");
		}

		return explode(',', $stmt->fetchColumn());
	}
	catch(PDOException $e){
		reject($prefix, $GLOBALS['RESPONSE_ERROR_CODE']['PDO'], $e->getMessage());
	}
}

?>
