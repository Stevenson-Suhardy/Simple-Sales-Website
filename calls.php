<?php
$title = "Calls";

require("./includes/header.php");

$error = "";

if ($_SERVER["REQUEST_METHOD"] == "GET") {
    $client_id = "";
}
else if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $client_id = trim($_POST["client"]);
    $client_id = (int)$client_id;
    $message = "Client Call Record has been successfully created!";
    InsertCallRecord($client_id);
}

?>

<div class="container">
    <h1 class="h2">Client Call Records</h1>
    <hr/>
    <?php if(isset($message) && $message != "") {echo "<h5>".$message."</h5>";} ?>
    <form class="form" method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>">
    <div class="mb-3">
    <label for="client">Choose the client that made the call:</label>
    </div>
    <div class="mb-3">
    <select class='form-control' name="client" id="client">
        <optgroup label="Your Clients">
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
$page = 1;

if (isset($_GET["page"])) {
    $page = $_GET["page"];
}

echo display_table(
    [
        "callid" => "Call ID",
        "clientid" => "Client ID",
        "timeofcall" => "Time Of Call",
    ],
    call_select_all($page),
    calls_count(),
    $page
);
?>

<?php
require("./includes/footer.php");
?>