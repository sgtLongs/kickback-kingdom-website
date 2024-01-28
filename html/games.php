<?php 

$session = require($_SERVER['DOCUMENT_ROOT']."/api/v1/engine/session/verifySession.php");


require("php-components/base-page-pull-active-account-info.php");


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
                
                
                $activePageName = "Games";
                require("php-components/base-page-breadcrumbs.php"); 
                
                ?>

                <?php
                for ($i=0; $i < 1; $i++) 
                { 
                    //$news = $homeFeed[$i];
                ?>
                
                <?php
                }
                ?>

                <?php require("php-components/coming-soon.php"); ?>
            </div>
            
            <?php require("php-components/base-page-discord.php"); ?>
        </div>
    </main>

    
    <?php require("php-components/base-page-javascript.php"); ?>

</body>

</html>