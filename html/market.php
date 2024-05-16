<?php
require_once(($_SERVER["DOCUMENT_ROOT"] ?: __DIR__) . "/Kickback/init.php");

$session = require(\Kickback\SCRIPT_ROOT . "/api/v1/engine/session/verifySession.php");
require("php-components/base-page-pull-active-account-info.php");
$storeResp = getStoreById(1);
$thisStore = ["Id"=>"?"];
$products = [];

if (!$storeResp->Success)
{
    $showPopUpError = true;
    $PopUpTitle = "Market Not Found";
    $PopUpMessage = "Market has been lost to the sea!";
}
else
{
    $thisStore = $storeResp->Data;
    $productsResp = getProductsByStoreId($storeResp->Data["Id"]);

    if(!$productsResp->Success)
    {
        $showPopUpError = true;
        $PopUpTitle = "Prodcuts Not Found!";
        $PopUpMessage = "Products have been lost to the sea!";
    }
    else
    {
        $products = $productsResp->Data;
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
                
                ?>

                <!-- Products Section -->
                <div class="row row-cols-1 row-cols-md-3 g-4">
                    <?php
                    // Example array of products
                    

                    
                    foreach ($products as $product) {
                        ?>
                        <div class="col">
                        <div class="card h-100">
                        <img src='<?= $product['image']; ?>' class="card-img-top" alt="<?= $product['name']; ?>"> 
                        <div class="card-body">
                        <h5 class="card-title">'<?= $product['name']; ?>'</h5>
                        <p class="card-text">Price: '<?= $product['price']; ?>'</p>
                        </div>
                        <div class="card-footer"><a href="#" class="btn btn-primary">Add to Cart</a></div>
                        </div>
                        </div>
                        <?php

                        }
                        ?>
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
