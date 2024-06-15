<?php

use \Kickback\Utilities\FormToken;

use \Kickback\LICH\Controllers\CardController;

use \Kickback\LICH\Models\Card;



if(isset($_POST["commitCardForm"]))
{

    $tokenResponse = FormToken::useFormToken();

    if($tokenResponse->success)
    {
        $cardName = $_POST["cardName"];
        $cardLocator = $_POST["cardLocator"];
        $cardDescription = $_POST["cardDescription"];
        $cardCost = (int)$_POST["cardCost"];
        $cardType = $_POST["cardType"];
        $cardTeam = $_POST["cardTeam"];
        $cardImageId = (int)$_POST["cardImageId"];

        $card = new Card($cardName, $cardLocator, $cardDescription, $cardCost, $cardType, $cardTeam, $cardImageId);

        CardController::commitCard($card);

    }

}

?>