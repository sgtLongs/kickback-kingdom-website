<?php

namespace Kickback\LICH\Controllers;

use \Kickback\Services\Database;

use \Kickback\LICH\Controllers\BaseController;

use \Kickback\LICH\Models\Card;

use \Kickback\LICH\Views\vCard;
use \Kickback\Views\vRecordId;

use \Kickback\Models\Response;

class CardController extends BaseController
{
    public static string $allViewColumns = "ctime, crand, name, locator, description, cost, type, team, mediaPath, image_id";
    public static string $allTableColumns = "ctime, crand, name, locator, description, cost, type, team, image_id";

    public function __construct()
    {
    }

    public static function getCardByLocator(string $locator)
    {

        $stmt = "SELECT ".CardController::$allViewColumns." FROM v_lich_card WHERE locator = ? LIMIT 1;";

        $params = [$locator];

        $cardResp = new Response(false, "Unkown Error In Getting Card By Locator. Locator : ".$locator, null);

        try
        {
            $result = Database::executeSqlQuery($stmt, $params);

            if($result->num_rows > 0)
            {
                $card = CardController::resultToVCard($result->fetch_assoc());

                $cardResp->success = true;
                $cardResp->message = "Card Found With Locator ".$locator;
                $cardResp->data = $card;

                
            }
            else
            {
                $cardResp->message = "Card Not Found With Locator ".$locator;
            }
        }
        catch(Exception $e)
        {
            $cardResp->message = "Error In Execting Sql Query To Get Card By Locator. Locator : ".$locator;
        }

        return $cardResp;
    }

    public static function commitCard(Card $card)
    {
        $cardResp = new Response(false, "Unkown Error In Commiting Card. ".BaseController::printIdDebugInfo(["card"=>$card]));

        $getCardResp = CardController::getCardByLocator($card->locator);

        if($getCardResp->success == true)
        {
            $ctime = $getCardResp->data->ctime;
            $crand = $getCardResp->data->crand;

            $vCard = new vCard($ctime, $crand, $card->name, $card->locator, $card->description, $card->cost, $card->type, $card->team, "", $card->imageId);

            $cardResp = CardController::updateCard($vCard);   
        }
        else
        {
            $cardResp = CardController::addCard($card);   
        }

        if($cardResp->success = true)
        {
            $cardResp->message = "Card Commited : ".$cardResp->message; 
        }
        else
        {
            $cardResp->message = "Card Not Commited : ".$cardResp->message; 
        }

        return $cardResp;
    
    }

    public static function addCard(Card $card)
    {
        $stmt = "INSERT INTO lich_card (ctime, crand, name, locator, description, cost, type, team, image_id)VALUES(?,?,?,?,?,?,?,?,?);";

        $params = [$card->ctime, $card->crand, $card->name, $card->locator, $card->description, $card->cost, $card->type, $card->team, $card->imageId];

        $cardResp = new Response(false, "Unkown Error In Adding Card To Database. ".BaseController::printIdDebugInfo(["card"=>$card]), null);

        try
        {
            Database::executeSqlQuery($stmt, $params);

            $cardExistsResp = CardController::doesCardExist($card);

            if($cardExistsResp->data)
            {
                $cardResp->success = true;
                $cardResp->message = "Card Added To Database";
                $cardResp->data = $card;

                
            }
            else
            {
                $cardResp->message = "Card Not Added To Database.".BaseController::printIdDebugInfo(["card"=>$card]);
            }
            
        }
        catch(Exception $e)
        {
            $cardResp->message = "Error In Executing Sql Query To Add Card To Database. ".BaseController::printIdDebugInfo(["card"=>$card]);
        }
        
        return $cardResp; 
    }

    public static function updateCard(vCard $card)
    {
        $stmt = "UPDATE lich_card SET name = ?, locator = ?, description = ?, cost = ?, type = ?, team = ?, image_id = ? WHERE ctime = ? AND crand = ?;";

        $params = [$card->name, $card->locator, $card->description, $card->cost, $card->type, $card->team, $card->imageId, $card->ctime, $card->crand];

        $cardResp = new Response(false, "Unkown Error In Updating Card. ".BaseController::printIdDebugInfo(["card"=>$card]), null);

        try
        {
            Database::executeSqlQuery($stmt, $params);

            $cardResp->success = true;
            $cardResp->message = "Card Updated";
            $cardResp->data = $card;

            BaseController::logToFile($card);
        }
        catch(Exception $e)
        {
            $cardResp->message = "Error In Executing Sql Query To Update Card. ".BaseController::printIdDebugInfo(["card"=>$card]);
        }

        return $cardResp;
    }

    public static function removeCard(vRecordId $cardId)
    {
        $stmt = "DELETE FROM card WHERE ctime = ? AND crand = ? LIMIT 1;";

        $parms = [$card->ctime, $card->crand];

        $cardResp = new Response(false, "Unkown Error In Removing Card From Table. ".BaseController::printIdDebugInto(["card"=>$cardId]));

        try
        {
            Database::executeSqlQuery($stmt, $params);

            $cardExistsResp = CardController::doesCardExist($cardId);

            if($cardExistsResp->data == false)
            {
                $cardResp->success = true;
                $cardResp->message = "Card Removed From Database";
                $careResp->data = $cardId;
            }
            else
            {
                $cardResp->message = "Card Still Found In Database; Not Removed";
            }
        }
        catch(Exception $e)
        {
            $cardResp->message = "Error In Executing Sql Query To Removed Card From Database ".BaseController::printIdDebugInfo(["card"=>$cardId]);
        }

        return $cardResp;
    }

    public static function doesCardExist(vRecordId $cardId)
    {
        $stmt = "SELECT ctime, crand FROM lich_card WHERE ctime = ? AND crand = ? LIMIT 1";

        $params = [$cardId->ctime, $cardId->crand];

        $cardResp = new Response(false, "Unkown Error In Checking Card Existence. ".BaseController::printIdDebugInfo(["card"=>$cardId]),null);

        try
        {
            $result = Database::executeSqlQuery($stmt, $params);

            if($result->num_rows > 0)
            {
                $cardResp->success = true;
                $cardResp->message = "Card Exists";
                $cardResp->data = true;
            }
            else
            {
                $cardResp->success = true;
                $cardResp->message = "Card Does Not Exist";
                $cardResp->data = false;
            }
        }
        catch(Exception $e)
        {
            $cardResp->message = "Error In Executing Sql Query To Check Card Existence. ".BaseController::printIdDebugInfo(["card"=>$cardId]);
        }
        
        return $cardResp;
    }

    public static function getCard(Card $card)
    {
        $stmt = "SELECT card_ctime, card_crand, name, locator, description, cost, type, team, mediaPath FROM v_lich_card WHERE card_ctime = ? AND card_crand = ? LIMIT 1";

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
        $card = new vCard($row["ctime"], $row["crand"], $row["name"], $row["locator"], $row["description"], $row["cost"], $row["type"], $row["team"], $row["mediaPath"], $row["image_id"]);
    
        return $card;
    }
}

?>