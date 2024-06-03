<?

namespace Kickback\LICH;

class BaseController
{

    public function __construct()
    {

    }

    protected static function printIdDebugInfo(array $debugDict)
    {

        $string = "";

        foreach($debugDict as $key => $value)
        {
            $ctime = $value->ctime;
            $crand = $value->crand;
        
            $string = $string." | ".$key."_ctime : ".$value." ".$key."_crand : ".$crand;
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
}

?>