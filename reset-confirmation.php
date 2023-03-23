<?php
/*
Stevenson Suhardy
September 16, 2022
Webd3201
*/

// Set the title of the page
$title = "Reset Password Link Sent";
// Require the header
require("./includes/header.php");
?>

<!-- This page is to tell the user that the email has been sent, but does not tell if the email exists or not -->
<div class="container">
    <h1 class="h2">Reset Password Confirmed</h1>
    <hr/>
    <p class="p">The reset password link has been sent to your email address. Please check your inbox.</p>
    <a class="text-primary link-primary" href="./sign-in.php">Go back to Sign-In page</a>
</div>

<?php 
// Require the footer
require("./includes/footer.php");
?>