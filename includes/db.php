<?php
/*
Stevenson Suhardy
September 16, 2022
Webd3201
*/

$conn = db_connect();
echo $conn;
// Pg prepare statements
pg_prepare($conn, "call_insert", "INSERT INTO calls(ClientId, TimeOfCall) VALUES ($1, $2);");
pg_prepare($conn, "select_all_calls_offset", "SELECT * FROM calls WHERE clientid = ANY($1) LIMIT $2 OFFSET $3");
pg_prepare($conn, "select_all_calls", "SELECT * FROM calls");
pg_prepare($conn, "user_select", 'SELECT * FROM users WHERE emailaddress = $1');
pg_prepare($conn, "user_select_all", "SELECT * FROM users WHERE Enable = $1");
pg_prepare($conn, "salesperson_insert", "INSERT INTO users(EmailAddress, Password, FirstName, LastName, PhoneExtension, LastAccess, EnrolDate, Enable, Type) VALUES ($1, crypt($2, gen_salt('bf')), $3, $4, $5, $6, $7, true, 's');");
pg_prepare($conn, "select_client", "SELECT * FROM clients WHERE emailaddress = $1");
pg_prepare($conn, "select_sales_people", "SELECT * FROM users WHERE Type = $1");
pg_prepare($conn, "select_sales_people_details", "SELECT emailaddress, firstname, lastname, phoneextension, lastaccess, enroldate FROM users WHERE Type = $1 LIMIT $2 OFFSET $3");
pg_prepare($conn, "client_insert", "INSERT INTO clients(SalesPersonId, FirstName, LastName, EmailAddress, PhoneNumber, Extension, LogoPath) VALUES ($1, $2, $3, $4, $5, $6, $7);");
pg_prepare($conn, "select_client_from_sales_person", "SELECT * FROM clients WHERE SalesPersonId = $1");
pg_prepare($conn, "select_client_id", "SELECT * FROM clients WHERE clientid = $1");
pg_prepare($conn, "user_update_password", "UPDATE users SET password = crypt($1, gen_salt('bf')) WHERE emailaddress = $2");
pg_prepare($conn, "user_update_login_time", "UPDATE users SET lastaccess = '" . date("Y-m-d H:i:s") . "' WHERE emailaddress = $1");
pg_prepare($conn, "sales_client_select_all", "SELECT emailaddress, firstname, lastname, phonenumber, extension, logopath FROM clients  WHERE salespersonid = $1 LIMIT $2 OFFSET $3");
pg_prepare($conn, "select_related_sales_and_client", "SELECT * FROM clients WHERE salespersonid = $1");
pg_prepare($conn, "client_select_all", "SELECT emailaddress, firstname, lastname, phonenumber, extension, logopath FROM clients LIMIT $1 OFFSET $2");
pg_prepare($conn, "active_sales_people_select", "SELECT emailaddress, firstname, lastname, enable FROM users WHERE Type=$1 LIMIT $2 OFFSET $3");
pg_prepare($conn, "select_all_clients", "SELECT * FROM clients");
pg_prepare($conn, "update_logo_path", "UPDATE clients SET logopath = $1 WHERE clientid = $2");
pg_prepare($conn, "user_count", "SELECT * FROM users WHERE Enable = $1");
pg_prepare($conn, "update_active_user", "UPDATE users SET Enable = $1 WHERE emailaddress = $2");

// Function to connect to the database
function db_connect() {
    $connection = pg_connect("host=".DB_HOST." port=".DB_PORT." dbname=".DATABASE." user=".DB_ADMIN." password=".DB_PASSWORD);
    // return the connection
    return $connection;
}

// This function returns a single user depending on the email passed on the user
function user_select($email) {
    // Connects to database
    $conn = db_connect();
    // Stores the result of execute into a variable
    $result = pg_execute($conn, "user_select", array($email));
    // Checks to see if there is a result
    if(pg_num_rows($result) == 1) {
        // Stores the user details into an associative array called user
        $user = pg_fetch_assoc($result, 0);
        // Returns the associative array
        return $user;
    }
    // If there is an error
    else {
        // Return false
        return false;
    }
}

function user_count($enabled) {
    $conn = db_connect();
    $result = pg_execute($conn, "user_count", [$enabled]);

    return pg_num_rows($result);
}

function enabled_or_disabled_user_select_all($enabled) {
    $conn = db_connect();
    $user = [];
    $result = pg_execute($conn, "user_select_all", [$enabled]);

    if (pg_num_rows($result) > 0) {
        for ($i = 0; $i < pg_num_rows($result); $i++) {
            $user[$i] = pg_fetch_assoc($result, $i);
        }
        return $user;
    }
    else {
        return $user;
    }
}

// This function is used to authenticate the user and check to see if their email and password matches with the data on the database
function user_authenticate($emailaddress, $plain_password) {
    // Using user_select to get details of the user
    $user = user_select($emailaddress);
    if ($user['enable'] == 't') {
        // Verifies whether the password the user put in is equal to the password in the database.
        $verify_password = password_verify($plain_password, $user['password']);
        // If it returns true
        if($verify_password) {
            // Connect to the database
            $conn = db_connect();
            // Updates the user last access
            $result = pg_execute($conn, "user_update_login_time", [$emailaddress]);
            // Returns the associative array
            return $user;
        }
        // Anything else
        else{
            // Returns false
            return false;
        }
    }
    else {
        return false;
    }
}

// This function checks if the user is an admin or not
function isAdmin()
{
    // Using user_select to get the current logged in user details
    $user = user_select($_SESSION['email']);
    // Checks if they are an admin
    if ($user['type'] == 'a') {
        // Return true if the user is an admin
        return true;
    } else {
        // Return false if the user is not an admin
        return false;
    }
}

// This function checks if the user is an sales person or not
function isSalesperson() 
{
    // Using user_select to get the current logged in user details
    $user = user_select($_SESSION['email']);
    // Checks if they are a salesperson
    if ($user['type'] == 's') {
        // Return true if the user is a salesperson
        return true;
    }
    else {
        // Return false if the user is not a salesperson
        return false;
    }
}


// This function inserts a sales person to the users table
function InsertSalesperson($email, $first_name, $last_name, $extension, $password) {
    // Connect to the database
    $conn = db_connect();
    // Checks to see if the email they entered alreadyt exists inside the databse using user_select function
    if (user_select($email) == false) {
        // Execute the query using the appropirate pg_prepare
        $result = pg_execute($conn, "salesperson_insert", [$email, $password, $first_name, $last_name, $extension, null, date("Y-m-d H:i:s")]);
        // Execute a select query to see if the salesperson has been successfully inserted
        $result2 = pg_execute($conn, "user_select", [$email]);
        // If it returns 1 result
        if (pg_num_rows($result2) == 1) {
            // Return true
            return true;
        }
    }
    // Return false if email already exists
    else {
        return false;                                           
    }
}

// This functions lists all sales people in the database
function ListAllSalesPeople() {
    // Connect to the database
    $conn = db_connect();
    // Execute a query for every user that has the 's' type meaning salesperson
    $result = pg_execute($conn, "select_sales_people", ['s']);
    // Setting up the output
    $output = "";
    // Store number of rows in a variable
    $total_rows = pg_num_rows($result);
    // Checks to see if there are more than 0 rows
    if ($total_rows >= 1) {
        // Loops through the result
        for($row_number = 0; $row_number < $total_rows; $row_number++ ){
            // Stores the result inside an associative array
            $sales_people = pg_fetch_assoc($result, $row_number);
            // Creating an option for a drop down box
            $output .= "<option value='" . $sales_people['id'] . "'>" . $sales_people['firstname'] . " " . $sales_people['lastname'] . "</option>";
        }
        // return the drop down options
        return $output;
    }
}

// This functions outputs the client in an associative array based on their email
function client_select($email) {
    // Connect to the database
    $conn = db_connect();
    // Executes the query based on the email and store it in a variable
    $result = pg_execute($conn, "select_client", [$email]);
    // Checks to see if it returns 1 result
    if(pg_num_rows($result) == 1) {
        // store it in an associative array
        $user = pg_fetch_assoc($result, 0);
        // return the associative array
        return $user;
    }
    // returns false if there are more than 1 or less than 1 row
    else {
        return false;
    }
}

// This function checks whether the client exists based on their id
function client_id_select($id) {
    // Connect to the database
    $conn = db_connect();
    // Executes the query based on the email and store it in a variable
    $result = pg_execute($conn, "select_client_id", [$id]);
    // Checks to see if it returns 1 result
    if (pg_num_rows($result) == 1) {
        // returns true
        return true;
    }
    // anything else
    else {
        // returns false
        return false;
    }
}

// This function inserts a new client into the database
function InsertClient($sales_person_id, $first_name, $last_name, $email_address, $phone_number, $extension, $logopath) {
    // Connects to the database
    $conn = db_connect();
    // Checks to see if email address already exists in the database
    // If it does not exist
    if (client_select($email_address) == false) {
        // Execute query to insert the client into the database
        $result = pg_execute($conn, "client_insert", [$sales_person_id, $first_name, $last_name, $email_address, $phone_number, $extension, $logopath]);
        // Execute query to see if client has been inserted into the database
        $result2 = pg_execute($conn, "select_client", [$email_address]);
        // If it returns 1 result
        if (pg_num_rows($result2) == 1) {
            // return true
            return true;
        }
    }
    // anything else
    else {
        // return false
        return false;
    }
}
// This function displays all the clients related to a sales person and generate a table to put all the client into it.
function ListAllClientsAssociated($sales_person_id) {
    // Connects to the database
    $conn = db_connect();
    // Execute query to get all clients related to the logged in sales person
    $result = pg_execute($conn, "select_client_from_sales_person", [$sales_person_id]);
    // Creating a data table to display all clients related to the salesperson
    $output = "<div class=\"table-responsive\">";
    $output .= "<table class=\"table table-striped table-sm\">";
    $output .= "<thead>";
    $output .= "<tr>";
    $output .= "<th>Client ID</th>";
    $output .= "<th>First Name</th>";
    $output .= "<th>Last Name</th>";
    $output .= "<th>Email Address</th>";
    $output .= "<th>Phone Number</th>";
    $output .= "<th>Extension</th>";

    $output .= "</tr>";
    $output .= "</thead>";
    $output .= "<tbody>";
    // Stores the number of rows into a variable
    $total_rows = pg_num_rows($result);
    // Checks to see if it returns more than 0 result
    if ($total_rows >= 1) {
        // Loop through the result
        for ($row_number = 0; $row_number < $total_rows; $row_number++) {
            // Store result in an associative array
            $clients = pg_fetch_assoc($result, $row_number);
            // Inserting data into the HTML table
            $output .= "<tr>";
            $output .= "<td>" . $clients["clientid"] ."</td>";
            $output .= "<td>" . $clients["firstname"] ."</td>";
            $output .= "<td>" . $clients["lastname"] ."</td>";
            $output .= "<td>" . $clients["emailaddress"] ."</td>";
            $output .= "<td>" . $clients["phonenumber"] ."</td>";
            $output .= "<td>" . $clients["extension"] ."</td>";
            $output .= "</tr>";
        }
    }
    // Closing tags
    $output .= "</tbody>";
    $output .= "</table>";
    // Returns the table
    return $output;
}

// This function will return options for a drop down menu of clients related to the salesperson in HTML
function ClientDropBox($sales_person_id) {
    // Connects to the database
    $conn = db_connect();
    // Executes query and store it in result
    $result = pg_execute($conn, "select_client_from_sales_person", [$sales_person_id]);
    // Stores the number of rows inside a variable
    $total_rows = pg_num_rows($result);
    // Checks to see if there are more than 0 results
    if ($total_rows >= 1) {
        // Loop through the results
        for($row_number = 0; $row_number < $total_rows; $row_number++ ){
            // Stores result inside an associative array
            $clients = pg_fetch_assoc($result, $row_number);
            // Creating options for the drop down menu
            $output .= "<option value='" . $clients['clientid'] . "'>" . $clients['firstname'] . " " . $clients['lastname'] . "</option>";
        }
        // returns the options
        return $output;
    }
}


// This function inserts a new call record into the calls table.
function InsertCallRecord($client_id) {
    // Connect to the database
    $conn = db_connect();
    // Executes the query to insert a call from client
    $result = pg_execute($conn, "call_insert", [$client_id, date("Y-m-d H:i:s")]);
    // returns true
    return true;
}

// This function will update a user's password in the database
function UpdatePassword($password, $email_address) {
    // Using user_select to get user details
    $user = user_select($email_address);
    // Conecting to the database
    $conn = db_connect();
    // Updates the user password
    $result = pg_execute($conn, "user_update_password", [$password, $user['emailaddress']]);
    // Return true
    return true;
}

// This function will return an associative array of clients. Show all clients for admin, and show clients related to them for a salesperson
function client_select_all($page) {
    // Connects to database
    $conn = db_connect();
    // Calculating offset
    $offset = ($page - 1) * RECORDS_PER_PAGE;
    
    // Checks to see if they are an admin
    if (isAdmin()) {
        // Executes query and store it in a variable
        $result = pg_execute($conn, "client_select_all", [RECORDS_PER_PAGE, $offset]);
        // Creating an array variable
        $clients = [];
        // Stores the number of rows inside a variable
        $total_result = pg_num_rows($result);
        // Checks to see if the rows is more than 0
        if ($total_result > 0) {
            // Loop through the result
            for ($i = 0; $i < $total_result; $i++) {
                // Stores the result in an associative array and store it in the variable
                $clients[$i] = pg_fetch_assoc($result, $i);
            }
        }
    }
    // Checks to see if they are a salesperson
    else if (isSalesperson()) {
        // Get details of the logged in user
        $user = user_select($_SESSION['email']);
        // Get the sales person id
        $sales_person_id = $user['id'];
        // Executes the following query
        $result = pg_execute($conn, "sales_client_select_all", [$sales_person_id, RECORDS_PER_PAGE, $offset]);
        // Stores the number of rows inside a variable
        $total_result = pg_num_rows($result);
        // Checks to see if the rows is more than 0
        if ($total_result > 0) {
            // Loop through the result
            for ($i = 0; $i < $total_result; $i++) {
                // Stores the result in an associative array and store it in the variable
                $clients[$i] = pg_fetch_assoc($result, $i);
            }
        }
    }
    // Returns the associative array
    return $clients;
}

// This function will return how many clients there are
function client_count() {
    // Connect to the database
    $conn = db_connect();
    // Checks to see if the user is an admin
    if (isAdmin()) {
        // Execute the query to return all clients in the database
        $result = pg_execute($conn, "select_all_clients", []);
    }
    // Checks to see if the user is a salesperson
    else if (isSalesperson()) {
        // Getting logged in user details
        $user = user_select($_SESSION['email']);
        // Storing the id into a variable
        $sales_person_id = $user['id'];
        // Executing the query to return all clients related to the sales person
        $result = pg_execute($conn, "select_client_from_sales_person", [$sales_person_id]);
    }
    // return how many rows there are
    return pg_num_rows($result);
}

// This function will return all the sales people inside the database
function salespeople_select_all($page) {
    // Connect to the database
    $conn = db_connect();
    // Calculating offset
    $offset = ($page - 1) * RECORDS_PER_PAGE;
    // Executing query
    $result = pg_execute($conn, "select_sales_people_details", ["s", RECORDS_PER_PAGE, $offset]);
    // Creating an array variable
    $sales_people = [];
    // Storing the number of rows inside a variable
    $total_result = pg_num_rows($result);
    // Checks to see if the number of rows is more than 0
    if ($total_result > 0) {
        // Loops through the result
        for ($i = 0; $i < $total_result; $i++) {
            // Put the result in an associative array
            $sales_people[$i] = pg_fetch_assoc($result, $i);
        }
    }
    // return the associative array
    return $sales_people;
}

function active_sales_people_select($page) {
    $conn = db_connect();
    // Calculating offset
    $offset = ($page - 1) * RECORDS_PER_PAGE;
    // Executing query
    $result = pg_execute($conn, "active_sales_people_select", ["s", RECORDS_PER_PAGE, $offset]);
    // Creating an array variable
    $sales_people = [];
    // Storing the number of rows inside a variable
    $total_result = pg_num_rows($result);
    // Checks to see if the number of rows is more than 0
    if ($total_result > 0) {
        // Loops through the result
        for ($i = 0; $i < $total_result; $i++) {
            // Put the result in an associative array
            $sales_people[$i] = pg_fetch_assoc($result, $i);
        }
    }
    // return the associative array
    return $sales_people;
}

// This function returns how many sales peopl there are inside the database
function sales_people_count() {
    // Connect to the database
    $conn = db_connect();

    // Execute the query
    $result = pg_execute($conn, "select_sales_people", ["s"]);
    // returns the number of rows
    return pg_num_rows($result);
}

// This function returns the details of call from the database
function call_select_all($page) {
    // connects to database
    $conn = db_connect();
    // calculates offset
    $offset = ($page - 1) * RECORDS_PER_PAGE;
    // Creating an array variable
    $calls = [];
    $user = user_select($_SESSION['email']);
    $sales_person_id = $user['id'];

    $client_result = pg_execute($conn, "select_related_sales_and_client", [$sales_person_id]);
    $client_ids = [];
    for ($row_number = 0; $row_number < pg_num_rows($client_result); $row_number++) {
        $client_ids[$row_number] = pg_fetch_assoc($client_result, $row_number)["clientid"];
    }
    
    $counter = 0;
    $str_client_ids = "{";
    for ($i = 0; $i < sizeof($client_ids); $i++) {
        if ($i == 0) {
            $str_client_ids .= $client_ids[$i];
        }
        else {
            $str_client_ids .= ", " . $client_ids[$i];
        }
    }
    $str_client_ids .= "}";
    // Execute query
    $result = pg_execute($conn, "select_all_calls_offset", [$str_client_ids, RECORDS_PER_PAGE, $offset]);
    
    // Storing the number of rows inside a variable
    $total_result = pg_num_rows($result);
    // Checks to see if the number of rows is more than 0
    if ($total_result > 0) {
        // Loops through the result
        for ($row_number = 0; $row_number < $total_result; $row_number++) {
            $check_client = pg_execute($conn, "select_client_id", [pg_fetch_assoc($result, $row_number)['clientid']]);
            // Put the result in an associative array
            // TODO: Check is salespersonid in the clientid is the same as the current salespersonid
            $client = pg_fetch_assoc($check_client, 0);
            if ($client['salespersonid'] == $sales_person_id) {
                $calls[$counter] = pg_fetch_assoc($result, $row_number);
                $counter++;
            }
        }
    }
    
    // returns the associative array
    return $calls;
}

// This function returns how many calls there are from a client related to a sales person
function calls_count() {
    // Connect to database
    $conn = db_connect();
    $user = user_select($_SESSION['email']);
    $sales_person_id = $user['id'];
    $client_result = pg_execute($conn, "select_related_sales_and_client", [$sales_person_id]);
    $client_ids = [];
    for ($row_number = 0; $row_number < pg_num_rows($client_result); $row_number++) {
        $client_ids[$row_number] = pg_fetch_assoc($client_result, $row_number)["clientid"];
    }
    // Executes query
    $result = pg_execute($conn, "select_all_calls", []);
    $counter = 0;
    for ($row_number = 0; $row_number < pg_num_rows($result); $row_number++) {
        if (in_array(pg_fetch_assoc($result, $row_number)["clientid"], $client_ids)) {
            $counter++;
        }
    }
    // returns the total number of rows
    return $counter;
}

// This function updates the logo path for a client
function UpdateLogoPath($file_path, $client_id) {
    // connects to the database
    $conn = db_connect();
    // executes the query
    $result = pg_execute($conn, "update_logo_path", [$file_path, $client_id]);
    // returns true
    return true;
}

function activate_user($email, $enable) {
    $conn = db_connect();
    $result = pg_execute($conn, "update_active_user", [$enable, $email]);
    return true;
}

?>