<?php
include 'connexion.php';
$pdo = connect();
$sql = " SELECT * from products ";
$result = $pdo -> prepare($sql);
$result -> execute();
$products = $result ->fetchALL(PDO::FETCH_ASSOC  );
echo json_encode($products);
