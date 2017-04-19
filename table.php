<?php
header('Content-Type: application/json');

require_once 'MDB2.php';

include "coa123-mysql-connect.php";

$host = "localhost";
$dsn = "mysql://$username:$password@$host/$db_name"; 

$db = &MDB2::connect($dsn);

$db->setFetchMode(MDB2_FETCHMODE_ASSOC);

if(PEAR::isError($db))
{
    die($db->getMessage());
}

$country1 = (isset($_REQUEST['country1']) ? $_REQUEST['country1'] : null);
$country2 = (isset($_REQUEST['country2']) ? $_REQUEST['country2'] : null);
$country3 = (isset($_REQUEST['country3']) ? $_REQUEST['country3'] : null);
$country4 = (isset($_REQUEST['country4']) ? $_REQUEST['country4'] : null);

$countries = array();

if ($country1 != null) $countries['country1'] = $country1;
if ($country2 != null) $countries['country2'] = $country2;
if ($country3 != null) $countries['country3'] = $country3;
if ($country4 != null) $countries['country4'] = $country4;

$column_names = array("country_name", "gold", "silver", "bronze", "total", "population", "gdp");

$sql_base = "SELECT " . implode(", ", $column_names) . " FROM Country WHERE ISO_id LIKE";
$sql_full = "";

$i = 0;

foreach ($countries as $c)
{	
	$sql_full .= ($sql_base . " \"");
	$sql_full .= $c;
	$sql_full .= "\"";
	if (++$i < count($countries)) $sql_full .= " UNION ";
}

$res = &$db->query($sql_full);

if(PEAR::isError($res))
{
	die($res->getMessage());
}

if ($res->numRows() > 1)
{
	$compdata = &$res->fetchAll();
	
	$sql_base = "SELECT Country.ISO_id, Cyclist.name, Country.country_name FROM Cyclist INNER JOIN Country ON Cyclist.ISO_id = Country.ISO_id WHERE Cyclist.ISO_id LIKE";
	$sql_full = "";

	$i = 0;

	foreach ($countries as $c)
	{	
		$sql_full .= ($sql_base . " \"");
		$sql_full .= $c;
		$sql_full .= "\"";
		
		if (++$i < count($countries))
		{
			$sql_full .= " UNION ";
		}
		else
		{
			$sql_full = "SELECT name, country_name FROM (" . $sql_full;
			$sql_full .= " ORDER BY ISO_id, name) results";
		}
	}
	
	$res = &$db->query($sql_full);

	if(PEAR::isError($res))
	{
		die($res->getMessage());
	}
	
	$cycdata = &$res->fetchAll();
	echo json_encode(array("countries" => $compdata, "cyclists" => $cycdata));
}
?>