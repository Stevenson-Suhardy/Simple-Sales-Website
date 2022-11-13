<?php
/*
Stevenson Suhardy
September 16, 2022
Webd3201
*/
$title = "Sales People";

require("./includes/header.php");

unsignedRedirect();

if (!isAdmin()) {
    redirect("./index.php");
}

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

$error = "";
if ($_SERVER["REQUEST_METHOD"] == "GET") {
    $first_name = "";
    $last_name = "";
    $email = "";
    $password = "";
}
else if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $sales_people_form[0]['value'] = trim($_POST["first_name"]);
    $sales_people_form[1]['value'] = trim($_POST["last_name"]);
    $sales_people_form[2]['value'] = trim($_POST["email"]);
    $sales_people_form[3]['value'] = trim($_POST["extension"]);
    $sales_people_form[4]['value'] = trim($_POST["password"]);

    if (!isset($sales_people_form[0]['value']) || $sales_people_form[0]['value'] == "") {
        $error .= "Please enter the sales person first name.<br/>";
    } 
    if (!isset($sales_people_form[1]['value']) || $sales_people_form[1]['value'] == "") {
        $error .= "Please enter the sales person last name.<br/>";
    }
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
    if (!isset($sales_people_form[4]['value']) || $sales_people_form[4]['value'] == "") {
        $error .= "Please enter a password.<br/>";
    }

    if ($error == "") {
        $message = "Sales Person has been created successfully!";
        $first_name = $sales_people_form[0]['value'];
        $last_name = $sales_people_form[1]['value'];
        $email = $sales_people_form[2]['value'];
        $extension = $sales_people_form[3]['value'];
        $password = $sales_people_form[4]['value'];
        
        $insert = InsertSalesperson($email, $first_name, $last_name, $extension, $password);
        if ($insert == false) {
            $message = "";
            $error = "Email Address has already been taken. Please enter another email address.<br/>";
        }
        else {
            $sales_people_form[0]['value'] = "";
            $sales_people_form[1]['value'] = "";
            $sales_people_form[2]['value'] = "";
            $sales_people_form[3]['value'] = "";
            $sales_people_form[4]['value'] = "";
            
        }
    }
}
?>

<div class="container">
    <h1 class="h2">Sales People</h1>
    <hr />
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
$page = 1;

if (isset($_GET["page"])) {
    $page = $_GET["page"];
}

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
    sales_people_count(),
    $page
);
?>

<?php
require("./includes/footer.php");
?>