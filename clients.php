<?php
/*
Stevenson Suhardy
September 16, 2022
Webd3201
*/
// Declares title of the page
$title = "Clients";

// Requires the header.php file
require("./includes/header.php");

// Calling function to check whether the user is signed in or not
unsignedRedirect();

// Declaring some variable
$error = "";
// Getting the logged in user details
$user = user_select($_SESSION['email']);
// Storing their id
$sales_person_id = $user['id'];

// Checking to see if the user is a sales person
if (isSalesPerson()) {
    // Creating a form using an associative array
    $clients_form = [
        [
            "type" => 'text',
            "name" => "first_name",
            "value" => "",
            "label" => "First Name"
        ],
        [
            "type" => 'text',
            "name" => "last_name",
            "value" => "",
            "label" => "Last Name"
        ],
        [
            "type" => 'email',
            "name" => "email",
            "value" => "",
            "label" => "Email Address"
        ],[
            "type" => 'text',
            "name" => "phone_number",
            "value" => "",
            "label" => "Phone Number"
        ],[
            "type" => 'number',
            "name" => "extension",
            "value" => "",
            "label" => "Extension"
        ]
    ];
    // Checking to see if the request method is get
    if ($_SERVER["REQUEST_METHOD"] == "GET") {
        // Declare some variables
        $first_name = "";
        $last_name = "";
        $email = "";
        $phone_number = "";
        $extension = "";
    }
    // Checking to see if the request method is post
    else if ($_SERVER["REQUEST_METHOD"] == "POST") {
        // Storing all values input by the user into the form value
        $clients_form[0]['value'] = trim($_POST["first_name"]);
        $clients_form[1]['value'] = trim($_POST["last_name"]);
        $clients_form[2]['value'] = trim($_POST["email"]);
        $clients_form[3]['value'] = trim($_POST["phone_number"]);
        $clients_form[4]['value'] = trim($_POST["extension"]);

        // Input data validation, mainly checking to see if they are empty or not.
        if (!isset($clients_form[0]['value']) || $clients_form[0]['value'] == "") {
            $error .= "Please enter your first name.<br/>";
        }
        if (!isset($clients_form[1]['value']) || $clients_form[1]['value'] == "") {
            $error .= "Please enter your last name.<br/>";
        }
        if (!isset($clients_form[2]['value']) || $clients_form[2]['value'] == "") {
            $error .= "Please enter an email address.<br/>";
        }
        // Checking to see if the email exceeds the maximum number of characters
        else if (strlen($clients_form[2]['value']) > MAXIMUM_EMAIL_LENGTH) {
            $error .= "Email address entered (" . $clients_form[2]['value'] . ") cannot be more than " . MAXIMUM_EMAIL_LENGTH . " characters.<br/>";
            // Clearing the value from the textbox
            $clients_form[2]['value'] = "";
        }
        // Checking to see if the input is an email or at least formatted like an actual email
        else if (!filter_var($clients_form[2]['value'], FILTER_VALIDATE_EMAIL)) {
            $error .= "Email address entered (" . $clients_form[2]['value'] . ") is not a valid email address.<br/>";
            // Clearing the value from the textbox
            $clients_form[2]['value'] = "";
        }
        if (!isset($clients_form[3]['value']) || $clients_form[3]['value'] == "") {
            $error .= "Please enter your phone number.<br/>";
        }
        // Checking to see if the phone number input is formatted like a phone number using regex
        else if (!preg_match("/^\\+?[1-9][0-9]{7,14}$/", $clients_form[3]['value'])) {
            $error .= "Phone number must be in a valid format.<br./>";
            // Clearing the value from the textbox
            $clients_form[3]['value'] = "";
        }
        if (!isset($clients_form[4]['value']) || $clients_form[4]['value'] == "") {
            $error .= "Please enter your phone number extension.<br/>";
        }
        // CHecking to see if the extension is an integer or not
        else if (filter_var($clients_form[4]['value'], FILTER_VALIDATE_INT) === false) {
            $error .= "Phone number extension must be a number.<br./>";
            // Clearing the value from the textbox
            $clients_form[4]['value'] = "";
        }
        else if ($clients_form[4]['value'] < 0) {
            $error .= "Phone number extension cannot be a negative number.<br/>";
            $clients_form[4]['value'] = "";
        }
        // Uploaded file validation
        // Checking to see if there are any errors
        if ($_FILES["uploadfileName"]["error"] != 0) {
            // Put the following error message when there is an error
            $error .= "There was a problem with uploading your file. Please check if you have chosen a file.";
        }
        // Checking to see if the file size exceeds the maximum file size
        else if ($_FILES["uploadfileName"]["size"] > MAXIMUM_FILE_SIZE) {
            $error .= "File selected is too large. File must be smaller than " . ini_get("upload_max_filesize") . "B";
        }
        // Checking to see if the type of the file is a jpg/jpeg
        else if ($_FILES["uploadfileName"]["type"] != "image/jpeg" && $_FILES["uploadfileName"]["type"] != "image/pjpeg") {
            $error .= "Logo must be of type JPEG.";
        }
        if ($error == "") {
            // Flash the following message
            $message = "Client has been created and logo has been uploaded successfully!";
            // Store all input values inside a variable
            $first_name = $clients_form[0]['value'];
            $last_name = $clients_form[1]['value'];
            $email = $clients_form[2]['value'];
            $phone_number = $clients_form[3]['value'];
            $extension = $clients_form[4]['value'];

            // For Logo File
            // Storing the tmp_name of the uploaded file into a variable
            $tmp_name = $_FILES["uploadfileName"]["tmp_name"];
            // Storing the basename of the file path name into a variable
            $name = basename($_FILES["uploadfileName"]["name"]);
            // Creatinga file path into the uploads folder and naming it using the original basename of the file
            $file_path = "./uploads/$name";
            // Checking to see if a duplicate name exists inside the folder
            if (file_exists($file_path)) {
                // Display the following error message if file exists
                $error = "This file already exists or another file has the same name in the directory.";
                $message = "";
            }
            else {
                // Move the uploaded file to the designated file path
                move_uploaded_file($tmp_name, $file_path);
                // Insert the client
                $client_insert = InsertClient($sales_person_id, $first_name, $last_name, $email, $phone_number, $extension, $file_path);
                
                // If for some reason the insert fails
                if ($client_insert == false) {
                    // Clear message
                    $message = "";
                    // Set the email textbox to nothing and flash the following error message
                    $error = "Email Address has already been taken.<br/>Please enter another email address.<br/>";
                    $clients_form[2]['value'] = "";
                }
                else {
                    // Clear all the values to reset the form when the client insert is successful
                    $clients_form[0]['value'] = "";
                    $clients_form[1]['value'] = "";
                    $clients_form[2]['value'] = "";
                    $clients_form[3]['value'] = "";
                    $clients_form[4]['value'] = "";
                }
            }
        }
    }
}

// Checking to see if the user is an admin
else if (isAdmin()) {
    // Checking to see if the request method is get
    if ($_SERVER['REQUEST_METHOD'] == "GET") {
        // Declare a variable
        $sales_person = "";
    }
    // Checking to see if the request method is post
    else if($_SERVER['REQUEST_METHOD'] == "POST") {
        // Store the value inside a variable
        $sales_person = $_POST["sales_person"];
        // Converting the value to an integer
        $sales_person = (int)$sales_person;
        // Call the following function to list all clients associated with the sales person into a table
        $table = ListAllClientsAssociated($sales_person);
    }
}


?>

<!-- Checking to see if the user is a sales person and display all the following if they are. -->
<?php if(isSalesPerson()): ?>
<div class="container">
    <h1 class="h2">Clients</h1>
    <hr />
    <?php if(isset($message) && $message != "") {echo "<h5>".$message."</h5>";} ?>
    <?php if(isset($error) && $error != "") {echo "<h5>".$error."</h5>";} ?>
    <form class="form" id="uploadform" enctype="multipart/form-data" method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>">
        <?php echo display_form($clients_form); ?>
        <label class="form-label" for="uploadfileId">Select file for upload: </label>
        <input class="form-control" name="uploadfileName" type="file" id="uploadfileId" />
        <br/>
        <input class="btn btn-lg btn-primary btn-block" type="submit" value="Submit" />
    </form>
<?php endif; ?>

<!-- Checking to see if the user is an admin and display all the following if they are. -->
<?php if(isAdmin()): ?>
<div class="container">
    <h1 class="h2">List of Sales People and Clients Associated</h1>
    <hr/>
    <!-- Creating a drop down menu to list all sales person inside the drop down menu -->
    <form class="form" method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>">
    <div class="mb-3">
    <label for="sales_person">Choose a Sales Person:</label>
    </div>
    <div class="mb-3">
    <select class='form-control' name="sales_person" id="sales_person">
        <optgroup label="Sales People">
            <!-- List all sales people inside the drop down menu using the following function -->
            <?php echo ListAllSalesPeople(); ?>
        </optgroup>
    </select>
    </div>
    <div class="mb-3">
    <button class="btn btn-lg btn-primary" type="submit">Submit</button>
    </div>
    </form>
    <!-- Displaying the table if the variable is set or not empty -->
    <?php if(isset($table) && $table != "") {echo $table;} ?>
<?php endif; ?>

<hr/>
<h1 class="h2">Client List</h1>
<?php
// Declaring page variable and set it to 1
$page = 1;

// Checking to see if page inside GET array is set
if (isset($_GET["page"])) {
    // If it is, store it in the page variable
    $page = $_GET["page"];
}

// Display the following table using the following fields and appropriate values
echo display_table(
    [
        "email" => "Email",
        "first_name" => "First Name",
        "last_name" => "Last Name",
        "phone_number" => "Phone Number",
        "extension" => "Extension",
        "logo_path" => "Logo"
    ],
    client_select_all($page),
    []
);
// Display pagination
echo display_pagination(client_count(), $page);
?>

<?php
require("./includes/footer.php");
?>