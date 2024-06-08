<?php


declare(strict_types=1);


require_once(($_SERVER["DOCUMENT_ROOT"] ?: __DIR__) . "/Kickback/init.php");

$session = require(\Kickback\SCRIPT_ROOT . "/api/v1/engine/session/verifySession.php");
require("php-components/base-page-pull-active-account-info.php");

use Kickback\Controllers\ProductController;
use Kickback\Controllers\CartController;

use Kickback\Views\vRecordId;

use Kickback\Models\ForeignRecordId;

$productIsFound = false;
$thisProduct = null;

if(isset($_GET['productLocator']))
{
    $productLocator = $_GET['productLocator'];
    $productResp = ProductController::getProductByLocator($productLocator);

    if($productResp->success == true)
    {
        $productIsFound = true;
        $thisProduct = $productResp->data;
        $productId = new vRecordId($thisProduct->ctime, $thisProduct->crand);
    }
}

$_SESSION["account"]["Id"] = new vRecordId('2022-10-06 16:46:07', 1);

/*

if (!isLoggedIn()) {
    // Redirect to login.php
    header("Location: login.php");
    exit();
}*/


?>

<!DOCTYPE html>
<html lang="en">


<?php require("php-components/base-page-head.php"); ?>


<head>
<script src="https://code.jquery.com/jquery-3.2.1.min.js"></script>

</head>

<body class="bg-body-secondary container p-0" id="page_top">
    
    <?php 
    
    require("php-components/base-page-components.php"); 
    
    ?>

    <main class="container mt-4 pt-5 bg-body">
        <div class="row">
            <div class="col-md-2">
                <form action="market.php">
                    <button type = "submit" class="btn btn-primary btn-sm">Continue Shopping</button>
                </form>
            </div>

        </div>

        <div class="row">
            <div class="col-md-6">
            <img src="<?= $thisProduct->image->getFullPath(); ?>" class="img-fluid" style="width: 500px; height: 500px; object-fit: cover;" alt="Product Image">

            </div>
            <div class="col-md-6">
                <h1 class="display-4"><?php echo $thisProduct->name ?></h1>
                <p class="lead"><?php echo $thisProduct->description ?></p>
                <h3 class="text-danger"><?php echo $thisProduct->price->returnPriceIn()?> ADA</h3>
                
                <div class="d-flex justify-content-between mt-4">
                    <form method="POST" action="">
                        <?php FormToken::registerForm();?>
                        <button type = "submit" name="addToCart" class="btn btn-primary btn-lg">Add to Cart</button>
                    </form>
                    <button class="btn btn-secondary btn-lg">Buy Now</button>
                </div>

                <?php
                    // Show the modal if there's a cart message
                    if (isset($_SESSION['cart_message'])) 
                    {
                        echo "
                            <script type='text/javascript'>

                                console.log('Showing modal');

                                $(document).ready(function() 
                                {
                                    $('#cartModal').modal('show');
                                });

                            </script>";
                    }

                    

                    
                ?>
            </div>
        </div>

        <div id="page_bottom">
        </div>

        <?php require("php-components/base-page-footer.php"); ?>
    </main>


    <!-- Modal -->
    <div class="modal fade" id="cartModal" tabindex="-1" role="dialog" aria-labelledby="cartModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">

                <div class="modal-header">
                    <h5 class="modal-title" id="cartModalLabel">Cart Status</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <div class="modal-body">
                    <!-- This will be populated by PHP -->
                    <?php
                    if (isset($_SESSION['cart_message'])) 
                    {
                        echo $_SESSION['cart_message'];
                        // Clear the message
                        unset($_SESSION['cart_message']);
                    }
                    ?>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>  

            </div>
        </div>
    </div>

    <?php require("php-components/base-page-javascript.php"); ?>

</body>

</html>