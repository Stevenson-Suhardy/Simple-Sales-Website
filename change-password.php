<?php
$title = "Change Password";

require("./includes/header.php");

$error = "";

$password_update_form = [
    [
        "type" => "password",
        "name" => "password",
        "value" => "",
        "label" => "New Password"
    ],
    [
        "type" => "password",
        "name" => "confirm",
        "value" => "",
        "label" => "Confirm Password"
    ]
];

if ($_SERVER["REQUEST_METHOD"] == "GET") {
    $password = "";
    $confirm_password = "";
}
else if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $password_update_form[0]["value"] = trim($_POST["password"]);
    $password_update_form[1]["value"] = trim($_POST["confirm"]);

    if (!isset($password_update_form[0]["value"]) || $password_update_form[0]["value"] == "") {
        $error .= "Please enter the new password.";
    }
    else if (strlen($password_update_form[0]["value"]) < MINIMUM_PASS_LENGTH) {
        $error .= "Password must be more than " . MINIMUM_PASS_LENGTH;
        $password_update_form[0]["value"] = "";
    } 
    if (!isset($password_update_form[1]["value"]) || $password_update_form[1]["value"] == "") {
        $error .= "Please enter your password confirmation.";
    }
    if ($password_update_form[0]["value"] != $password_update_form[1]["value"]) {
        $error .= "Your new password and confirmation password does not match.";
    }
    if ($error == "") {
        $update_password = UpdatePassword($password_update_form[0]["value"], $_SESSION["email"]);
        $message = "Your password has been updated!";
        $password_update_form[0]["value"] = "";
        $password_update_form[1]["value"] = "";
    }
}

?>

<div class="container">
    <h1 class="h2">Change Password</h1>
    <hr />
    <?php if(isset($message) && $message != "") {echo "<h5>".$message."</h5>";} ?>
    <?php if(isset($error) && $error != "") {echo "<h5>".$error."</h5>";} ?>
    <form class="form" method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>">
    <?php echo display_form($password_update_form); ?>
    <button class="btn btn-lg btn-primary btn-block" type="submit">Submit</button>
    </form>
    <br/>

<?php
require("./includes/footer.php");
?>