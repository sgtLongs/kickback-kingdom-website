<?php
declare(strict_types=1);


require_once(($_SERVER["DOCUMENT_ROOT"] ?: __DIR__) . "/Kickback/init.php");

$session = require(\Kickback\SCRIPT_ROOT . "/api/v1/engine/session/verifySession.php");
require("php-components/base-page-pull-active-account-info.php");

use \Kickback\LICH\Controllers\CardController;

use \Kickback\LICH\Views\vCard;

use \Kickback\Views\vMedia;

use \Kickback\Utilities\FormToken;


if (isset($_GET['locator']))
{
    $locator = $_GET['locator'];
    $cardResp = CardController::getCardByLocator($locator);
    $thisCard = $cardResp->data;
}
else
{
    $thisCard = new vCard("", -1, "", "",  0, "ACT", "Other", "/assets/media/items/221.png");
}

?>



<!DOCTYPE html>
<html lang="en">

<?php require("php-components/base-page-head.php"); ?>

<body class="bg-body-secondary container p-0">

    <?php 
    
    require("php-components/base-page-components.php"); 
    
    require("php-components/ad-carousel.php"); 
    
    ?>

    <main class="container mt-5">
            <div class="card">
                <div class="card-header">
                    Card Editor
                </div>
                <div class="row m-2">

                    <div class="col-md-6">
                        <div class="form-section">
                            <form method="POST" action="">
                                <?php FormToken::registerForm();?>

                                <div class="form-row">
                                    <div class="form-group">
                                        <label for="cardName">Name</label>
                                        <input type="text" class="form-control" id="cardName" placeholder="Enter card name" value=<?= $thisCard->name?>>
                                    </div>
                                    <div class="form-group">
                                        <label for="cardDescription">Description</label>
                                        <textarea class="form-control" id="cardDescription" rows="3" placeholder="Enter card description" value=<?=$thisCard->description?>></textarea>
                                    </div>
                                    <div class="form-group">
                                        <label for="cardCost">Cost</label>
                                        <input type="number" class="form-control" id="cardCost" placeholder="Enter card cost" value=<?=$thisCard->cost?>>
                                    </div>
                                    <div class="form-group">
                                        <label for="cardType">Type</label>
                                        <select class="form-control" id="cardType">
                                            <option value="ACT">ACT</option>
                                            <option value="DEF">DEF</option>
                                        </select>
                                    </div>
                                    <div class="form-group">
                                        <label for="cardTeam">Team</label>
                                        <select class="form-control" id="cardTeam">
                                            <option value="Hunter">Hunter</option>
                                            <option value="Lich">Lich</option>
                                            <option value="Other">Other</option>
                                        </select>
                                    </div>
                                    <div class="form-group">
                                        <label for="cardImageId">Card Image ID</label>
                                        <input type="number" class="form-control" id="cardImageId" placeholder="Enter card image ID">
                                    </div>
                                </div>
                                
                                <div class="form-row mt-4">
                                    <button type="submit" class="btn btn-secondary">Save</button>
                                </div>
                            </form>
                        </div>
                        
                    </div>
                    
                    <div class="col-md-6">
                            <img src=<?=$thisCard->mediaPath?> alt="Card Image" class="card-image" id="cardImage" style="width: 500px; height: 700px; object-fit: cover;">
                    </div>
                </div>
            </div>
            <?php require("php-components/base-page-footer.php"); ?>
    </main>

    <?php require("php-components/base-page-javascript.php"); ?>
</body>

</html>