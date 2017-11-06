<?php
header('Content-Type: application/json');

require_once 'MDB2.php';

include "coa123-mysql-connect.php";

$host = "localhost";
$dsn = "mysql://$username:$password@$host/$db_name"; 

$db = &MDB2::connect($dsn);

$db->setFetchMode(MDB2_FETCHMODE_ASSOC);

if (PEAR::isError($db))
{
    die($db->getMessage());
}

$sql = "SELECT ISO_id FROM Country";

$res = &$db->query($sql);

echo json_encode($res->fetchAll());
?>