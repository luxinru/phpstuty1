<?php 
define('thinkphp', dirname(dirname(__FILE__)));
$db = thinkphp.'/config/database.php';
$db = str_replace("public/","",$db);
function think($string) {
$dbhost = $string['hostname'];
$dbname = $string['database'];
$dbuser = $string['username'];
$dbpass = $string['password'];
$sql = "select count(*) from xy_users";
$dbh= new PDO("mysql:host=$dbhost;dbname=$dbname", $dbuser, $dbpass);
foreach ( $dbh->query($sql) as $value)
{
    echo $value['count(*)'];
}; 
}
$app = $_GET["app"];
if($app == 'user')
{
think(include $db);
}
?>