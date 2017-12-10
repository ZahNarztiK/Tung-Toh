<?
echo "---===[ Test ]===---<br><br>";

//error_reporting(-1);
//ini_set('display_errors', 'On');
//set_error_handler("var_dump");

$kuy = ["a"=>"asd","b"=>"asdfsdaf","c"=>"411471"];//["ok"=>9,"aaa"=>1,"asdf"=>999];
//var_dump($kuy);
//lol($kuy);
//echo preg_match("/^.*(\*)$/", "+int", $rs);
//preg_match("/^(.*[^\*])\*?$/", "+int*", $rs);
//var_dump($rs);
echo "<br><br><br>";
//echo $rs[1];

lol($kuy, 123);

//$GLOBALS['ok'] = "555+";
session_start();
echo (in_array("asdfkkkkk", $kuy) ==0?"T":"F");


function lol(&$eiei){
	foreach ($eiei as $key => $value) {
		unset($eiei[$key]);
		
	}
}

//var_dump($kuy);

echo "<br><br><br>";
//echo (null ==0?"T":"F");
//echo is_numeric("123")?"true":"false";


//echo mail("zahnarztik@gmail.com", "test", "long du si", "From: admin@nc-production.net")."<br>";
//echo mail("zahnarztik@windowslive.com", "test", "long du si", "From: admin@nc-production.net")."<br>";

?>
