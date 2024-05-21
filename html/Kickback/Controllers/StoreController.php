<?php
declare(strict_types=1);

namespace Kickback\Controllers;
use \Kickback\Services\Database;

use \Kickback\Views\vRecordId;
use \Kickback\Views\vAccount;
use \Kickback\Views\vStore;

use \Kickback\Models\RecordId;
use \Kickback\Models\Response;
use \Kickback\Models\Account;
use \Kickback\Models\Store;
use \Kickback\Models\ForeignRecordId;

class StoreController extends BaseController
{

    function __construct()
    {

    }

    public static function runUnitTests()
    {
        $testStoreId = new vRecordId("2024-04-23 08:54:36", 887940953);

        $testOwner = StoreController::getTestOwner();
        $testAddStore = new Store("test_store_add_store", $testOwner);

        BaseController::runTest([StoreController::class, 'getStore'], [$testStoreId]);
        BaseController::runTest([StoreController::class, 'addStore'], [$testAddStore]);
        BaseController::runTest([StoreController::class, 'doesStoreExist'], [$testStoreId]);
        BaseController::runTest([StoreController::class, 'removeStore'], [$testAddStore]);
    }

    public static function getTestOwner()
    {
        $testAccount = new vAccount( "2022-10-06 16:46:07", 1);
        $accountResp = AccountController::getAccountById($testAccount);
        $testOwner = new ForeignRecordId($accountResp->data->ctime, $accountResp->data->crand);

        return $testOwner;
    }

    public static function getStore(vRecordId $storeId) : Response
    {
        $stmt = "SELECT store_ctime, store_crand, store_name, ref_account_ctime, ref_account_crand FROM store WHERE store_ctime = ? AND store_crand = ? LIMIT 1";
        $params = [$storeId->ctime, $storeId->crand];

        $storeResp = new Response(false, "Unkown Error Occured In Attempting To Get Store", null);

        try
        {
            $result = Database::executeSqlQuery($stmt, $params);
            $row = $result->fetch_assoc();
 
            $storeOwner = new ForeignRecordId($row["ref_account_ctime"], $row["ref_account_crand"]);
            $returnedStoreId = new vRecordId($row["store_ctime"], $row["store_crand"]);
            $returnedStore = new vStore($returnedStoreId, $row["store_name"], $storeOwner);

            if($result->num_rows > 0)
            {
                $storeResp->success = true;
                $storeResp->message = "Successfully Found Store";
                $storeResp->data = $returnedStore;  
            }
            else
            {
                $storeResp->message = "Store Not Found";
            }
        }
        catch(Exception $e)
        {
            $storeResp->message = "Failed To Execute Sql Query ".StoreController::printIdDebugInfo($storeId, $e);
        }

        return $storeResp;
        
    }

    public static function addStore(Store $store) : Response
    {
        $stmt = "
        INSERT INTO store 
        (store_ctime, 
        store_crand, 
        store_name, 
        ref_account_ctime, 
        ref_account_crand) 
        VALUES (?,?,?,?,?)";

        $params = [$store->ctime, $store->crand, $store->name, $store->ownerId->ctime, $store->ownerId->crand];

        $storeResp = new Response(False, "Unknown Error When Trying to Add Store", null);

        try 
        {
            $result = Database::executeSqlQuery($stmt, $params);

            $storeExistsResp = StoreController::doesStoreExist($store);
            
            if($storeExistsResp->data == true)
            {
                $storeResp->success = true;
                $storeResp->message = "Successfully Added Store";
                $storeResp->data = $store;
            }
            else
            {
                $storeResp->message = "Store Not Added";
            }
        } 
        catch (Exception $e) 
        {
           $storeResp->message = "Failed To Execute Sql Query To Add Store"; 
        }

        return $storeResp;
    }

    public static function removeStore(vRecordId $storeId) : Response
    {
        $stmt = "DELETE FROM store WHERE store_ctime = ? AND store_crand = ?";

        $params = [$storeId->ctime, $storeId->crand];

        $storeResp = new Response(false, "Unkown Error In Removing Store", null);

        try 
        {
    
            $result = Database::executeSqlQuery($stmt, $params);
    
            $doesStoreExistResp = StoreController::doesStoreExist(new vRecordId($storeId->ctime, $storeId->crand));

            if($doesStoreExistResp->data == false)
            {
                $storeResp->success = true;
                $storeResp->message = "Successfully Removed Store";
                $storeResp->data = $storeId;
            }
            else
            {
                $storeResp->success = false;
                $storeResp->message = "Store Still Exists; Failed to Remove";
            }  
        } 
        catch (Exception $e) 
        {
           $storeResp->message = "Error In Executing Sql Query To Remove Store".BaseController::printIdDebugInfo($storeId); 
        }

        return $storeResp;        

    }

    public static function doesStoreExist(vRecordId $storeId) : Response
    {
        $stmt = "SELECT store_ctime FROM store WHERE store_ctime = ? AND store_crand = ? LIMIT 1";

        $params = [$storeId->ctime, $storeId->crand];

        $storeResp = new Response(false, "Unkown Error In Checking If Store Exists. ".BaseController::printIdDebugInfo($storeId), null);

        try
        {
            $result = Database::executeSqlQuery($stmt, $params);  
            
            if($result->num_rows > 0)
            {
                $storeResp->success = true;
                $storeResp->message = "Store Exists";
                $storeResp->data = true;  
            }
            else
            {
                $storeResp->success = true;
                $storeResp->message = "Store Does Not Exist";
                $storeResp->data = false; 
            }

        } 
        catch (Exception $e) 
        {
            $storeResp->message = "Error In Executing Sql Statement. ".BaseController::printIdDebugInfo($storeId);
        }

        return $storeResp;
    }

}

?>