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

$country_id = $_REQUEST['country_id'];
$part_name = $_REQUEST['part_name'];

$sql = "SELECT name, height, weight, gender FROM Cyclist WHERE ISO_id = '$country_id' AND name LIKE '%$part_name%'";

$db->setFetchMode(MDB2_FETCHMODE_ASSOC);

$res = &$db->query($sql);

if (PEAR::isError($res))
{
    die($res->getMessage());
}

if ($res->numRows() > 0)
{
    echo "<table border=\"1\">";
    echo "<tr><th>Name</th><th>Gender</th><th>BMI</th></tr>";

    while ($row = $res->fetchRow())
    {
        echo "<tr>";
        
        echo "<td>" . $row['name'] . "</td>";
        echo "<td>" . ($row['gender'] == "F" ? "Female" : "Male") . "</td>";
        
        echo "<td>";
        
        if ($row['height'] > 0)
        {
            $hcm = $row['height'] / 100;
            
            echo round($row['weight']/($hcm*$hcm), 3);
        }
        else
        {
            echo "ERR";
        }
        
        echo "</td>";
        
        echo "</tr>";
    }

    echo "</table>";
}
else
{
    echo "No cyclists match this criteria.";
}
?>