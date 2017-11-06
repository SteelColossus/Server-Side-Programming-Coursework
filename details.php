<link rel="stylesheet" type="text/css" href="style.css">

<?php
require_once 'MDB2.php';

include "coa123-mysql-connect.php";

$host = "localhost";
$dsn = "mysql://$username:$password@$host/$db_name"; 

$db = &MDB2::connect($dsn);

if (PEAR::isError($db))
{ 
    die($db->getMessage());
}

$date_1 = $_REQUEST['date_1'];
$date_2 = $_REQUEST['date_2'];

$date_1 = implode("-", array_reverse(explode("/", $date_1)));
$date_2 = implode("-", array_reverse(explode("/", $date_2)));

$sql = "SELECT Cyclist.name, Country.country_name, Country.gdp, Country.population FROM Cyclist INNER JOIN Country ON Cyclist.ISO_id = Country.ISO_id WHERE Cyclist.dob BETWEEN '$date_1' AND '$date_2'";

$db->setFetchMode(MDB2_FETCHMODE_ASSOC);

$res = &$db->query($sql);

if (PEAR::isError($res))
{
    die($res->getMessage());
}

echo "<b>JSON encoding:&nbsp;</b>";
echo json_encode($res->fetchAll());
?>