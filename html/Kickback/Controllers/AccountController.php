<?php

namespace Kickback\Controllers;
use Kickback\Services\Database;
use Kickback\Models\Response;
use Kickback\Views\vRecordId;

class AccountController extends BaseController
{
    function __construct()
    {

    }

    public static function runUnitTests()
    {
        $testAccountId = new vRecordId( 1, "2022-10-06 16:46:07");

        BaseController::runTest([AccountController::class, 'getAccount'], [$testAccountId]);
    }

    public static function getAccount(vRecordId $accountId)
    {
        $stmt = "SELECT Id, Email, FirstName, LastName, DateCreated, Username, Banned, pass_reset, passage_id FROM account WHERE Id = ? AND DateCreated = ? LIMIT 1";

        $params = [$accountId->crand, $accountId->ctime];

        $accountResp = new Response(false, "Unkown Error In Getting Account", null);

        try 
        {
            $result = Database::executeSqlQuery($stmt, $params);

            if($result->num_rows > 0)
            {
                $accountResp->success = true;
                $accountResp->message = "Account Successfully Found";
                $accountResp->data = $result->fetch_assoc();
            }
            else
            {
                $accountResp->message = "Account Not Found. ".BaseController::printIdDebugInfo($accountId);
            }
        } 
        catch (Exception $e) 
        {
            $accountResp->message = "Error In Executing Sql Query. ".BaseController::printIdDebugInfo($accountId, $e);
        }

        return $accountResp;
    }
}

?>