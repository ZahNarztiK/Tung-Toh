<?

$kuy = ["ok"=>9];
var_dump($kuy);
$kuy=eiei($kuy);
var_dump($kuy);

function eiei($kuy){
	$kuy['ok']=5555;
	return $kuy;
}

?>