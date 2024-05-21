<?php

declare(strict_types=1);

namespace Kickback\Controllers;
use Kickback\Services\Database;

use Kickback\Models\Response;
use Kickback\Models\Product;
use Kickback\Models\ForeignRecordId;

use Kickback\Views\vRecordId;
use Kickback\Views\vProduct;

class ProductController extends BaseController
{

    function __construct()
    {

    }

    public static function runUnitTests()
    {
        $testStoreResp = StoreController::getStore(new vRecordId("2024-04-23 08:54:36", 887940953));
        $testStoreId = new ForeignRecordId($testStoreResp->data->ctime, $testStoreResp->data->crand);

        $testProductId = new vRecordId('2023-05-17 09:51:25', 95465168);
        $testAddProduct = new Product("test_add_product", $testStoreId);
        $testRemoveProductId = new vRecordId($testAddProduct->ctime, $testAddProduct->crand);
        $testRemoveProduct = new vProduct($testRemoveProductId, "test_add_product", $testStoreId);

        BaseController::runTest([ProductController::class, "doesProductExist"], [$testProductId]);
        BaseController::runTest([ProductController::class, "getProduct"], [$testProductId]);
        BaseController::runTest([ProductController::class, "addProductToStore"], [$testAddProduct]);
        BaseController::runTest([ProductController::class, "removeProductFromStore"], [$testRemoveProduct]);
    }

    public static function doesProductExist(vRecordId $product)
    {
        $stmt = "SELECT product_ctime FROM product WHERE product_ctime = ? and product_crand = ? LIMIT 1";

        $params = [$product->ctime, $product->crand];

        $productResp = new Response(false, "Unkown Error In Checking If Product Exists. ".BaseController::printIdDebugInfo($product), null);

        try 
        {
            $result = Database::ExecuteSqlQuery($stmt, $params);
            
            if($result->num_rows > 0)
            {
                $productResp->success = true;
                $productResp->message = "Product Exists";
                $productResp->data = true;
            
            }
            else
            {
                $productResp->success = true;
                $productResp->message = "Product Does Not Exist";
                $productResp->data = false;
            }
        } 
        catch (Exception $e) 
        {
            $productResp->message = "Error In Executing Sql Query. ".BaseController::printIdDebugInfo($product, $e);
        }

        return $productResp;
    }

    public static function getProduct(vRecordId $product)
    {
        $stmt = "SELECT product_ctime, product_crand, product_name, ref_store_ctime, ref_store_crand FROM product WHERE product_ctime = ? AND product_crand = ? LIMIT 1";

        $params = [$product->ctime, $product->crand];

        $productResp = new Response(false, "Unkown Error In Getting Product. ".BaseController::printIdDebugInfo($product), null);

        try 
        {
           $result = Database::executeSqlQuery($stmt, $params);
           $row = $result->fetch_assoc();
           $productId = new vRecordId($row["product_ctime"], $row["product_crand"]);
           $refStoreId = new ForeignRecordId($row["ref_store_ctime"], $row["ref_store_crand"]);
           $product = new vProduct($productId, $row["product_name"], $refStoreId);
           
           if($result->num_rows > 0)
           {
                $productResp->success = true;
                $productResp->message = "Product Found";
                $productResp->data = $product;
           }
           else
           {
                $productResp->success = false;
                $productResp->message = "Product Not Found";
           }

        } 
        catch (Exception $e ) 
        {
            $productResp->message = "Error In Executing Sql Query In Getting Product. ".BaseController::printIdDebugInfo($product, $e);
        }

        return $productResp;
    }

    public static function addProductToStore(Product $product)
    {
        $stmt = "INSERT INTO product (product_ctime, product_crand, product_name, ref_store_ctime, ref_store_crand) VALUES (?,?,?,?,?)";

        $params = [$product->ctime, $product->crand, $product->name, $product->storeId->ctime, $product->storeId->crand];

        $productResp = new Response(false, "Unkown Error In Adding Product To Store".BaseController::printIdDebugInfo($product)." Store Id : ".BaseController::printIdDebugInfo($product->storeId), null);

        try
        {

            $result = DataBase::ExecuteSqlQuery($stmt, $params);

            $productExistsResp = ProductController::doesProductExist($product);

            if($productExistsResp->data)
            {
                $productResp->success = true;
                $productResp->message = "Product Successfully Added";
                $productResp->data = $product;
            }
            else
            {
                $productResp->message = "Product Not Added".BaseController::printIdDebugInfo($product)." Store Id : ".BaseController::printIdDebugInfo($product->storeId);
            }

        }
        catch(Exception $e)
        {
            $productResp->message = "Error In Executing Sql Query In Adding Product. ".BaseController::printIdDebugInfo($product)." Store Id : ".BaseController::printIdDebugInfo($product->storeId);
        }

        return $productResp;
    }

    public static function removeProductFromStore(vProduct $product)
    {
        $stmt = "DELETE FROM product WHERE product_ctime = ? AND product_crand = ?";

        $params = [$product->ctime, $product->crand];

        $productResp = new Response(false, "Unkown Error In Removing Product From Store. ".BaseController::printIdDebugInfo($product)." Store Id : ".BaseController::printIdDebugInfo($product->storeId), null);

        try
        {
            Database::executeSqlQuery($stmt, $params);

            $productExistsResp = ProductController::doesProductExist($product);
            
            if($productExistsResp->data == false)
            {
                $productResp->success = true;
                $productResp->message = "Product Removed";
            }
            else
            {
                $productResp->message = "Product Not Removed";
            }
            
        }
        catch(Exception $e)
        {
            $productResp->message = "Error In Executing Sql Query To Remove Store. ".BaseController::printIdDebugInfo($product)." Store Id : ".BaseController::printIdDebugInfo($product->storeId);
        }

        return $productResp;
    }

}

?>