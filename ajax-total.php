<?php
include 'connexion.php';
$pdo = connect();
$sql = "
      SELECT  SUM(o.price*cart.quantity) as total
      from cart
      JOIN products o on o.productid = cart.productid
";
$result = $pdo -> prepare($sql);
$result -> execute();
$total = $result ->fetch(PDO::FETCH_ASSOC  );
echo json_encode($total);
