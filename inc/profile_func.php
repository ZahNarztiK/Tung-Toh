<?

if(!isset($_IN_SITE)){
	die("Access denied ai sus!!!");
}

require_once("../../inc/db_connect.php");



function getProfile($member_id){
	try{
		global $DB_PDO;


		$stmt = $DB_PDO->prepare("SELECT TOP 1 firstname, lastname, level, points, profile_image, email FROM member WHERE member_id = :member_id");
		$stmt->bindParam(':member_id', $member_id, PDO::PARAM_INT);
		$stmt->execute();
		
		if($stmt->rowCount() == 0){
			reject("IP14", "No info.");
		}

		$rs = $stmt->fetch(PDO::FETCH_ASSOC);

		return $rs;
	}
	catch(PDOException $e){
		reject("IP10", $e->getMessage());
	}
}

?>