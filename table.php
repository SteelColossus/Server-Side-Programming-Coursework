<?php
require_once 'MDB2.php';

include "coa123-mysql-connect.php";

function num_to_short_form($num) {
	$num = intval($num);
	
	if ($num <= 0) return strval($num);
		
	$dg = floor(log10($num) + 1);	
	$short_num = $num / pow(10, floor(($dg - 1) / 3) * 3);
	$short_dg = floor(log10($short_num) + 1);
	$short_num = round($short_num, 3 - $short_dg);
	
	$text = strval($short_num);
	
	if ($dg > 12)
	{
		$text .= " trillion";
	}
	elseif ($dg > 9)
	{
		$text .= " billion";
	}
	elseif ($dg > 6)
	{
		$text .= " million";
	}
	elseif ($dg > 3)
	{
		$text .= " thousand";
	}
	
	return $text;
}

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
	$data = &$res->fetchAll();
	
	$countries_str = "<table>";
	
	for ($cn = 0; $cn < count($data[0]); $cn++)
	{
		$col_name = $column_names[$cn];
		
		$highest = -1;
		$highest_row = -1;
		
		if (is_numeric($data[0][$col_name]))
		{
			for ($rn = 0; $rn < count($data); $rn++)
			{
				if ($highest == -1 || $data[$rn][$col_name] > $highest)
				{
					$highest = intval($data[$rn][$col_name]);
					$highest_row = $rn;
				}
			}
		}
		
		$countries_str .= "<tr>";
		
		for ($rn = -1; $rn < count($data); $rn++)
		{
			$countries_str .= ($cn == 0) ? "<th" : "<td";
			
			$countries_str .= " class=\"" . (($rn == -1) ? "center" : "resultscell") . "\"" . " style=\"" . (($col_name == "total") ? "font-weight:bold;" : "") . (($highest_row >= 0 && $rn == $highest_row) ? "background-color:#0fff00;" : "") . "\"" . ">";
			
			if ($rn >= 0)
			{
				$text = $data[$rn][$col_name];
				
				if (is_numeric($text))
				{
					$text = num_to_short_form($text);
					if ($col_name == "gdp") $text = "$" . $text;
				}
				
				$countries_str .= $text;
			}
			elseif ($cn > 0)
			{
				$emoji = "";
				
				switch ($col_name)
				{
					case "total":
						$emoji = "🏅";
						break;
					case "gold":
						$emoji = "🥇";
						break;
					case "silver":
						$emoji = "🥈";
						break;
					case "bronze":
						$emoji = "🥉";
						break;
				}
				
				$countries_str .= $emoji . " " . strtolower($col_name) . " " . $emoji;				
			}
			
			$countries_str .= ($cn == 0) ? "</th>" : "</td>";
		}
		
		$countries_str .= "</tr>";
		
		if ($col_name == "total")
		{
			$countries_str .= "<tr><td colspan=\"3\">&nbsp;</td></tr>";
		}
	}
	
	$countries_str .= "</table>";
	
	echo $countries_str;
	echo "<br>";

	$sql_base = "SELECT Cyclist.iso_id, Cyclist.name, Country.country_name FROM Cyclist INNER JOIN Country ON Cyclist.ISO_id = Country.ISO_id WHERE Cyclist.ISO_id LIKE ";
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
			$sql_full .= " ORDER BY iso_id, name";
		}
	}
	
	$res = &$db->query($sql_full);

	if(PEAR::isError($res))
	{
		die($res->getMessage());
	}
	
	$cyclists_str = "<table border=\"1\">";
	
	$prev_id = "";
	$new_row = false;
	
	while ($row =& $res->fetchRow())
	{
		if ($prev_id == "" || $prev_id != $row['iso_id'])
		{	
			if ($prev_id != "") $cyclists_str .= "</tr>";
	
			$cyclists_str .= "<tr>";
			$cyclists_str .= "<th>".$row['country_name']."</th>";
			
			$prev_id = $row['iso_id'];
		}
		
		$cyclists_str .= "<td>".$row['name']."</td>";
	}

	echo "</tr>";

	$cyclists_str .= "</table>";
	
	echo $cyclists_str;
}
?>