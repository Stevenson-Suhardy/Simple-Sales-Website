<?php
/*
Stevenson Suhardy
September 16, 2022
Webd3201
*/

// Set the title of the page
$title = "Reset Password";

// Require header
require("./includes/header.php");

// Creating a new form called reset_form
$reset_form = [
    [
        "type" => 'email',
        "name" => "email",
        "value" => "",
        "label" => "Email Address"
    ]
];

// Declaring variable to store errors
$error = "";

// Checking to see if the request method is get
if ($_SERVER["REQUEST_METHOD"] == "GET") {
    $email = "";
}
// Checking to see if the request method is post
else if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get the value from post and trim it
    $reset_form[0]['value'] = trim($_POST["email"]);

    // Email validations
    if (!isset($reset_form[0]['value']) || $reset_form[0]['value'] == "") {
        $error .= "Please enter an email address.<br/>";
    }
    else if (!isset($reset_form[0]['value']) || $reset_form[0]['value'] == "") {
        $error .= "Email address entered (" . $reset_form[0]['value'] . ") cannot be more than " . MAXIMUM_EMAIL_LENGTH . " characters.<br/>";
        $reset_form[0]['value'] = "";
    }
    else if (!filter_var($reset_form[0]['value'], FILTER_VALIDATE_EMAIL)) {
        $error .= "Email address entered (" . $reset_form[0]['value'] . ") is not a valid email address.<br/>";
        // Clearing the value from the textbox
        $reset_form[0]['value'] = "";
    }
    // When there are no errors
    if ($error == "") {
        // Set the declared variable to the form value
        $email = $reset_form[0]['value'];

        // Set the to, subject, and message for email
        $to = $email;
        $subject = 'Please reset your password';
        $message = 'Reset your Titan Link password. You can use the following link to reset your password: https://opentech.durhamcollege.org/webd3201/suhardys/reset.php (This link is just going back to the previous link, just to give something for the user to click.)';
        // Mail to the subject
        @mail($to, $subject, $message);
        // Log the file to a text file
        logMail($to, $subject);
        // Remove the value inside the form
        $reset_form[0]['value'] = "";
        // Redirect to another page saying email has been sent
        redirect("./reset-confirmation.php");
    }
}
?>

<div class="container">
    <h1 class="h2">Reset Password</h1>
    <hr/>
    <p class="p">Enter your user account's verified email address and we will send you a password reset link.</p>
    <?php 
    // Display errors
    if(isset($error) && $error != "") {echo "<h5>".$error."</h5>";} ?>
    <form class="form" method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>">
        <?php 
        // Displays the form
        echo display_form($reset_form) 
        ?>
        <input class="btn btn-lg btn-primary btn-block" type="submit" value="Submit" />
    </form>
    <a class="text-primary link-primary" href="./sign-in.php">Go back to Sign-In page</a>
</div>

<?php
require('./includes/footer.php');
?>