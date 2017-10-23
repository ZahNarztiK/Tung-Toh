<?

if(!isset($_SESSION))
	session_start();

if(!isset($_SESSION['member_id']))
	reject("Login gon ai sus!!!");

$execute = !isset($in_site);
$in_site = true;
require_once("../../inc/init_response_func.php");
require_once("../../inc/db_connect.php");

if($execute){
	$member_id = $_SESSION['member_id'];
	if(isset($_GET['member_id']) && !is_nan($_GET['member_id']))
		$member_id = $_GET['member_id'];
	$rs = getProfile($member_id);
	set_response($rs);
	success("Ow pai!");
}





function getProfile($member_id){
	try{
		global $db_pdo;

		$stmt = $db_pdo->prepare("SELECT firstname, lastname, level, points, profile_image, email FROM member WHERE member_id = :member_id");
		$stmt->bindParam(':member_id', $member_id);
		$stmt->execute();
		
		if($stmt->rowCount()==0)
			reject("No info.");
		
		$rs = $stmt->fetch(PDO::FETCH_ASSOC);

		return $rs;
	}
	catch(PDOException $e){
		reject($e->getMessage());
	}
}

?>