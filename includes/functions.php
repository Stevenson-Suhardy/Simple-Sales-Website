<?php
/*
Stevenson Suhardy
September 16, 2022
Webd3201
*/

// This function is used to redirect user to the passed in url
function redirect($url) {
    // Redirect user
    header("Location: ".$url);
    // Flush the output buffer
    ob_flush();
}

// This function will redirect user that is not logged in to the sign-in page
function unsignedRedirect() {
    // Checks to see if they are logged in
    if (!isLoggedIn()) {
        // Redirect them to the sign in page if they are not logged in
        redirect("./sign-in.php");

        // return true
        return true;
    }
    // anything else
    else {
        // return false
        return false;
    }
}

// Checks to see if the url passed in is the current page
function isCurrentPage($url)
{
    // Checks the base name of the current page
    if (basename($_SERVER["PHP_SELF"]) === $url) {
        // returns true if it is the current page
        return true;
    }
}

// This function checks to see if the user is logged in
function isLoggedIn() {
    // Check if the $_SESSION['email'] is set or not
    if (isset($_SESSION['email'])) {
        // return true
        return true;
    }
    // anything else
    else {
        // return false
        return false;
    }
}

// This function is for testing purposes to see the value of an associative array or array
function dump($arg) {
    echo "<pre>";
    print_r($arg);
    echo "</pre>";
}

// This function returns flash message set in the session
function getMessage() {
    return $_SESSION['message'];
}

// This function checks to see if the flash message in the session is set or not
function isMessage() {
    return isset($_SESSION['message'])?true:false;
}

// This function will flash message in the website
function flashMessage() {
    $message = "";
    if(isMessage()) {
        $message = $_SESSION['message'];
        removeMessage();
    }
    return $message;
}

// This function is used to set what message to say in the flash message
function setMessage($msg) {
    $_SESSION['message'] = $msg;
}

// This function is used to remove the session message variable
function removeMessage() {
    unset($_SESSION['message']);
}

// This function is used to write to a log or create a new one if it does not exist
function writeToActivityLog($message) {
    // Opens the log and based on the date in append mode and put it in a variable 
    $log = fopen('./logs/'.date('Y-m-d').'_log.txt', 'a');
    //fwrite($log, "Sign in success at ".date("Y-m-d H:i:s").". User ".$_SESSION['email']." signed in.\n");
    // Writes the message to the log
    fwrite($log, $message);
    // Close the stream
    fclose($log);
}

// This function will display an appropriate form based on the passed in associative form array
function display_form($form_data)
{
    $output = "";
    // Loops through the associative array
    foreach ($form_data as $data) {
        // Creates a HTML form
        $output .= "<div class=\"mb-3\">";
        $output .= "<p>" . $data["label"] . "</p>";                               
        $output .= "<label for=\"inputID\" class=\"sr-only\">" . $data["label"] . "</label>";
        $output .= "<input type=" . $data["type"] . " value='" . $data["value"] . "' name=" . $data["name"] . " id=\"inputID\" class=\"form-control\" placeholder='" . $data["label"] . "' required autofocus>";
        $output .= "</div>";
    }
    // Returns the form
    return $output;
}

// This function will return a table based on the data passed in
function display_table($table_data, $datas, $active) {
    // Creating an output variable to store the HTML table
    $output = "";
    $output = "<div class=\"table-responsive\">";
    $output .= "<table class=\"table table-striped table-sm\">";
    $output .= "<thead>";
    $output .= "<tr>";

    if (sizeof($active) > 0) {
        $radio = true;
    }
    else {
        $radio = false;
    }

    // Using foreach to loop through the table fields
    foreach($table_data as $data) {
        // Creating table fields
        $output .= "<th>" . $data . "</th>";
    }

    $output .= "</tr>";
    $output .= "</thead>";
    $output .= "<tbody>";
    // Loops through the actual data field values to fill in the table
    foreach ($datas as $data=>$value) {
        $output .= "<tr>";
        // Storing all keys inside a variable
        $all_keys = array_keys($value);
        // Loops through the datas
        if ($radio == false) {
            for ($index = 0; $index < count($table_data); $index++) {
                // If the key is not logo path
                if ($all_keys[$index] != "logopath") {
                    // Put the actual data value inside the table
                    $output .= "<td>" . $value[$all_keys[$index]] . "</td>";
                }
                // If the key is logopath
                else if ($all_keys[$index] == "logopath") {
                    // Generate the image based on the logo path
                    $output .= "<td><img src=\"" . $value[$all_keys[$index]] . "\" alt=\"No Logo\" width=\"150\" /></td>";
                }
            }
        }
        else {
            for ($index = 0; $index < count($table_data); $index++) {
                // If the key is not logo path
                if ($all_keys[$index] != "enable") {
                    // Put the actual data value inside the table
                    $output .= "<td>" . $value[$all_keys[$index]] . "</td>";
                }
                else {
                    $user = user_select($value[$all_keys[0]]);
                    $active = $user['enable'];
                    $output .= "<td> 
            <form method='POST' action='" . $_SERVER["PHP_SELF"] . "'>";
                    if ($active == 't') {
                        $output .= "
                        <div>
                            <input type=\"radio\" name=\"active[" . $value[$all_keys[0]] . "]\" value=\"true\" checked>
                            <label class=\"form-check-label\">Active</label>
                        </div>
                        <div>
                            <input type=\"radio\" name=\"active[" . $value[$all_keys[0]] . "]\" value=\"false\">
                            <label class=\"form-check-label\">Inactive</label>
                        </div>";
                    }
                    else {
                        $output .= "
                        <div>
                            <input type=\"radio\" name=\"active[" . $value[$all_keys[0]] . "]\" value=\"true\">
                            <label class=\"form-check-label\">Active</label>
                        </div>
                        <div>
                            <input type=\"radio\" name=\"active[" . $value[$all_keys[0]] . "]\" value=\"false\" checked>
                            <label class=\"form-check-label\">Inactive</label>
                        </div>";
                    }
            $output .= "
            <input type=\"submit\" value=\"Update\" class=\"btn btn-info\" />
            </form>
            </td>";
                }
            }
        }
        $output .= "</tr>";
    }

    $output .= "</tbody>";
    $output .= "</table>";
    // Returns the HTML table along with the pagination
    return $output;
}

function display_pagination($count, $page) {
    $output = "";

    // Calculating the total page for the pagination and round it up just in case it is a floating point number
    $total_pages = ceil($count / RECORDS_PER_PAGE);

    $output .= "<nav aria-label=\"Page navigation\">";
    $output .= "<ul class=\"pagination justify-content-center\">";
    
    // Calculating for a previous button and next button
    $previous = $page - 1;
    $next = $page + 1;
    // If the page is more than 2
    if ($page >= 2) {
        //  Generate a button called previous for the pagination that will go to the previous table page
        $output .= '<li class="page-item"><a class="page-link" href="' . $_SERVER['PHP_SELF'] . '?page=' . $previous . '">Previous</a></li>';
    }
    // Loop through to generate the pagination
    for($page_number = 1; $page_number<= $total_pages; $page_number++) {  
        // Checks to see if the page number is the current page
        if ($page_number == $page) {
            // Generate the button and set it as active
            $output .= '<li class="page-item"><a class="page-link active" href="' . $_SERVER['PHP_SELF'] . '?page=' . $page_number . '">' . $page_number . ' </a></li>';
        }
        // Anything else
        else {
            // Generate a normal button for pagination
            $output .= '<li class="page-item"><a class="page-link" href="' . $_SERVER['PHP_SELF'] . '?page=' . $page_number . '">' . $page_number . ' </a></li>';
        }
    }
    // Checks to see if the current page is less than total page
    if ($page < $total_pages) {
        // Generate a next button when there are still more pages after the current page
        $output .= '<li class="page-item"><a class="page-link" href="' . $_SERVER['PHP_SELF'] . '?page=' . $next . '">Next</a></li>';
    }

    $output .= "</ul>";
    $output .= "</nav>";
    return $output;
}

// This function will log a mail after calling the mail function
function logMail($email, $subject) {
    // Opens the log and based on the date in append mode and put it in a variable 
    $log = fopen('./logs/mail.log.txt', 'a');
    // Writes the message to the log
    fwrite($log, date('Y-m-d H:i:s') . "\tEmail Sent.\t To: $email\tSubject: $subject\n\n");
    // Close the stream
    fclose($log);
}

?>
