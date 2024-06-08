<?php

namespace Kickback\LICH\Controllers;

use \Kickback\Services\Database;

use \Kickback\LICH\Controllers\BaseController;

use \Kickback\LICH\Models\Card;

use \Kickback\LICH\Views\vCard;

use \Kickback\Models\Response;

class CardController extends BaseController
{

    public function __construct()
    {

    }

    public static function addCard(Card $card)
    {
        $stmt = "INSERT INTO lich_card (ctime, crand, name, description, cost, type, team, card_image_id)VALUES(?,?,?,?,?,?,?,?);";

        $params = [];
        
    }

    public static function getCard(Card $card)
    {
        $stmt = "SELECT card_ctime, card_crand, name, description, cost, type, team, mediaPath FROM v_lich_card WHERE card_ctime = ? AND card_crand = ? LIMIT 1";

        $params = [$card->ctime, $card->crand];

        $cardResp = new Response(false, "Unkown Error In Getting Card. ".BaseController::printIdDebugInfo(["Card"=>$card]));

        try
        {
            $result = Database::executeSqlQuery($stmt, $params);

            if($result->num_rows > 0)
            {
                $foundCard = CardController::resultToVCard($result->fetch_assoc());

                $cardResp->success = true;
                $cardResp->messsage = "Found Card";
                $cardResp->data = $foundCard;
            }
            else
            {
                $cardResp->message = "Card Not Found";
            }
        }
        catch(Exception $e)
        {
            $cardResp->message = "Error In Executing Sql Qiery ".BaseController::printIdDebugInfo(["Card"=>$card]);
        }

        return $cardResp;
    }

    public static function resultToVCard(array $row)
    {
        $card = vCard($row["ctime"], $row["crand"], $row["name"], $row["description"], $row["cost"], $row["type"], $row["team"], $row["mediaPath"]);
    
        return $card;
    }
}

?>