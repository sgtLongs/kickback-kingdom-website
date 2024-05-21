<?php

declare(strict_types=1);

namespace Kickback\Controllers;
use \Kickback\Services\Database;
use \Kickback\Views\vRecordId;
use \Kickback\Models\Response;

class BaseController
{
    protected static function runTest(callable $delegateTestMethod, array $params = [], string $parentClassPath = "Kickback\Controllers\StoreController")
    {
        echo "<h4>TESTING ".$delegateTestMethod[1]." FROM ".$parentClassPath."</h4><br>";

        $params[] = Database::getConnection();
        $response = call_user_func_array($delegateTestMethod, $params);

        BaseController::printResponse($response);

        echo "<h5>Test Complete.</h5><br>";

        return $response;

    }

    protected static function printResponse(Response $response)
    {
        echo "Success : ".var_export($response->success, true)."<br>";
        echo "Message : ".$response->message."<br>";
        echo "Data : ";
        var_dump($response->data);
        echo "<br><br>";
    }

    protected static function printIdDebugInfo(vRecordId $id, Exception $e = null) : string
    {
        $infoMessage = "Ctime : ".$id->ctime." | Crand : ".$id->crand;

        if(isset($e))
        {
            $infoMessage = $infoMessage." | Exception : ".$e;
        }
        
        return $infoMessage;
    }
}

?>