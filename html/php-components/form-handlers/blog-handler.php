<?php


if (isset($_POST["submit-blog-post-publish"]))
{
    $tokenResponse = Kickback\Common\Utility\FormToken::useFormToken();
    
    if ($tokenResponse->success) {
        $blog_post_id = $_POST["blog-post-id"];

        $response = PublishBlogPost($blog_post_id);

        // Handle the response
        if ($response->success) {
            $showPopUpSuccess = true;
            $PopUpTitle = "Updated Blog Post";
            $PopUpMessage= "Your blog post has been published successfully.";
        } else {
            $showPopUpError = true;
            $PopUpTitle = "Error";
            $PopUpMessage = $response->message." -> ".json_encode($response->data);
        }
    }
    else {
        $hasError = true;
        $errorMessage = $tokenResponse->message;
    }
}

if (isset($_POST["submitBlogOptions"])) {
    
    
    /*$showPopUpSuccess = true;
    $PopUpTitle = "Recieved Data";
    $PopUpMessage = json_encode($_POST);*/

    $title = $_POST["blogPostOptionsTitle"];
    $locator = $_POST["blogPostOptionsLocator"];
    $desc = $_POST["blogPostOptionsDesc"];
    $imageId = $_POST["blogPostOptionsIcon"];
    $postIdToUpdate = $_POST["blogPostId"]; // You'll need a way to determine which post to update.

    $response = UpdateBlogPost($postIdToUpdate, $title, $locator, $desc, $imageId);

    // Handle the response
    if ($response->success) {
        $showPopUpSuccess = true;
        $PopUpTitle = "Updated Blog Post";
        $PopUpMessage= "Your changes have been saved successfully.";
        $newURL = Version::urlBetaPrefix().$response->data;
        header('Location: '.$newURL);
    } else {
        $showPopUpError = true;
        $PopUpTitle = "Error";
        $PopUpMessage = $response->message." -> ".json_encode($response->data);
    }
}

?>
