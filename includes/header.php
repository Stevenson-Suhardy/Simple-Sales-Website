<!--
Stevenson Suhardy
September 16, 2022
Webd3201
-->
<!doctype html>
<html lang="en">
    <?php
        if (session_id() == "") {
            session_start();
        }
        ob_start();
        require("./includes/constants.php");
        require("./includes/db.php");
        require("./includes/functions.php");
        //$message = isset($_SESSION['message'])?$_SESSION['message']:"";
        if (isset($_SESSION['message'])) {
            $message = $_SESSION['message'];
        }
        else {
            $message = "";
        }
        $message = flashMessage();
    ?>
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="">
    <link rel="icon" href="/docs/4.0/assets/img/favicons/favicon.ico">

    <title><?php echo $title; ?></title>

    <!-- Bootstrap core CSS -->
    <link href="./css/bootstrap.min.css" rel="stylesheet">

    <!-- Custom styles for this template -->
    <link href="./css/styles.css" rel="stylesheet">
	
    </head>
    <body>
    <?php if (isLoggedIn()): ?>
            <nav class="navbar navbar-dark sticky-top bg-dark flex-md-nowrap p-0">
            <a class="navbar-brand col-sm-3 col-md-2 mr-0" href="./index.php">Titan Link</a>
            <ul class="navbar-nav px-3">
            <li class="nav-item text-nowrap">
                <a class="nav-link" href="./sign-out.php">Sign out</a>
            </li>
            </ul>
        </nav>
        <div class="container-fluid" />
            <div class="row" />
            <nav class="col-md-2 d-none d-md-block bg-light sidebar">
                <div class="sidebar-sticky" />
                <ul class="nav flex-column">
                    <li class="nav-item">
                    <a class="nav-link<?php if (isCurrentPage("dashboard.php")): ?> active<?php endif; ?>" href="./dashboard.php">
                        <span data-feather="home"></span>
                        Dashboard
                        <?php if (isCurrentPage("dashboard.php")): ?>
                        <span class="sr-only">(current)</span>
                        <?php endif; ?>
                    </a>
                    </li>
                    <?php if (isAdmin()): ?>
                    <li class="nav-item">
                    <a class="nav-link<?php if (isCurrentPage("salespeople.php")): ?> active<?php endif; ?>" href="./salespeople.php">
                        <span data-feather="home"></span>
                        Sales People
                        <?php if (isCurrentPage("salespeople.php")): ?>
                        <span class="sr-only">(current)</span>
                        <?php endif; ?>
                    </a>
                    </li>
                    <?php endif; ?>
                    <li class="nav-item">
                    <a class="nav-link<?php if (isCurrentPage("clients.php")): ?> active<?php endif; ?>" href="./clients.php">
                        <span data-feather="home"></span>
                        Clients
                        <?php if (isCurrentPage("clients.php")): ?>
                        <span class="sr-only">(current)</span>
                        <?php endif; ?>
                    </a>
                    </li>
                    <?php if (isSalesperson()): ?>
                    <li class="nav-item">
                    <a class="nav-link<?php if (isCurrentPage("calls.php")): ?> active<?php endif; ?>" href="./calls.php">
                        <span data-feather="home"></span>
                        Calls
                        <?php if (isCurrentPage("calls.php")): ?>
                        <span class="sr-only">(current)</span>
                        <?php endif; ?>
                    </a>
                    </li>
                    <?php endif; ?>
                    <li class="nav-item">
                    <a class="nav-link<?php if (isCurrentPage("change-password.php")): ?> active<?php endif; ?>" href="./change-password.php">
                        <span data-feather="home"></span>
                        Change Password
                        <?php if (isCurrentPage("change-password.php")): ?>
                        <span class="sr-only">(current)</span>
                        <?php endif; ?>
                    </a>
                    </li>
                </ul>
            </nav>
                <main class="col-md-9 ml-sm-auto col-lg-10 pt-3 px-4" />
                    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pb-2 mb-3 border-bottom" />
    <?php endif; ?>