<?php

use \Kickback\Utilities\FormToken;

use \Kickback\Controllers\CartController;

use \Kickback\Views\vRecordId;


if (isset($_POST['addProductToCartForm'])) 
{


    $tokenResp = FormToken::useFormToken();


    if($tokenResp->success == true)
    {
        

        $cartCtime = $_POST["cartCtime"];
        $cartCrand = $_POST["cartCrand"];

        $productCtime = $_POST["productCtime"];
        $productCrand = $_POST["productCrand"];

        $cartId = new vRecordId($cartCtime, $cartCrand);
        $productId = new vRecordId($productCtime, $productCrand);


        CartController::linkProductToCart($productId, $cartId);
    }
}

?>