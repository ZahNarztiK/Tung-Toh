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
	"ADMIN" => 9
];

function access_check($prefix, $previlege = 0, $DataRequired = false){
	if(!isset($_SESSION['member_id'])){
		reject($prefix, $GLOBALS['RESPONSE_ERROR_CODE']['AC_LOGGED'], "Login gon ai sus!!!");
	}

	try{
		global $DB_PDO;

		$stmt = $DB_PDO->prepare("SELECT verified FROM member WHERE member_id = :member_id LIMIT 1");
		$stmt->bindParam(':member_id', $_SESSION['member_id'], PDO::PARAM_INT);
		$stmt->execute();
		
		if($stmt->rowCount() == 0){
			reject($prefix, $GLOBALS['RESPONSE_ERROR_CODE']['DB_NODATA'], "User not found.");
		}
		$_SESSION['verified'] = $stmt->fetchColumn();
	}
	catch(PDOException $e){
		reject($prefix, $GLOBALS['RESPONSE_ERROR_CODE']['PDO'], $e->getMessage());
	}
	
	if($_SESSION['verified'] < $previlege){
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

?>
