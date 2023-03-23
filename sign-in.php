<?php
/*
Stevenson Suhardy
September 16, 2022
Webd3201
*/
$title = "Titan Link Sign In Page";
require("./includes/header.php");

$error = "";
if ($_SERVER["REQUEST_METHOD"] == "GET") {
    if (isLoggedIn()) {
        redirect("./dashboard.php");
    }
}
else if($_SERVER["REQUEST_METHOD"] == "POST") {
    $_SESSION['email'] = trim($_POST['emailAddress']);
    $_SESSION['password'] = trim($_POST['password']);
    
    if (!isset($_SESSION['email'])) {
        $error = "Please enter a valid email address.<br/>";
    }
    if (!isset($_SESSION['password'])) {
        $error = "Please enter the password.<br/>";
    }
    if ($error == "") {
        $user = user_select($_SESSION['email']);
        setMessage("You successfully logged in! You last accessed this website on ".$user['lastaccess']);
        if (user_authenticate($_SESSION['email'], $_SESSION['password'])) {
            writeToActivityLog("Sign-in success at ".date("Y-m-d H:i:s").". User ".$_SESSION['email']." signed in.\n");
            redirect("./dashboard.php");
        }
        else {
            $error = "Your email address or password is not found.";
            writeToActivityLog("Failed sign-in at ".date("Y-m-d H:i:s").". User ".$_SESSION['email']." failed to sign in.\n");
            removeMessage();
            unset($_SESSION['email']);
            unset($_SESSION['password']);
        }
    }
}


?>
<div class="container">
    <form class="form-signin" method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>">
        <?php if(isset($error) && $error != "") {echo "<h5>".$error."</h5>";} ?>
        <?php if(isset($message) && $message != "") {echo "<h5>".$message."</h5>";} ?>
        <h1 class="h3 mb-3 font-weight-normal">Please sign in</h1>
        <label for="inputID" class="sr-only">Email Address</label>
        <input type="text" name="emailAddress" id="inputID" class="form-control" placeholder="Email Address" required autofocus>
        <label for="inputPassword" class="sr-only">Password</label>
        <input type="password" name="password" id="inputPassword" class="form-control" placeholder="Password" required>
        <button class="btn btn-lg btn-primary btn-block" type="submit">Sign in</button>
        <a class="text-primary link-primary" href="./reset.php">Forgot Password?</a>
    </form>
</div>

<hr/>
<div class="container-fluid">
    <h5 class="text-center">
        This is a public test ID.
        <br/>
        Sales Person:
        ID: jdoe@durhamcollege.ca
        Password: testpass
        <br/>
        Admin:
        ID: stevenson.suhardy@dcmail.ca
        Password: pass123
    </h5>
</div>
<hr/>

<?php
require "./includes/footer.php";
?>    