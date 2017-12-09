<?

if(!isset($_IN_SITE)){
	die("Access denied ai sus!!!");
}

require_once("../../inc/basic_func.php");

$GLOBALS['PROFILE_PREFIX'] = "IU";
$__PROFILE_DEFAULT = [
	"firstname" => "",
	"lastname" => "",
	"tel" => "",
	"profile_image" => ""
];
$__PROFILE_DATA_REQUIRED = [
	"editProfile" => [
		"+int*" => [ "member_id" ],
		"str*" => [ "name" ],
		"str" => [ "firstname", "lastname", "tel", "profile_image" ]
	]
];
$__PROFILE_INFO_QUERY = "email, verified, firstname, lastname, tel, level, points, profile_image";



function editProfile($profile_raw){
	global $__PROFILE_DEFAULT, $__PROFILE_DATA_REQUIRED;
	$prefix = $GLOBALS['PROFILE_PREFIX'];

	try{
		global $DB_PDO;
		
		$profile = prepareJSON($prefix, $profile_raw, $__PROFILE_DATA_REQUIRED['editProfile'], $__PROFILE_DEFAULT);


		checkProfileAccess($profile['member_id']);
		

		$stmt = $DB_PDO->prepare("SELECT member_id FROM member WHERE member_id = :member_id LIMIT 1");
		$stmt->bindParam(':member_id', $profile['member_id'], PDO::PARAM_INT);
		$stmt->execute();

		if($stmt->rowCount() == 0){
			reject($prefix, $GLOBALS['RESPONSE_ERROR_CODE']['DB_NODATA'], "Member not found.");
		}


		$stmt = $DB_PDO->prepare("UPDATE member SET firstname = :firstname, lastname = :lastname, tel = :tel, profile_image = :profile_image WHERE member_id = :member_id");
		$stmt->bindParam(':firstname', $profile['firstname']);
		$stmt->bindParam(':password', $profile['lastname']);
		$stmt->bindParam(':tel', $profile['tel']);
		$stmt->bindParam(':profile_image', $profile['profile_image']);
		$stmt->bindParam(':member_id', $profile['member_id'], PDO::PARAM_INT);
		$stmt->execute();
		

		$rs = [
			"member_id" => $profile['member_id']
		];
		
		return $rs;
	}
	catch(PDOException $e){
		reject($prefix, $GLOBALS['RESPONSE_ERROR_CODE']['PDO'], $e->getMessage());
	}
}

function getProfile($member_id){
	global $__PROFILE_INFO_QUERY;
	$prefix = $GLOBALS['PROFILE_PREFIX'];

	try{
		global $DB_PDO;


		checkProfileAccess($member_id);
		

		$stmt = $DB_PDO->prepare("SELECT $__PROFILE_INFO_QUERY FROM member WHERE member_id = :member_id LIMIT 1");
		$stmt->bindParam(':member_id', $member_id, PDO::PARAM_INT);
		$stmt->execute();
		
		if($stmt->rowCount() == 0){
			reject($prefix, $GLOBALS['RESPONSE_ERROR_CODE']['DB_NODATA'], "No info.");
		}

		$rs = $stmt->fetch(PDO::FETCH_ASSOC);

		return $rs;
	}
	catch(PDOException $e){
		reject($prefix, $GLOBALS['RESPONSE_ERROR_CODE']['PDO'], $e->getMessage());
	}
}

function checkProfileAccess($member_id){
	if($_SESSION['verified'] < $GLOBALS['ACCESS_CONSTANT']['ADMIN']  && $_SESSION['member_id'] != $member_id){
		reject($GLOBALS['PROFILE_PREFIX'], $GLOBALS['RESPONSE_ERROR_CODE']['AC_LOW'], "Not yours. olo");
	}
}

?>
