<?php

namespace Kickback\LICH\Controllers;

use \Kickback\Services\Database;

class BaseController
{

    public function __construct()
    {

    }

    public static function logToFile($object)
    {
        // Define the path to the log file
        $logFile = 'D:/PHPLogs/lichLogs.log';

        // Create the log message with a timestamp
        $logMessage = "[" . date('Y-m-d H:i:s') . "] " . json_encode($object) . PHP_EOL;

        // Append the log message to the log file
        file_put_contents($logFile, $logMessage, FILE_APPEND);
    }


    protected static function printIdDebugInfo(array $debugDict)
    {

        $string = "";

        foreach($debugDict as $key => $value)
        {
            $ctime = $value->ctime;
            $crand = $value->crand;
        
            $string = $string." | ".$key."_ctime : ".$ctime." ".$key."_crand : ".$crand;
        }

        return $string;
    }

    protected static function runTest(callable $delegateTestMethod, array $params = [], string $parentClassPath = "Kickback\Controllers\StoreController")
    {
        echo "<h4>TESTING ".$delegateTestMethod[1]." FROM ".$parentClassPath."</h4><br>";

        $params[] = Database::getConnection();
        $response = call_user_func_array($delegateTestMethod, $params);

        BaseController::printResponse($response);

        echo "<h5>Test Complete.</h5><br>";

    }

    protected static function printResponse(Response $response)
    {
        echo "Success : ".var_export($response->success, true)."<br>";
        echo "Message : ".$response->message."<br>";
        echo "Data : ";
        var_dump($response->data);
        echo "<br><br>";
    }

    protected static function loadParams()
    {
        
    }
}

?>