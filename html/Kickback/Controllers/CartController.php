<?php

declare(strict_types=1);

namespace Kickback\Controllers;
use Kickback\Services\Database;

use Kickback\Models\Response;
use Kickback\Models\Cart;
use Kickback\Models\ForeignRecordId;
use Kickback\Models\CartProductLink;

use Kickback\Views\vRecordId;
use Kickback\Views\vProduct;
use Kickback\Views\vCart;
use Kickback\Views\vStore;
use Kickback\Views\vCartProductLink;

class CartController extends BaseController
{

    function __construct()
    {

    }

    public static function runUnitTests()
    {
        $testStoreResp = StoreController::getStore(new vRecordId("2024-04-23 08:54:36", 887940953));
        $testStoreId = new ForeignRecordId($testStoreResp->data->ctime, $testStoreResp->data->crand);
        $testProductId = new ForeignRecordId('2023-05-17 09:51:25', 95465168);
        $testAccount = StoreController::getTestOwner();

        $testCartId = new ForeignRecordId("2020-08-11 09:51:25", 98137686);
        $testCartAdd = new Cart($testAccount, $testStoreId);

        BaseController::runTest([CartController::class, "doesCartExist"], [$testCartId]);
        BaseController::runTest([CartController::class, "getCart"], [$testCartId]);
        BaseController::runTest([CartController::class, "addCart"], [$testCartAdd]);
        BaseController::runTest([CartController::class, "removeCart"], [$testCartAdd]);

        $addedLinkResp = BaseController::runTest([CartController::class, "linkProductToCart"], [$testProductId, $testCartId]);
        $addedLinkId = new vRecordId($addedLinkResp->data->ctime, $addedLinkResp->data->crand);
        BaseController::runTest([CartController::class, "doesCartProductLinkExist"], [$addedLinkId]);
        BaseController::runTest([CartController::class, "getCartProductLink"], [$addedLinkId]);
        BaseController::runTest([CartController::class, "removeCartProductLink"], [$addedLinkId]);
    }

    private static function printCartIdDebugInfo(mixed $cart, $e = null)
    {
        if($cart instanceof vRecordId)
        {
            $infoMessage = "cart_ctime : ".$cart->ctime." | cart_crand : ".$cart->crand;
        }

        if($cart instanceof Cart)
        {
            $infoMessage = "cart_ctime : ".$cart->ctime." | cart_crand : ".$cart->crand." | account_ctime : ".$cart->accountId->ctime." | account_crand : ".$cart->accountId->crand." | store_ctime : ".$cart->storeId->ctime." | store_crand : ".$cart->storeId->crand;
        }

        if(isset($e))
        {
            $infoMessage = $infoMessage." | Exception : ".$e;
        }

        return $infoMessage;
        
    }

    private static function printLinkIdDebugInfo(mixed $link, $e = null)
    {
        if($link instanceof vRecordId)
        {
            $infoMessage = "link_ctime : ".$link->ctime." | link_crand : ".$link->crand;
        }

        if($link instanceof CartProductLink)
        {
            $infoMessage = "link_ctime : ".$link->ctime." | link_crand : ".$link->crand." | ref_cart_ctime : ".$link->cartId->ctime." | ref_cart_crand : ".$link->cartId->crand." | store_ctime : ".$link->productId->ctime." | store_crand : ".$link->productId->crand;
        }

        if(isset($e))
        {
            $infoMessage = $infoMessage." | Exception : ".$e;
        }

        return $infoMessage;  
    }

    

    public static function doesCartExist(vRecordId $cart)
    {
        $stmt = "SELECT cart_ctime FROM cart WHERE cart_ctime = ? and cart_crand = ? LIMIT 1";

        $params = [$cart->ctime, $cart->crand];

        $cartResp = new Response(false, "Unkown Error In Checking If Cart Exists. ".CartController::printCartIdDebugInfo($cart), null);

        try 
        {
            $result = Database::ExecuteSqlQuery($stmt, $params);
            
            if($result->num_rows > 0)
            {
                $cartResp->success = true;
                $cartResp->message = "Cart Exists";
                $cartResp->data = true;
            
            }
            else
            {
                $cartResp->success = true;
                $cartResp->message = "Cart Does Not Exist";
                $cartResp->data = false;
            }
        } 
        catch (Exception $e) 
        {
            $cartResp->message = "Error In Executing Sql Query. ".CartController::printCartIdDebugInfo($cart, $e);
        }

        return $cartResp;
    }

    public static function getCart(vRecordId $cart)
    {
        $stmt = "SELECT cart_ctime, cart_crand, ref_store_ctime, ref_store_crand, ref_account_ctime, ref_account_crand FROM cart WHERE cart_ctime = ? AND cart_crand = ? LIMIT 1";

        $params = [$cart->ctime, $cart->crand];

        $cartResp = new Response(false, "UNKOWN ERROR IN RETRIEVING CART".CartController::printCartIdDebugInfo($cart), null);

        try
        {
            $result = Database::executeSqlQuery($stmt, $params);

            

            if($result->num_rows > 0)
            {
                $row = $result->fetch_assoc();

                $foundCartId = new vRecordId($row["cart_ctime"], $row["cart_crand"], );
                $foundStoreId= new ForeignRecordId($row["ref_store_ctime"], $row["ref_store_crand"]);
                $foundAccountId = new ForeignRecordId($row["ref_account_ctime"], $row["ref_account_crand"]);

                $foundCart = new vCart($foundCartId, $foundStoreId, $foundAccountId);

                $cartResp->success = true;
                $cartResp->message = "Cart Successfully Found";
                $cartResp->data = $foundCart;
            }
            else
            {
                $cartResp->message = "Cart Not Found";
            }
        }
        catch(Exception $e)
        {
            $cartResp->message = "Error In Executing SQL Query. ".CartController::printCartIdDebugInfo($cart, $e);
        }

        return $cartResp;
    }

    public static function addCart(Cart $cart)
    {
        $stmt = "INSERT INTO cart (cart_ctime, cart_crand, ref_store_ctime, ref_store_crand, ref_account_ctime, ref_account_crand) VALUES (?,?,?,?,?,?)";
        $params = [$cart->ctime, $cart->crand, $cart->storeId->ctime, $cart->storeId->crand, $cart->accountId->ctime, $cart->accountId->crand];

        $cartResp = new Response(false, "Unkown Error In Adding Cart. ".CartController::printCartIdDebugInfo($cart), null);

        try
        {
            Database::executeSqlQuery($stmt, $params);

            $cartId = new vRecordId($cart->ctime, $cart->crand);
            $cartExistsResp = CartController::doesCartExist($cartId);

            if($cartExistsResp->data)
            {
                $cartResp->success = true;
                $cartResp->message = "Successfully Added Cart";
                $cartResp->data = $cart;
            }
            else
            {
                $cartResp->message = "Cart Not Added. ".CartController::printCartIdDebugInfo($cart);
            }
        }
        catch(Exception $e)
        {
            $cartResp->message = "Error In Executing Sql To Add Cart. ".CartController::printCartIdDebugInfo($cart);
        }

        return $cartResp;
    }

    public static function removeCart(vRecordId $cart)
    {
        $stmt = "DELETE FROM cart WHERE cart_ctime = ? AND cart_crand = ?";
        $params = [$cart->ctime, $cart->crand];

        $cartResp = new Response(false, "Unkown Error In Deleting Cart. ".CartController::printCartIdDebugInfo($cart), null);

        try
        {
            Database::executeSqlQuery($stmt, $params);

            $cartExistsResp = CartController::doesCartExist($cart);

            if($cartExistsResp->data == false)
            {
                $cartResp->success = true;
                $cartResp->message = "Cart Removed";
            }
            else
            {
                $cartResp->message = "Cart Not Removed. ".CartController::printCartIdDebugInfo($cart);
            }
        }
        catch(Exception $e)
        {
            $cartResp->message = "Error In Executing Sql Query To Remove Cart. ".CartController::printCartIdDebugInfo($cart);
        }

        return $cartResp;
    }

    

    public static function doesCartProductLinkExist(vRecordId $link)
    {
        $stmt = "SELECT link_ctime FROM cart_product_link WHERE link_ctime = ? AND link_crand = ? LIMIT 1";

        $params = [$link->ctime, $link->crand];

        $linkResp = new Response(false, "Unkown Error In Checking Existence of Cart Product Link. ".CartController::printLinkIdDebugInfo($link), null);

        try
        {
            $result = Database::executeSqlQuery($stmt, $params);

            if($result->num_rows > 0)
            {
                $linkResp->success = true;
                $linkResp->message = "Link Exists: ".CartController::printLinkIdDebugInfo($link);
                $linkResp->data = true;
            }
            else
            {
                $linkResp->message = "Link Does Not Exist.";
                $linkResp->data = false;
            }

        }
        catch(Exception $e)
        {
            $linkResp->message = "Error In Executing Sql Query To Check Existence Of Cart Product Link. ".CartController::printLinkIdDebugInfo($link, $e);
        }

        return $linkResp;
    }

    public static function getCartProductLink(vRecordId $link)
    {
        $stmt = "SELECT link_ctime, link_crand, ref_cart_ctime, ref_cart_crand, ref_product_ctime, ref_product_crand FROM cart_product_link WHERE link_ctime = ? AND link_crand = ? LIMIT 1";

        $params = [$link->ctime, $link->crand];

        $linkResp = new Response(false, "Unkown Error In Getting Cart Prodcut Link. ".CartController::printLinkIdDebugInfo($link), null);

        try
        {
            $result = Database::executeSqlQuery($stmt, $params);

            if($result->num_rows > 0)
            {
                $row = $result->fetch_assoc();

                $linkId = new vRecordId($row["link_ctime"], $row["link_crand"]);
                $cartId = new ForeignRecordId($row["ref_cart_ctime"], $row["ref_cart_crand"]);
                $productId = new ForeignRecordId($row["ref_product_ctime"], $row["ref_product_crand"]);
                $foundLink = new vCartProductLink($linkId, $cartId, $productId);

                $linkResp->success = true;
                $linkResp->message = "Store Found";
                $linkResp->data = $foundLink;
            }
            else
            {
                $linkResp->message = "Cart Product Not Found. ".CartController::printLinkIdDebugInfo($link);
            }


        }
        catch(Exception $e)
        {
            $linkResp->message = "Error In Executing Sql Query To Get Cart Product Link. ".CartController::printLinkIdDebugInfo($link, $e);
        }

        return $linkResp;
    }

    public static function removeCartProductLink(vRecordId $link)
    {
        $stmt = "DELETE FROM cart_product_link WHERE link_ctime = ? AND link_crand = ?";

        $params = [$link->ctime, $link->crand];

        $linkResp = new Response(false, "Unknown Error In Removing Cart Product Link. ".CartController::printLinkIdDebugInfo($link), null);

        try
        {
            Database::executeSqlQuery($stmt, $params);

            $linkExistsResp = CartController::doesCartProductLinkExist($link);

            if($linkExistsResp->data == false)
            {
                $linkResp->success = true;
                $linkResp->message = "Cart Product Link Removed. ".CartController::printLinkIdDebugInfo($link);
            }
            else
            {
                $linkResp->message = "Cart Product Link Was Not Removed";
            }
        }
        catch (Exception $e)
        {
            $linkMessage = "Error In Executing Sql Query To Remove Cart Link. ".CartController::printLinkIdDebugInfo($link, $e);
        }
        
        return $linkResp;
    }

    public static function linkProductToCart(ForeignRecordId $product, ForeignRecordId $cart)
    {
        $cartProductLink = new CartProductLink($product, $cart);

        $stmt = "INSERT INTO cart_product_link (link_ctime, link_crand, ref_cart_ctime, ref_cart_crand, ref_product_ctime, ref_product_crand) VALUES (?,?,?,?,?,?)";

        $params = [$cartProductLink->ctime, $cartProductLink->crand, $cartProductLink->cartId->ctime, $cartProductLink->cartId->crand, $cartProductLink->productId->ctime, $cartProductLink->productId->crand];

        $linkResp = new Response(false, "Unknown Error In Linking Product To Cart. ".CartController::printLinkIdDebugInfo($cartProductLink), null);

        try
        {
            Database::executeSqlQuery($stmt, $params);

            $linkExistsResp = CartController::doesCartProductLinkExist($cartProductLink);

            if($linkExistsResp->data)
            {
                $linkResp->success = true;
                $linkResp->message = "Product Successfully Linked.";
                $linkResp->data = $cartProductLink;
            }
            else
            {
                $linkResp->message = "Product Not Linked. ".CartController::printLinkIdDebugInfo($cartProductLink);
            }
        }
        catch(Exception $e)
        {
            $linkResp->message = "Error in Executing Sql Query To Link Product To Cart. ".CartController::printLinkIdDebugInfo($cartProductLink, $e);
        }

        return $linkResp;
    }

}

?>