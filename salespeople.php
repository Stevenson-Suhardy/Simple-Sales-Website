<?php
/*
Stevenson Suhardy
September 16, 2022
Webd3201
*/
// Set the title of the page
$title = "Sales People";
// Require the header
require("./includes/header.php");

// Check to see if the user is logged in or not
unsignedRedirect();

// Check to see if the user is an admin or not
if (!isAdmin()) {
    // If not, redirect to the index
    redirect("./index.php");
}

// Creating a new form
$sales_people_form = [
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
    ],
    [
        "type" => 'number',
        "name" => "extension",
        "value" => "",
        "label" => "Extension"
    ],
    [
        "type" => "password",
        "name" => "password",
        "value" => "",
        "label" => "Password"
    ]
];
// Declaring error variable to store errors
$error = "";
// Check to see if the request method is get
if ($_SERVER["REQUEST_METHOD"] == "GET") {
    $first_name = "";
    $last_name = "";
    $email = "";
    $password = "";
}
// Check to see if the request method is post
else if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Check if active is inside the post array
    if (array_key_exists("active", $_POST)) {
        // If it is, loop through it
        foreach($_POST["active"] as $user_email => $enable) {
            // Activate or deactivate depending on the passed in value
            activate_user($user_email, $enable);
            // Flash message
            $message = "$user_email state has been successfully changed to $enable!";
        }
    }
    // Anything else
    else {
        // Assigning values to the form array
        $sales_people_form[0]['value'] = trim($_POST["first_name"]);
        $sales_people_form[1]['value'] = trim($_POST["last_name"]);
        $sales_people_form[2]['value'] = trim($_POST["email"]);
        $sales_people_form[3]['value'] = trim($_POST["extension"]);
        $sales_people_form[4]['value'] = trim($_POST["password"]);

        // Validation for first name
        if (!isset($sales_people_form[0]['value']) || $sales_people_form[0]['value'] == "") {
            $error .= "Please enter the sales person first name.<br/>";
        } 
        // Validation for last name
        if (!isset($sales_people_form[1]['value']) || $sales_people_form[1]['value'] == "") {
            $error .= "Please enter the sales person last name.<br/>";
        }
        // Validation for email address
        if (!isset($sales_people_form[2]['value']) || $sales_people_form[2]['value'] == "") {
            $error .= "Please enter an email address.<br/>";
        }
        else if (strlen($sales_people_form[2]['value']) > MAXIMUM_EMAIL_LENGTH) {
            $error .= "Email address entered (" . $sales_people_form[2]['value'] . ") cannot be more than " . MAXIMUM_EMAIL_LENGTH . " characters.<br/>";
            $sales_people_form[2]['value'] = "";
        }
        else if (!filter_var($sales_people_form[2]['value'], FILTER_VALIDATE_EMAIL)) {
            $error .= "Email address entered (" . $sales_people_form[2]['value'] . ") is not a valid email address.<br/>";
            $sales_people_form[2]['value'] = "";
        }
        // Validation for extension
        if (!isset($sales_people_form[3]['value']) || $sales_people_form[3]['value'] == "") {
            $error .= "Please enter your phone number extension.<br/>";
        }
        else if (filter_var($sales_people_form[3]['value'], FILTER_VALIDATE_INT) === false) {
            $error .= "Phone number extension must be a number.<br./>";
            $sales_people_form[3]['value'] = "";
        }
        else if ($sales_people_form[3]['value'] < 0) {
            $error .= "Phone number extension cannot be a negative number.<br/>";
            $sales_people_form[3]['value'] = "";
        }
        // Validation for password
        if (!isset($sales_people_form[4]['value']) || $sales_people_form[4]['value'] == "") {
            $error .= "Please enter a password.<br/>";
        }
        // If there are no errors
        if ($error == "") {
            // Flash the message
            $message = "Sales Person has been created successfully!";
            // Assign form array values to variables for readability
            $first_name = $sales_people_form[0]['value'];
            $last_name = $sales_people_form[1]['value'];
            $email = $sales_people_form[2]['value'];
            $extension = $sales_people_form[3]['value'];
            $password = $sales_people_form[4]['value'];
            // Insert the sales person into the database
            $insert = InsertSalesperson($email, $first_name, $last_name, $extension, $password);
            // Check to see if the insertion is successful
            if ($insert == false) {
                $message = "";
                // At this point, the only thing that can fail the query is because the email has been taken by another user, because it can only be unique
                $error = "Email Address has already been taken. Please enter another email address.<br/>";
            }
            else {
                // Reset the form
                $sales_people_form[0]['value'] = "";
                $sales_people_form[1]['value'] = "";
                $sales_people_form[2]['value'] = "";
                $sales_people_form[3]['value'] = "";
                $sales_people_form[4]['value'] = "";
                
            }
        }   
    }
}
?>

<div class="container">
    <h1 class="h2">Sales People</h1>
    <hr />
    <!-- Display message and error when those have values -->
    <?php if(isset($message) && $message != "") {echo "<h5>".$message."</h5>";} ?>
    <?php if(isset($error) && $error != "") {echo "<h5>".$error."</h5>";} ?>
    <form class="form" method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>">
    <?php echo display_form($sales_people_form); ?>
    <button class="btn btn-lg btn-primary btn-block" type="submit">Submit</button>
    </form>
<hr/>
<div class="container">
    <h1 class="h2">List of Sales People</h1>
<?php
// Declaring page variable
$page = 1;

// Check to see if page inside get array has a value
if (isset($_GET["page"])) {
    // Set the page to the current page value
    $page = $_GET["page"];
}

// Display the table using all the information given
echo display_table(
    [
        "email" => "Email",
        "firstname" => "First Name",
        "lastname" => "Last Name",
        "extension" => "Extension",
        "lastaccess" => "Last Access",
        "enroldate" => "Enrol Date",
    ],
    salespeople_select_all($page),
    []
);
// Display the pagination for the table
echo display_pagination(sales_people_count(), $page);
?>
<hr/>
<div class="container">
    <h1 class="h2">Activate Sales People</h1>
<?php
// Display table to update sales person active or inactive
echo display_table(
    [
        "email" => "Email",
        "first_name" => "First Name",
        "last_name" => "Last Name",
        "active" => "Is Active?"
    ],
    active_sales_people_select($page),
    [
        "active" => [
            true => "Active",
            false => "Inactive"
        ]
    ]
);
// Display pagination for the table
echo display_pagination(sales_people_count(), $page);
?>

<?php
require("./includes/footer.php");
?>