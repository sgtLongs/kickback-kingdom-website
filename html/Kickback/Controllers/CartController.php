<?php

declare(strict_types=1);

namespace Kickback\Controllers;
use Kickback\Services\Database;

use Kickback\Models\Response;
use Kickback\Models\Cart;
use Kickback\Models\ForeignRecordId;

use Kickback\Views\vRecordId;
use Kickback\Views\vProduct;
use Kickback\Views\vCart;
use Kickback\Views\vStore;

class CartController extends BaseController
{

    function __construct()
    {

    }

    public static function runUnitTests()
    {
        $testStoreResp = StoreController::getStore(new vRecordId("2024-04-23 08:54:36", 887940953));
        $testStoreId = new ForeignRecordId($testStoreResp->data->ctime, $testStoreResp->data->crand);
        $testProductId = new vRecordId('2023-05-17 09:51:25', 95465168);
        $testAccount = StoreController::getTestOwner();

        $testCartId = new vRecordId("2020-08-11 09:51:25", 98137686);
        $testCartAdd = new Cart($testAccount, $testStoreId);

        BaseController::runTest([CartController::class, "doesCartExist"], [$testCartId]);
        BaseController::runTest([CartController::class, "getCart"], [$testCartId]);
        BaseController::runTest([CartController::class, "addCart"], [$testCartAdd]);
        BaseController::runTest([CartController::class, "removeCart"], [$testCartAdd]);
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

}

?>