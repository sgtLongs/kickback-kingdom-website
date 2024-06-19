<?php
declare(strict_types=1);

require_once(($_SERVER["DOCUMENT_ROOT"] ?: __DIR__) . "/Kickback/init.php");

$session = require(\Kickback\SCRIPT_ROOT . "/api/v1/engine/session/verifySession.php");
require("php-components/base-page-pull-active-account-info.php");

use \Kickback\Controllers\StoreController;
use \Kickback\Controllers\ProductController;
use \Kickback\Controllers\CartController;

use \Kickback\Controllers\BaseController;

use \Kickback\Utilities\FormToken;

use \Kickback\Views\vRecordId;

if(isset($_GET["locator"]))
{
    $productResp = ProductController::getProductByLocator($_GET["locator"]);

    $accountId = new vRecordId('2022-10-06 16:46:07', 1);
    

    if(!$productResp->success)
    {
        $showPopUpError = true;
        $PopUpTitle = "Product Not Found";
        $PopUpMessage = "Product has been lost to the sea!";
    }
    else
    {
        $thisProduct = $productResp->data;
        $thisProductId = new vRecordId($thisProduct->ctime, $thisProduct->crand);

        $cartResp = CartController::getOrCreateAccountCart($accountId, $thisProduct->storeId);
        $thisCart = $cartResp->data;
        $thisCartId = new vRecordId($thisCart->ctime, $thisCart->crand);
    }

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

    <main class="container pt-3 bg-body" style="margin-bottom: 56px;"> 
            <div class="row">
                <div class="col-12 col-xl-9">

                    <?php 
                        $activePageName = $thisProduct->name;
                        require("php-components/base-page-breadcrumbs.php"); 

                        if(isset($_GET['addedToCart']))
                        {
                            ?>
                            <script>
                                document.addEventListener('DOMContentLoaded', function() {
                                    var myModal = new bootstrap.Modal(document.getElementById('modalAddedToCart'));
                                    myModal.show();
                                });
                            </script>
                            <?php
                        }
                    ?>

                    <!--Product Information and Picture-->
                    <div class="row">

                        <!--Right side product picture and back to maket button-->
                        <div class="col-md-6">

                            <button class="btn btn-primary" onclick="window.location.href='market.php?locator=<?=$thisProduct->storeLocator?>'">Market</button>


                            <img src=<?= $thisProduct->media->getFullPath();?> class="img-fluid" alt="Product Image" style="width: 500px; height: 500px;">
                        </div>

                        <!-- Left Side Product Information Display With Buy and Add to cart buttons-->
                        <div class="col-md-6 d-flex flex-column">

                            <div class="flex-grow-1">
                                <h1 class="display-4"><?=$thisProduct->name?></h1>
                                <p class="lead"><?=$thisProduct->description?></p>
                                <h3 class="text-danger"><?= $thisProduct->price->returnPriceWithSymbol("ADA")?></h3>
                            </div>

                            <div class="d-flex justify-content-between mt-4">

                                <form id="addProductToCartForm" method="POST" action="product.php?locator=<?=$thisProduct->locator?>&addedToCart=true">
                                    <?php FormToken::registerForm()?>
                                    <input type="hidden" name="addProductToCartForm" value="True">
                                    <input type="hidden" name="cartCtime" value="<?= $thisCartId->ctime?>">
                                    <input type="hidden" name="cartCrand" value="<?= $thisCartId->crand?>">
                                    <input type="hidden" name="productCtime" value="<?= $thisProductId->ctime?>">
                                    <input type="hidden" name="productCrand" value="<?= $thisProductId->crand?>">
                                    <button type="submit" class="btn btn-primary btn-lg" data-toggle="modal" data-target="modalCart">Add To Cart</button>
                                </form>
                                
                                <form action="cart.php?locator=<?=$thisProduct->storeLocator?>" method="POST">
                                    <button type="submit" class="btn btn-secondary btn-lg">Checkout</button>
                                </form>
                            </div>
                        </div>

                    </div>
                </div>
                <?php require("php-components/base-page-discord.php"); ?>
            </div>

        

        <?php require("php-components/base-page-footer.php"); ?>

        

    </main>


    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.3/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

    <?php require("php-components/base-page-javascript.php"); ?>
</body>
</html>


