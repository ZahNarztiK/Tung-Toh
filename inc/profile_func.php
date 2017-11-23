<?

if(!isset($_IN_SITE)){
	die("Access denied ai sus!!!");
}

require_once("../../inc/db_connect.php");
require_once("../../inc/init_response_func.php");

$__PROFILE_PREFIX = "IU";



function getProfile($member_id){
	global $__PROFILE_PREFIX;
	$prefix = $__PROFILE_PREFIX;

	try{
		global $DB_PDO;


		$stmt = $DB_PDO->prepare("SELECT verified, firstname, lastname, level, points, profile_image, email FROM member WHERE member_id = :member_id LIMIT 1");
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

?>