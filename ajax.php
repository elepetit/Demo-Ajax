<?php
include 'connexion.php';
$pdo = connect();
$response = '';
/*******************************************************************/
/****************************** PRODUCTS ***************************/
/*******************************************************************/

/************** UPDATE STOCK ******************/

function updateStock($productid, $method, $pdo) {

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

 function get_stock($productid, $pdo){


    $select = "select quantity from products where productid = ?";
    $request = $pdo -> prepare($select);
    $request -> execute([$productid]);
    $result = $request -> fetch(PDO::FETCH_OBJ);
    return $result -> quantity;
};


function get_cart_product ($productid, $pdo){



    $sql = "select * from cart where productid = ?";

    $result = $pdo -> prepare($sql);

    $result -> execute([$productid]);

    $response = $result -> rowCount();

    return $response;

};

/***************** ADD TO CART **********************/

function update_cart ($productid, $quantity, $method, $pdo){



    if($method==='add'){

        $select = 'update cart set quantity= quantity + :quantity where productid = :productid';

    }
    else if($method==='create'){

        $select = 'insert into cart (productid, quantity) VALUES (:productid,:quantity)';

    }
    else if($method==='remove'){

        $select = 'update cart set quantity= quantity - :quantity where productid = :productid';

    }


    $request = $pdo -> prepare($select);
    $request -> bindParam(':quantity', $quantity);
    $request -> bindParam(':productid', $productid);
    $request -> execute();

    /* SUPPRESSION DES LIGNES A ZERO DANS LE CART*/

    $select = "delete from cart where quantity=0";
    $request = $pdo -> prepare($select);
    $request -> execute();

};

/************* ON RECOIT LES DONNEES *********************************************/


if(array_key_exists('method', $_GET) && array_key_exists('productid', $_GET)){

    $productid = $_GET['productid'];
    $method = $_GET['method'];

    /************* ON SWITCH SUR LA METHODE *********************************************/

    if($productid){
        switch($method){
            case 'add':

                /****  ENCORE EN STOCK ****/

                if( get_stock($productid)>0){

                    /*  DEJA EXISTANT DANS LE CART OU PAS */

                    if(get_cart_product($productid)===1){

                        update_cart($productid,1, 'add'); /* ON AJOUTE */
                    }
                    else {

                        update_cart($productid,1, 'create'); /* ON CREE */
                    }
                    /*  ON DIMINUE LE STOCK */

                    updateStock($productid, 'remove');

                }

                /****  PLUS EN STOCK ****/

                else {
                    $response = 'nullStock';
                }
                break;

            case 'remove':
                update_cart($productid,1,'remove');
                updateStock($productid,'add');
                break;

        }
    }



}

/******** GESTION DU RETOUR VERS L ECRAN PRINCIPAL *****************************/

if($response===''){
    $sql = "
      SELECT o.name,cart.quantity as quantity, o.price, o.price*cart.quantity as subtotal, cart.productid
      from cart
      JOIN products o on cart.productid = o.productid
";
    $result = $pdo -> prepare($sql);
    $result -> execute();
    $products = $result ->fetchALL(PDO::FETCH_ASSOC  );
    echo json_encode($products);
}
else {
    echo json_encode($response);
}




