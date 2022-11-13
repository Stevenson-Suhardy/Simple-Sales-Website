<?php
/*
Stevenson Suhardy
September 16, 2022
Webd3201
*/
define("COOKIE_LIFESPAN", "2592000");

// User Types
define("ADMIN", 'a');
define("AGENT", 'ag');
define("CLIENT", 'c');
define("PENDING", 'p');
define("DISABLED", 'd');
define("SALESPERSON", 's');

// Database Constants
define("DB_HOST", "localhost");
define("DB_PORT", "5432");
define("DB_PASSWORD", "Dvlive123");
define("DATABASE", "suhardys_db");
define("DB_ADMIN", "suhardys");

define("MAXIMUM_EMAIL_LENGTH", 255);
define("MINIMUM_PASS_LENGTH", 3);
define("RECORDS_PER_PAGE", 10);
define("MAXIMUM_FILE_SIZE", 2000000);
?>