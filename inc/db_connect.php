<?

if(!isset($in_site))
	die("Access denied ai sus!!!");

$db_servername = "localhost";
$db_username = "ncproduc_tungtoh";
$db_password = "kuykuykuy";
$db_name = "ncproduc_tungtoh";

$db_pdo = new PDO("mysql:host=$db_servername;dbname=$db_name", $db_username, $db_password);
$db_pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

?>