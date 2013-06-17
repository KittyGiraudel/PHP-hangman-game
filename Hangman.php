<?php

class Hangman {

	// Constants
	private $controler  = "controler.php";
	private $dictionary = "dictionary.txt";
	private $alphabet   = array('a', 'b', 'c', 'd', 'e', 'f', 'g', 'h', 'i', 'j', 'k', 'l', 'm', 'n', 'o', 'p', 'q', 'r', 's', 't', 'u', 'v', 'w', 'x', 'y', 'z', '-');
	private $nbWords    = 0;
	
	// Session-related	
	private $maxTries   = 5;
	private $victories  = 0;
	private $games      = 0;

	// Game-related
	private $word       = null;
	private $current    = null;
	private $tried      = null;
	private $tries      = 0;		

	// Constructor
	// $max = number of tries
	public function __construct($max = 5) {
		$this->maxTries = $max;
		$this->nbWords  = intval(exec('wc -l ' . $this->dictionary));  // Number of lines
	}

	// Get a new word from file
	private function get_word() {
		$file   = file($this->dictionary);      			// Open file
		$picked = rand(0, $this->nbWords);          // Pick a line
		$word   = trim(strtolower($file[$picked])); // Get the word
		return $word;
	}

	// Create a new word
	private function new_word() {
		$word = $this->get_word(); // Pick new word

		// Array all the things!
		$this->word    = array();
		$this->current = array();
		$this->tried   = array();	
		
		for($i = 0, $len = strlen($word); $i < $len; $i++) {
			// Fill the arrays
			array_push($this->word, $word[$i]);
			array_push($this->current, '_');
		}

		// Show first letter
		$this->current[0] = $this->word[0];

		return $word;
	}

	// Enable a new game session
	public function new_game() {
		$this->games++;    // Increment number of games
		$this->new_word(); // Pick new word
		$this->tries = 0;  // Reset number of tries
	}

	// Check letter and do what needs to be done
	// $letter = letter to check
	public function check_letter($letter) {

		// If not an allowed character, break
		if(!in_array($letter, $this->alphabet)) {
			return false;
		} else {
			// If not tried yet, push it
			if(!in_array($letter, $this->tried)) {
				array_push($this->tried, $letter);

				// If wrong guess, increment tries
				if(!in_array($letter, $this->word))
					$this->tries++;
			}

			// If well guessed
			if(in_array($letter, $this->word)) {
				// Update letters
				$index = array_keys($this->word, $letter);
				foreach($index as $i) {
					$this->current[$i] = $letter;
				}
			} 
		}

		// Check current game status
		$this->check_game();
	}

	// Check current game status
	private function check_game() {
		$done = $this->word === $this->current;

		// If word guessed
		if($done) {
			$this->victories++; // Increment victories
			$this->new_game();  // Launch new game
		}

		// If 0 try left
		if($this->tries >= $this->maxTries)
			$this->new_game(); // New game

		return $done;
	}

	// Get remaining tries
	public function get_remaining_tries() {
		return $this->maxTries - $this->tries;
	}

	// Display the keyboard
	public function display_alphabet() {
		$dump = "<ul class='alphabet'>";
		foreach($this->alphabet as $a) {
			$class = in_array($a, $this->tried) ? "tried" : "";
			$dump .= "<li class='".$class."'>";
			$dump .= "<a href='".$this->controler."?l=".$a."'>";
			$dump .= $a;
			$dump .= "</a></li>";
		}
		$dump .= "</ul>";
		return $dump;
	}

	// Display the current state as a string
	public function display_current() {
		$dump  = "<p class='current'>";
		foreach($this->current as $l) { $dump .= $l; }
		$dump .= "</p>";
		return $dump;
	}

	// Display the current score
	public function display_score() {
		$dump  = "<p class='score'>";
		$dump .= $this->victories;
		$dump .= " / ";
		$dump .= $this->games;
		$dump .= "</p>";
		return $dump;
	}

	// Display the current tries
	public function display_tries() {
		$dump  = "<p class='tries'>";
		$dump .= "Il reste ";
		$dump .= "<span class='try try-".$this->get_remaining_tries()."'>";
		$dump .= $this->get_remaining_tries();
		$dump .= "</span>";
		$dump .= " essais.";
		$dump .= "</p>";
		return $dump;
	}

	// Display reset button
	public function display_reset() { 
		return "<a class='restart' href='".$this->controler."?destroy=true'>Restart</a>";
	}
}

?>