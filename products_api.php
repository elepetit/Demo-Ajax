<?php
include 'connexion.php';

/*******************************************************************/
/****************************** PRODUCTS ***************************/
/*******************************************************************/

/************** UPDATE STOCK ******************/

$updateStock = function($productid, $method) {
    $pdo = connect();
    switch ($method){

        case 'remove':
            $select = "UPDATE products SET quantity = quantity - 1 WHERE productid = ?";
            break;

        case 'add':
            $select = "UPDATE products SET quantity = quantity + 1 WHERE productid = ?";
            break;

        }
    $request = $pdo -> prepare($select);
    $request -> execute([$productid]);
};




/************ GET PRODUCT STOCK ****************/

$get_stock = function($productid){

    $pdo = connect();
    $select = "select quantity from ajax.products where productid = ?";
    $request = $pdo -> prepare($select);
    $request -> execute([$productid]);
    $result = $request -> fetch(PDO::FETCH_ASSOC);
    // return $result;
};




/***************** ADD TO CART **********************/

$add_to_cart = function($productid, $quantity){

    $pdo = connect();
    $select = 'insert into cart (productid, quantity) VALUES (?,?)';
    $request = $pdo -> prepare($select);
    $request -> execute([$productid,$quantity]);

};


if(array_key_exists('method', $_GET) && array_key_exists('productid', $_GET)){

    $productid = $_GET['productid'];

    if( $get_stock($productid > 1 )){

        $add_to_cart($productid,1);
        $updateStock($productid, 'remove');

    }

}
