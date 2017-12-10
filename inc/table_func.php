<?

if(!isset($_IN_SITE)){
	die("Access denied ai sus!!!");
}

require_once("../../inc/basic_func.php");
require_once("../../inc/table_subfunc/default_table_func.php");
require_once("../../inc/table_subfunc/event_table_func.php");

require_once("../../inc/map_func.php");
require_once("../../inc/event_func.php");

$GLOBALS['TABLE_PREFIX'] = "IT";
$__TABLE_DEFAULT = [
	"defaultTable" => [
		"code" => "",
		"x" => 0,
		"y" => 0,
		"rotation" => 0,
		"table_type_id" => 0
	],
	"eventTable" => [
		"x" => 0,
		"y" => 0,
		"rotation" => 0,
		"table_type_id" => 0
	]
];
$__TABLE_DATA_REQUIRED = [
	"addTable" => [
		"+int*" => [ "map_id" ],
		"str*" => [ "code" ],
		"int" => [ "x", "y", "rotation" ],
		"+int" => [ "table_type_id" ]
	],
	"editTable" => [
		"+int*" => [ "table_id" ],
		"str*" => [ "code" ],
		"int" => [ "x", "y", "rotation" ],
		"+int" => [ "table_type_id" ]
	],
	"addEventTable" => [
		"+int*" => [ "event_id", "table_id" ]
	],
	"editEventTable" => [
		"+int*" => [ "event_table_id" ],
		"int" => [ "x", "y", "rotation" ],
		"+int" => [ "table_type_id" ]
	],
];
$__TABLE_QUERY = [
	"defaultTable" => "table_id, map_id, place_id, code, X(location) as x, Y(location) as y, rotation, table_type_id",
	"defaultTable_db" => "table_id, map_id, place_id, code, location, rotation, table_type_id"
];
$__TABLE_QUERY['eventTable'] = 
	"event_table_ID, event_id, ".$__TABLE_QUERY["defaultTable"].", booking_id, status";

?>
