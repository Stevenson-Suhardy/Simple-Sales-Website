<?php
/*
Stevenson Suhardy
September 16, 2022
Webd3201
*/
// Require the header
require("./includes/header.php");

// Check to see if the user is signed in
if (!unsignedRedirect()) {
    // Write the following message to the log
    writeToActivityLog("Sign-out success at ".date("Y-m-d H:i:s").". User ".$_SESSION['email']." signed out.\n");
    // Destroy the session
    session_destroy();
    // Unset all variables related to the current session
    session_unset();
    // Start a new session
    session_start();
    // Flash the message
    setMessage("You successfully logged out!");
    // Redirect to the sign in page
    redirect("./sign-in.php");
}

?>