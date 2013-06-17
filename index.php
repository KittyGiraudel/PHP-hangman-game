<?php

// Require Hangman
require_once('Hangman.php');

// Get session
session_start();

// If no game initialized yet, do it
if(!isset($_SESSION['hangman'])) {
	$_SESSION['hangman'] = new Hangman(10, 7); // Number of games / number of tries
	$_SESSION['hangman']->new_game();
}

?>
<!doctype html>
<html lang="fr">
	<head>
		<meta charset="utf-8">
		<title>Hangman</title>
		<link rel="stylesheet" type="text/css" href="css/styles.css">
	</head>
	<body>
	<?php
		echo $_SESSION['hangman']->display_score(); // Score
		echo $_SESSION['hangman']->display_reset(); // Reset
		echo $_SESSION['hangman']->display_current(); // Word
		echo $_SESSION['hangman']->display_tries(); // Tries
		echo $_SESSION['hangman']->display_alphabet(); // Keyboard
	?>
	<script src="js/main.js"></script>
	</body>
</html>