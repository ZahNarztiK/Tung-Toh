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
	if(!isset($_SESSION['verified'])){
		reject($prefix, $GLOBALS['RESPONSE_ERROR_CODE']['AC_LOGGED'], "Login gon ai sus!!!");
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
