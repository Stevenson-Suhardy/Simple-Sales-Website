<?php
// Set the title page
$title = "Home Page";

// Require header
require("./includes/header.php");

// Check to see if the user is logged in or not
unsignedRedirect();
?>
<div class="container">
    <h1 class="cover text-center">Titan Link<br/></h1>
    <hr/>
    <p class="lead text-justify">This is the homepage for my WEBD3201 course at Durham College. In this course we are going to learn web development intermediate to improve our fundamentals and make it into something a little more advanced. This course covers bootstrap, CSS, PHP, and HTML. There are a total of 4 labs for this course which will be all implemented on this website. Titan Link is only an imaginary company name that I came up with on the spot.</p>
<div class="container text-center">
    <p class="lead">
        <a href="https://durhamcollege.ca/" class="btn btn-lg btn-secondary">Durham College</a>
    </p>
<?php
require "./includes/footer.php";
?>    