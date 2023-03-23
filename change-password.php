<?php
// Set the title of the page
$title = "Change Password";

// Require the header
require("./includes/header.php");

// Declare error variable to store errors
$error = "";
// Creating a new form called password_update_form
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

// Check to see if the request method is get
if ($_SERVER["REQUEST_METHOD"] == "GET") {
    $password = "";
    $confirm_password = "";
}
// Check to see if the request method is post
else if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Update the form valeus using the post variables
    $password_update_form[0]["value"] = trim($_POST["password"]);
    $password_update_form[1]["value"] = trim($_POST["confirm"]);

    // Validation for the password
    if (!isset($password_update_form[0]["value"]) || $password_update_form[0]["value"] == "") {
        $error .= "Please enter the new password.";
    }
    else if (strlen($password_update_form[0]["value"]) < MINIMUM_PASS_LENGTH) {
        $error .= "Password must be more than " . MINIMUM_PASS_LENGTH;
        $password_update_form[0]["value"] = "";
    } 
    // Validation for the confirm password
    if (!isset($password_update_form[1]["value"]) || $password_update_form[1]["value"] == "") {
        $error .= "Please enter your password confirmation.";
    }
    if ($password_update_form[0]["value"] != $password_update_form[1]["value"]) {
        $error .= "Your new password and confirmation password does not match.";
    }
    // If there are no errors
    if ($error == "") {
        // Call the function to update the password for the current logged in user
        $update_password = UpdatePassword($password_update_form[0]["value"], $_SESSION["email"]);
        // Flash the following message
        $message = "Your password has been updated!";
        // Resetting form
        $password_update_form[0]["value"] = "";
        $password_update_form[1]["value"] = "";
    }
}
?>

<div class="container">
    <h1 class="h2">Change Password</h1>
    <hr />
    <?php 
    // Display flash message
    if(isset($message) && $message != "") {echo "<h5>".$message."</h5>";} ?>
    <?php 
    // Display errors
    if(isset($error) && $error != "") {echo "<h5>".$error."</h5>";} ?>
    <form class="form" method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>">
    <?php
    // Display form
    echo display_form($password_update_form); ?>
    <button class="btn btn-lg btn-primary btn-block" type="submit">Submit</button>
    </form>
    <br/>

<?php
require("./includes/footer.php");
?>