<?

if(!isset($_IN_SITE)){
	die("Access denied ai sus!!!");
}

$DB_PDO = null;
init_DB();

function init_DB(){
	global $DB_PDO;

	$db_servername = "localhost";
	$db_username = "ncproduc_tungtoh";
	$db_password = "kuykuykuy";
	$db_name = "ncproduc_tungtoh";

	$DB_PDO = new PDO("mysql:host=$db_servername;dbname=$db_name", $db_username, $db_password);
	$DB_PDO->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
}

?>