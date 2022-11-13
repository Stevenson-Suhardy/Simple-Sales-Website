<?php
require("./includes/header.php");

if (!unsignedRedirect()) {
    writeToActivityLog("Sign-out success at ".date("Y-m-d H:i:s").". User ".$_SESSION['email']." signed out.\n");
    session_destroy();
    session_unset();
    session_start();
    setMessage("You successfully logged out!");
    redirect("./sign-in.php");
}

?>