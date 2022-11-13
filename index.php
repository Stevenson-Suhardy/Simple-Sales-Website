<?php
$title = "Home Page";

require("./includes/header.php");

unsignedRedirect();
?>
<h1 class="cover">Titan Link<br/></h1>
<p class="lead">This is the homepage for my WEBD3201 course at Durham College. In this course we are going to learn web development intermediate to improve our fundamentals and make it into something a little more advanced. This course covers bootstrap, CSS, PHP, and HTML. There are a total of 4 labs for this course which will be all implemented on this website. Titan Link is only an imaginary company name that I came up with on the spot.</p>
<p class="lead">
    <a href="https://durhamcollege.ca/" class="btn btn-lg btn-secondary">Durham College</a>
</p>
</div>
<?php
ListAllSalesPeople();
require "./includes/footer.php";
?>    