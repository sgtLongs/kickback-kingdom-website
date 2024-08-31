<?php

declare(strict_types=1);

namespace Kickback\Controllers;

use \Kickback\Services\Database;
use \Kickback\Views\vRecordId;
use \Kickback\Models\Response;

class BaseController
{
    public static function logData($logFile, $data) {
        $logData = date('Y-m-d H:i:s') . " - Data: " . json_encode($data) . PHP_EOL;
        file_put_contents($logFile, $logData, FILE_APPEND);
    }

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

    public static function printIdDebugInfo(array $debugDict, Exception $e = null)
    {

        $string = "";

        foreach($debugDict as $key => $value)
        {
            $ctime = $value->ctime;
            $crand = $value->crand;
        
            $string = $string." | ".$key."_ctime : ".$ctime." ".$key."_crand : ".$crand;
        }

        if(isset($e))
        {
            $string = $string." | With Exception : ".$e;
        }

        return $string;
    }
}

?>