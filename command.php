<?php
include 'connexion.php';
$pdo = connect();

$sql = "
      SELECT productid,quantity
      from cart
";
$result = $pdo -> prepare($sql);
$result -> execute();

$command = strtotime("now");

while($row = $result->fetch(PDO::FETCH_ASSOC)){
    $sql = 'INSERT INTO command (reference, productid, quantity) VALUES (?, ?, ?)';
    $request = $pdo -> prepare($sql);
    $request ->execute([$command, $row['productid'], $row['quantity']]);
}
$sql = "TRUNCATE TABLE cart";
$request = $pdo -> prepare($sql);
$request ->execute();
echo json_encode('ok');
