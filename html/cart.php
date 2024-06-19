<?php
require_once(($_SERVER["DOCUMENT_ROOT"] ?: __DIR__) . "/Kickback/init.php");

$session = require(\Kickback\SCRIPT_ROOT . "/api/v1/engine/session/verifySession.php");
require("php-components/base-page-pull-active-account-info.php");

use \Kickback\Controllers\StoreController;
use \Kickback\Controllers\ProductController;
use \Kickback\Controllers\CartController;
use \Kickback\Controllers\BaseController;

use \Kickback\Utilities\FormToken;

use \Kickback\Views\vRecordId;
use \Kickback\Views\vPrice;



if(isset($_GET['locator']))
{

    $accountId = new vRecordId('2022-10-06 16:46:07', 1);

    $accountResp = \Kickback\Controllers\AccountController::getAccountById($accountId);
    $account = $accountResp->data;
    $user = $account->username;

    $storeResp = StoreController::getStoreByLocator($_GET['locator']);
    $store = $storeResp->data;
    $storeId = new vRecordId($store->ctime, $store->crand);

    $cartResp = CartController::getOrCreateAccountCart($accountId, $storeId);


    $thisCheckoutCart = $cartResp->data;
    $checkoutCartId = new vRecordId($thisCheckoutCart->ctime, $thisCheckoutCart->crand);

    $productLinksResp = CartController::getProductsInCart($checkoutCartId);
    $productLinks = $productLinksResp->data;

    $totalPrice = new vPrice(0);

    if(count($productLinks) > 0)
    {
        foreach($productLinks as $link)
        {
            $totalPrice->lovelaces = $totalPrice->lovelaces + $link->price->lovelaces;
        }
    }
    else
    {
        $productLinks = [];
    }
}
else
{

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



    <!--MAIN CONTENT-->
    <main class="container pt-3 bg-body" style="margin-bottom: 56px;">
        <div class="row">
            <div class="col-12 col-xl-9">

                <?php 
                
                
                $activePageName = $user."'s Cart";
                require("php-components/base-page-breadcrumbs.php"); 
                
                ?>

                <div class='row'>
                    <div class="col-8 ">
                        <?php

                            if(isset($_GET['checkedout']))
                            {
                                if($_GET['checkedout'] == -1)
                                {
                                    ?>
                                    <script>
                                        document.addEventListener('DOMContentLoaded', function() {
                                            var myModal = new bootstrap.Modal(document.getElementById('modalProductRemovedFromCart'));
                                            myModal.show();
                                        });
                                    </script>
                                    <?php
                                }
                                elseif($_GET['checkedout'] = 0)
                                {
                                    ?>
                                    <script>
                                        document.addEventListener('DOMContentLoaded', function() {
                                            var myModal = new bootstrap.Modal(document.getElementById('modalCheckoutNoItemsInCart'));
                                            myModal.show();
                                        });
                                    </script>
                                    <?php
                                }
                                else
                                {
                                    ?>
                                    <script>
                                        document.addEventListener('DOMContentLoaded', function() {
                                            var myModal = new bootstrap.Modal(document.getElementById('modalCheckoutComplete'));
                                            myModal.show();
                                        });
                                    </script>
                                    <?php
                                }
                                
                            }

                            foreach($productLinks as $link)
                            {
                            ?>
                                
                                <div class="border rounded-3">
                                    <div class="row p-3">
                                        <div class="col-4">
                                            <image src="<?=$link->media->getFullPath(); ?>" class="img-fluid" alt="Product Image">
                                        </div>

                                        <div class="col-8 d-flex flex-column justify-content-between ">
                                            <div class="row mb-md-auto mb-3">
                                                <div class="col-6">
                                                    <?=$link->productName?>
                                                </div>
                                                
                                                <div class="col-6">
                                                    <div class="d-flex justify-content-end">
                                                        <form id="removeProductFromCart" method="POST" action="cart.php?locator=<?=$store->locator?>&checkedout=-1">
                                                            <?php FormToken::registerForm()?>
                                                            <input type="hidden" name="removeProductFromCart" value="True">
                                                            <input type="hidden" name="linkCtime" value="<?= $link->ctime?>">
                                                            <input type="hidden" name="linkCrand" value="<?= $link->crand?>">
                                                            <button type="submit" class="btn btn-primary btn-sm btn-sm-tn"><i class="fa-solid fa-trash"></i></button>
                                                        </form>
                                                    </div>
                                                </div>
                                                
                                            </div>
                                            
                                            <div class="row mb-md-auto mb-3">
                                                <div class="col">
                                                    <?=$link->description?>
                                                </div>
                                            </div>

                                            <div class="row d-flex flex-row justify-content-end">
                                                <?=$link->price->returnPriceWithSymbol('ADA')?>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            <?php
                            }
                        ?>  
                    </div>

                    <div class="col-4 border rounded-4 p-3">
                        <div class="row mb-2">
                            <image src="<?=$account->avatar->getFullPath();?>" class="img-fluid" alt="Avatar Image">
                        </div>

                        <div>
                            <p class="text-center lh-sm fw-light fs-3">Transaction Summary</p>
                        </div>

                        <div>
                            <hr class="mt-1 mb-1"></hr>
                            <p class="text-left lh-sm fw-lighter">Grand Total</p>

                            <p class="text-left lh-sm fw-light fs-5" style="word-wrap: break-word; overflow-wrap: break-word;"><?=$totalPrice->returnPriceWithSymbol("ADA")?></p>
                            <hr class="mt-1 mb-1"></hr>
                        </div>

                        <div class="d-flex flex-md-nowrap flex-wrap justify-content-around mt-3">
                            <div class="col-12 col-md-5">
                                <div class="d-md-flex justify-content-md-center">
                                    <form action="market.php?locator=test_locator" method="POST">
                                        <button type="submit" class="btn btn-primary w-100 2-md-auto mb-4 mb-md-0">Market</button>
                                    </form>
                                </div>
                            </div>

                            <div class="col-12 col-md-5">
                                <div class="d-md-flex justify-content-md-center">
                                    <form id="checkoutCartFromMarket" method="POST" action="cart.php?locator=<?=$store->locator?>&checkedout=<?=count($productLinks)?>">
                                        <?php FormToken::registerForm()?>
                                        <input type="hidden" name="checkoutCartFromMarket" value="True">
                                        <input type="hidden" name="cartCtime" value="<?= $checkoutCartId->ctime?>">
                                        <input type="hidden" name="cartCrand" value="<?= $checkoutCartId->crand?>">
                                        <button type="submit" class="btn btn-secondary w-100 2-md-auto mb-2 mb-md-0">Checkout</button>
                                    </form>  
                                </div>
                            </div>
                        </div>

                    </div>
                </div>

            </div>

            <?php require("php-components/base-page-discord.php"); ?>
        </div>
    </main>


    <?php require("php-components/base-page-javascript.php"); ?>

</body>

</html>