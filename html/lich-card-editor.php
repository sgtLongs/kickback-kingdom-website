<?php

require_once(($_SERVER["DOCUMENT_ROOT"] ?: __DIR__) . "/Kickback/init.php");

$session = require(\Kickback\SCRIPT_ROOT . "/api/v1/engine/session/verifySession.php");
require("php-components/base-page-pull-active-account-info.php");

$thisCard = true;

?>



<!DOCTYPE html>
<html lang="en">

<?php require("php-components/base-page-head.php"); ?>

<body class="bg-body-secondary container p-0">

    <?php 
    
    //require("php-components/base-page-components.php"); 
    
    //require("php-components/ad-carousel.php"); 
    
    ?>

    <div class="container mt-5">
        <div class="card">
            <div class="card-header">
                Card Editor
            </div>
            <div class="card-body card-editor">
                <div class="form-section">
                    <form>
                        <div class="form-group">
                            <label for="cardName">Name</label>
                            <input type="text" class="form-control" id="cardName" placeholder="Enter card name">
                        </div>
                        <div class="form-group">
                            <label for="cardDescription">Description</label>
                            <textarea class="form-control" id="cardDescription" rows="3" placeholder="Enter card description"></textarea>
                        </div>
                        <div class="form-group">
                            <label for="cardCost">Cost</label>
                            <input type="number" class="form-control" id="cardCost" placeholder="Enter card cost">
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
                    </form>
                </div>
                <div class="image-section">
                    <!--<img src="" alt="Card Image" class="card-image" id="cardImage">-->
                </div>
            </div>
        </div>
    </div>
</body>