<?php
include '../functions/functions.php';

$queryResult = '';
$rootElementName = 'Order';
$childElementName = 'Buyer';

$obj = new project();
$results = $obj->sqlToXml($rootElementName, $childElementName);
var_dump($results);

?>