<?

if(!isset($_IN_SITE)){
	die("Access denied ai sus!!!");
}

require_once("../../inc/db_connect.php");
require_once("../../inc/init_response_func.php");
require_once("../../inc/access_func.php");

$__PROFILE_PREFIX = "IU";
$__PROFILE_DEFAULT = [
	"firstname" => "",
	"lastname" => "",
	"tel" => "",
	"profile_image" => ""
];
$__PROFILE_INFO_QUERY = "email, verified, firstname, lastname, tel, level, points, profile_image";



function editProfile($profile_raw){
	global $__PROFILE_PREFIX;
	$prefix = $__PROFILE_PREFIX;

	try{
		global $DB_PDO;
		
		$profile = prepareProfileData($profile_raw, true);


		checkProfileAccess($profile['member_id']);
		

		$stmt = $DB_PDO->prepare("SELECT member_id FROM member WHERE member_id = :member_id LIMIT 1");
		$stmt->bindParam(':member_id', $profile['member_id'], PDO::PARAM_INT);
		$stmt->execute();

		if($stmt->rowCount() == 0){
			reject($prefix, "14", "Member not found.");
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
		reject($prefix, "10", $e->getMessage());
	}
}

function getProfile($member_id){
	global $__PROFILE_PREFIX, $__PROFILE_INFO_QUERY;
	$prefix = $__PROFILE_PREFIX;

	try{
		global $DB_PDO;


		checkProfileAccess($member_id);
		

		$stmt = $DB_PDO->prepare("SELECT $__PROFILE_INFO_QUERY FROM member WHERE member_id = :member_id LIMIT 1");
		$stmt->bindParam(':member_id', $member_id, PDO::PARAM_INT);
		$stmt->execute();
		
		if($stmt->rowCount() == 0){
			reject($prefix, "14", "No info.");
		}

		$rs = $stmt->fetch(PDO::FETCH_ASSOC);

		return $rs;
	}
	catch(PDOException $e){
		reject($prefix, "10", $e->getMessage());
	}
}

function checkProfileAccess($member_id){
	global $__PROFILE_PREFIX, $__ACCESS_CONSTANT;

	if($_SESSION['verified'] < $__ACCESS_CONSTANT['ADMIN']  && $_SESSION['member_id'] != $member_id){
		reject($__PROFILE_PREFIX, "90", "Not yours. olo");
	}
}

function prepareProfileData($profile_raw, $isEdit = false){
	global $__PROFILE_PREFIX, $__PROFILE_DEFAULT;

	$error = [];
	$prefix = $__PROFILE_PREFIX;

	$profile = prepareJSON($prefix, $profile_raw, $__PROFILE_DEFAULT);


	if($isEdit && (!isset($profile['member_id']) || notPositiveInt($profile['member_id']))){
		$error[] = "Member ID";
	}

	if(!empty($error)){
		reject($prefix, "04", "Error parameter(s) - ".implode(", ", $error));
	}


	$profile['firstname'] = trim($profile['firstname']);
	$profile['lastname'] = trim($profile['lastname']);
	$profile['tel'] = trim($profile['tel']);
	if(!validTel($profile['tel'])){
		$profile['tel'] = $__PROFILE_DEFAULT['tel'];
	}
	$profile['profile_image'] = trim($profile['profile_image']);


	return $profile;
}

?>
