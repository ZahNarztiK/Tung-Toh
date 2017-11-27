<?
error_reporting(-1);
ini_set('display_errors', 'On');
set_error_handler("var_dump");

$kuy = ["ok"=>9];
var_dump($kuy);
$kuy=eiei($kuy);
var_dump($kuy);
echo is_numeric("123")?"true":"false";
function eiei($kuy){
	echo isset($kuy);
}

echo mail("zahnarztik@gmail.com", "test", "long du si", "From: admin@nc-production.net");


?>
