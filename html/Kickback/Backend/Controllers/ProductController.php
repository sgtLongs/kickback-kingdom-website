<?php

declare(strict_types=1);

namespace Kickback\Controllers;
use Kickback\Services\Database;

use \Kickback\Models\Response;
use \Kickback\Models\Product;
use \Kickback\Models\ForeignRecordId;

use \Kickback\Views\vRecordId;
use \Kickback\Views\vProduct;
use \Kickback\Views\vPrice;

class ProductController extends BaseController
{
    static string $allViewColumns = 'ctime, crand, name, locator, price, description, store_locator, ref_store_ctime, ref_store_crand, ref_media_ctime, ref_media_crand, mediaPath';
    static string $allTableColumns = 'ctime, crand, name, locator, price, description, ref_store_ctime, ref_store_crand, ref_media_ctime, ref_media_crand';

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

        $productResp = new Response(false, "Unkown Error In Checking If Product Exists. ".BaseController::printIdDebugInfo(["product"=>$product]), null);

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
            $productResp->message = "Error In Executing Sql Query. ".BaseController::printIdDebugInfo(["product"=>$product], $e);
        }

        return $productResp;
    }

    public static function getProduct(vRecordId $product)
    {
        $stmt = "SELECT product_ctime, product_crand, product_name, ref_store_ctime, ref_store_crand FROM product WHERE product_ctime = ? AND product_crand = ? LIMIT 1";

        $params = [$product->ctime, $product->crand];

        $productResp = new Response(false, "Unkown Error In Getting Product. ".BaseController::printIdDebugInfo(["product"=>$product]), null);

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
            $productResp->message = "Error In Executing Sql Query In Getting Product. ".BaseController::printIdDebugInfo(["product"=>$product], $e);
        }

        return $productResp;
    }

    public static function addProductToStore(Product $product)
    {
        $stmt = "INSERT INTO product (product_ctime, product_crand, product_name, ref_store_ctime, ref_store_crand) VALUES (?,?,?,?,?)";

        $params = [$product->ctime, $product->crand, $product->name, $product->storeId->ctime, $product->storeId->crand];

        $productResp = new Response(false, "Unkown Error In Adding Product To Store".BaseController::printIdDebugInfo(["product"=>$product,"store"=>$product->storeId]), null);

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
                $productResp->message = "Product Not Added".BaseController::printIdDebugInfo(["product"=>$product,"store"=>$product->storeId]);
            }

        }
        catch(Exception $e)
        {
            $productResp->message = "Error In Executing Sql Query In Adding Product. ".BaseController::printIdDebugInfo(["product"=>$product,"store"=>$product->storeId]);
        }

        return $productResp;
    }

    public static function removeProductFromStore(vProduct $product)
    {
        $stmt = "DELETE FROM product WHERE product_ctime = ? AND product_crand = ?";

        $params = [$product->ctime, $product->crand];

        $productResp = new Response(false, "Unkown Error In Removing Product From Store. ".BaseController::printIdDebugInfo(["product"=>$product,"store"=>$product->storeId]), null);

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
            $productResp->message = "Error In Executing Sql Query To Remove Store. ".BaseController::printIdDebugInfo(["product"=>$product,"store"=>$product->storeId]);
        }

        return $productResp;
    }

    public static function getProductByLocator(string $locator)
    {
        $stmt = "SELECT ".ProductController::$allViewColumns." FROM v_product WHERE locator = ? LIMIT 1;";
    
        $params = [$locator];

        $productResp = new Response(false, "Unkown Error In Getting Product By Locator : ".$locator, null);

        try
        {
            $result = Database::executeSqlQuery($stmt, $params);

            if($result->num_rows > 0)
            {

                $product = ProductController::rowToVProduct($result->fetch_assoc());

                $productResp->success = true;
                $productResp->message = "Found Product With Locator : ".$locator;
                $productResp->data = $product;

            }
            else
            {
                $productResp->message = "Unable To Find Product With Locator : ".$locator;
            }
        }
        catch(Exception $e)
        {
            $productResp->message = "Error In Executing Sql Query To Get Product By Locator : ".$locator;
        }

        return $productResp;
    }

    public static function getProductsByStoreLocator(string $locator)
    {
        $stmt = "SELECT ".ProductController::$allViewColumns." FROM v_product WHERE store_locator = ?;";

        $params = [$locator];

        $productResp = new Response(false, "Unkown Error In Getting Store Products By Locator :".$locator, null);

        try
        {
            $result = Database::executeSqlQuery($stmt, $params);

            $productArray = [];

            while($row = $result->fetch_assoc())
            {
                $product = ProductController::rowToVProduct($row);
                
                $productArray[] = $product;
            }

            $productResp->success = true;
            $productResp->message = "Store Products Successfully Retreived From Store With Locator : ".$locator;
            $productResp->data = $productArray;           
        }
        catch(Exception $e)
        {
            $productResp->message = "Error In Executing Sql Query To Retrieve Products From Store With Locator : ".$locator;
        }

        return $productResp;
    }

    public static function rowToVProduct(array $row)
    {
        $price = new vPrice($row["price"]);

        $product = new vProduct(
            $row["ctime"], 
            $row["crand"], 
            $row["name"], 
            $row["locator"],
            $price, 
            $row["description"], 
            $row["store_locator"], 
            $row["ref_store_ctime"], 
            $row["ref_store_crand"], 
            $row["ref_media_ctime"], 
            $row["ref_media_crand"], 
            $row["mediaPath"]);

        return $product;
    }   

}

?>