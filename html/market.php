<?php
declare(strict_types=1);

require_once(($_SERVER["DOCUMENT_ROOT"] ?: __DIR__) . "/Kickback/init.php");

$session = require(\Kickback\SCRIPT_ROOT . "/api/v1/engine/session/verifySession.php");
require("php-components/base-page-pull-active-account-info.php");

use \Kickback\Controllers\StoreController;
use \Kickback\Controllers\ProductController;
use \Kickback\Controllers\CartController;

use \Kickback\Views\vRecordId;

use \Kickback\Utilities\FormToken;



if(isset($_GET['locator']))
{
    $storeResp = StoreController::getStoreByLocator($_GET['locator']);
    $thisStore = $storeResp->data;
    $storeId = new vRecordId($thisStore->ctime, $thisStore->crand);
    $products = [];

    $accountId = new vRecordId('2022-10-06 16:46:07', 1);
    $accountResp = \Kickback\Controllers\AccountController::getAccountById($accountId);
    $account = $accountResp->data;

    $cartResp = CartController::getOrCreateAccountCart($accountId, $storeId);
    $cart = $cartResp->data;
    $cardId = new vRecordId($cart->ctime, $cart->crand);

    if (!$storeResp->success)
    {
        $showPopUpError = true;
        $PopUpTitle = "Market Not Found";
        $PopUpMessage = "Market has been lost to the sea!";
    }
    else
    {
        $thisStore = $storeResp->data;
        $productsResp = ProductController::getProductsByStoreLocator($thisStore->locator);
        
        

        if(!$productsResp->success)
        {
            $showPopUpError = true;
            $PopUpTitle = "Products Not Found!";
            $PopUpMessage = "Products have been lost to the sea!";
        }
        else
        {
            $products = $productsResp->data;
        }
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

    

    <!--MAIN CONTENT-->
    <main class="container pt-3 bg-body" style="margin-bottom: 56px;">
        <div class="row">
            <div class="col-12 col-xl-9">
                
                
                <?php 
                
                
                $activePageName = "Market";
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

                <!-- Products Section -->
                <div class="row row-cols-1 row-cols-md-3 g-4">
                    <?php 

                        if(isset($_GET['locator']))
                        {
                            foreach ($products as $product) 
                            {
                                ?>
                                <div class="col">
                                    <div class="card h-100">
                                    <img src='<?= $product->media->getFullPath(); ?>' class="card-img-top" alt="<?= $product->name; ?>"> 
                                    <div class="card-body">
                                    <h5 class="card-title">'<?= $product->name; ?>'</h5>
                                    <p class="card-text">Price: <?= $product->price->returnPriceWithSymbol('ADA'); ?></p>
                                    </div>
                                        <div class="card-footer">
                                            <div class="d-flex justify-content-around">
                                                <a href="product.php?locator=<?=$product->locator?>" class="btn btn-primary">View</a>

                                                <form id="addProductToCartForm" method="POST" action="market.php?locator=<?=$thisStore->locator?>&addedToCart=true">
                                                    <?php FormToken::registerForm()?>
                                                    <input type="hidden" name="addProductToCartForm" value="True">
                                                    <input type="hidden" name="cartCtime" value="<?= $cardId->ctime?>">
                                                    <input type="hidden" name="cartCrand" value="<?= $cardId->crand?>">
                                                    <input type="hidden" name="productCtime" value="<?= $product->ctime?>">
                                                    <input type="hidden" name="productCrand" value="<?= $product->crand?>">
                                                    <button type="submit" class="btn btn-secondary" data-toggle="modal" data-target="modalCart">Add To Cart</button>
                                                </form>
                                            </div>
                                            
                                    
                                        </div>
                                    </div>
                                </div>
                                <?php

                            }
                        }
                    ?>
                </div>

                <div class="d-flex flex-row justify-content-end">
                    <div class="col-6">
                        <form action="cart.php?locator=<?=$thisStore->locator?>" method="POST">
                            <div class="d-flex justify-content-end">
                                <button class="w-50 btn btn-primary btn-lg ">Cart</button>  
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Products Section End -->

            </div>
            
            <?php require("php-components/base-page-discord.php"); ?>
        </div>
        <?php require("php-components/base-page-footer.php"); ?>
    </main>

    
    <?php require("php-components/base-page-javascript.php"); ?>

</body>

</html>
