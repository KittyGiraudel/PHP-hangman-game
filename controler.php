<?php

// Require Hangman
require_once('Hangman.php');

// Get session
session_start();

// If isset letter, check it
if(isset($_GET['l'])) 
	$_SESSION['hangman']->check_letter($_GET['l']);


// TEST @TO REMOVE
if(isset($_GET['destroy']))
	session_destroy();

header('location:/hangman/');
?>