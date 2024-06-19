<?php


use \Kickback\Utilities\FormToken;

use \Kickback\Controllers\CartController;

use \Kickback\Views\vRecordId;


if (isset($_POST['checkoutCartFromMarket'])) 
{
    $cartCtime = $_POST["cartCtime"];
    $cartCrand = $_POST["cartCrand"];

    $cartId = new vRecordId($cartCtime, $cartCrand);

    CartController::checkoutCart($cartId);

}

if (isset($_POST['removeProductFromCart'])) 
{
    $linkCtime = $_POST["linkCtime"];
    $linkCrand = $_POST["linkCrand"];

    $linkId = new vRecordId($linkCtime, $linkCrand);

    CartController::removeCartProductLink($linkId);

}

?>