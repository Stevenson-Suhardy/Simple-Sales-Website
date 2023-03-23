<?php
/*
Stevenson Suhardy
September 16, 2022
Webd3201
*/

// Set the title of the page
$title = "Calls";

// Require the header
require("./includes/header.php");
// Declare error variable to store erros
$error = "";

// Check to see if server request method is get
if ($_SERVER["REQUEST_METHOD"] == "GET") {
    $client_id = "";
}
// Check to see if the server request method is post
else if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Set the client id to the post value
    $client_id = trim($_POST["client"]);
    // Convert to an integer
    $client_id = (int)$client_id;
    // Flash the message
    $message = "Client Call Record has been successfully created!";
    // Insert the call record inside the database
    InsertCallRecord($client_id);
}

?>

<div class="container">
    <h1 class="h2">Client Call Records</h1>
    <hr/>
    <!-- Display flash message when it has value -->
    <?php if(isset($message) && $message != "") {echo "<h5>".$message."</h5>";} ?>
    <!-- The form for call record -->
    <form class="form" method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>">
    <div class="mb-3">
    <label for="client">Choose the client that made the call:</label>
    </div>
    <div class="mb-3">
    <select class='form-control' name="client" id="client">
        <optgroup label="Your Clients">
            <!-- Displaying all clients related to the logged in sales person -->
            <?php
                $user = user_select($_SESSION['email']);
                echo ClientDropBox($user['id']);
            ?>
        </optgroup>
    </select>
    </div>
    <div class="mb-3">
    <button class="btn btn-lg btn-primary" type="submit">Submit</button>
    </div>
    </form>

<?php
// Declare page variable
$page = 1;
// Check to see if there is value in get array page
if (isset($_GET["page"])) {
    // Set the variable to the current page
    $page = $_GET["page"];
}
// Display the table
echo display_table(
    [
        "callid" => "Call ID",
        "clientid" => "Client ID",
        "timeofcall" => "Time Of Call",
    ],
    call_select_all($page),
    []
);
// Display pagination for the table
echo display_pagination(calls_count(), $page);
?>

<?php
require("./includes/footer.php");
?>