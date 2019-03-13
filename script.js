"use strict";
/*******************************************************/
/********* VALIDATION DE LA COMMANDE ***********/
/******************************************************/

var onClickCmd = function () {

    $.getJSON('command.php', '', onAjaxCommandSuccess);

};

/*******************************************************/
/********* TRAITEMENT DU RETOUR DE LA COMMANDE ***********/
/******************************************************/

var onAjaxCommandSuccess = function($response){
    if($response==='ok'){
        init();
    }

};

/*******************************************************/
/***** ENVOI DES REQUETES AJAX Ajout/Suppression ******/
/*****************************************************/

var ajax_request = function(method, productid){

    var data = 'method=' + method +'&productid=' + productid;

    $.getJSON('ajax.php', data, onAjaxSuccess);


};

/******************************************/
/***** TRAITEMENT DES RETOURS DU PHP ******/
/******************************************/

var onAjaxSuccess = function(response){

    if(response === 'nullStock'){

        $( "#dialog-1" ).dialog( "open" );

    }
    else {

        /******* CONSITUTION DE LA LISTE DU CART ******/

        var ul = $('<ul>').addClass('item-list');

        $(response).each(function(index, value){
            if(value.quantity === 0){
                return
            }
            $(ul).append('<li>' + value.name + ' Qté : ' + value.quantity + ' - Unitaire : ' + value.price + '€ - Ss/total : ' + value.subtotal + '€ <a href="#" class="remove-from-cart" data-method="remove" data-index="' + value.productid + ' "><i class="fas fa-trash-alt"></i></a></li>');

        });

        /******* MISE A JOUR DU CART ******/



        updateCart(ul);

    }

};

/******************************************/
/*********** MISE A JOUR CART ************/
/******************************************/

var updateCart = function(cart){

    $('.item-list').replaceWith(cart);
    $(".remove-from-cart").click(onClickCart);

    /****** MISE A JOUR DU TOTAL *******/

    $.getJSON('ajax-total.php', '', onAjaxTotalSuccess);
};


/******************************************/
/********* MISE A JOUR DU TOTAL ***********/
/******************************************/

var onAjaxTotalSuccess = function(response){
    if(response.total === null){
        $('#total').text('Votre panier est vide');
        $('#cmd-btn').addClass('display-btn');
    }
    else {
        $('#total').text('Total du panier :'+response.total+' €');
        $('#cmd-btn').removeClass('display-btn');

    }

    /******** MISE A JOUR DE LA LISTE PRODUITS ********/

    $.getJSON('ajax-products.php', '', onAjaxProductsSuccess);
}


/*************************************************/
/******* MISE A JOUR DE LA LISTE PRODUITS ********/
/************************************************/

var onAjaxProductsSuccess = function(response){

    /******* CONSITUTION DE LA LISTE PRODUITS ******/

    var ul = $('<ul>').addClass('products-list');
    $(response).each(function(index, value){
        $(ul).append(
            '<li> <article class="block"><h2>'
            + value.name
            + '</h2><p class="price">'
            + 'Prix : '+ value.price +'€'
            + '</p>'
            + '<p class="price">'
            + 'Stock :' + value.quantity
            + '</p>'
            + '<a href="#" data-index ="'
            + value.productid
            + '" data-method="add" class="add-to-cart btn btn-important">Ajouter au panier</a>'
            + '</p></article></li>'
        );
    })

    /******* MISE A JOUR DE LA LISTE PRODUITS ******/

    updateProducts(ul);
}

/*******************************************************/
/********* MISE A JOUR DE LA LISTE PRODUITS ***********/
/******************************************************/

var updateProducts = function(products){

    $('.products-list').replaceWith(products);
    $(".add-to-cart").click(onClickCart);
    $("#cmd-btn").click(onClickCmd);
};

/*******************************************/
/***** GESTION DU CLICK SUR UN BOUTON ******/
/*******************************************/

var onClickCart = function(){
    var productid = $(this).data('index');
    var method = $(this).data('method');
    ajax_request(method,productid);
};

/************************************************/
/***** MISE EN PLACE DE LA PAGE PRINCIPALE ******/
/***********************************************/


$(function(){
    /************ LANCEMENT DE LA REQUETES AJAX ******************/

    ajax_request('set','');

    /************ DEFINITION DE LA MODAL STOCK EPUISE **************/

    $( "#dialog-1" ).dialog({
        autoOpen: false,
        position: {
            my: "center",
            at: "center"
        },
        width: 500
    });
});
