<?php

class Hangman {

	// Constants
	private $controler  = "controler.php";
	private $APIurl     = "http://api.wordnik.com/v4/words.json/randomWords?hasDictionaryDef=false&minCorpusCount=0&maxCorpusCount=-1&minDictionaryCount=1&maxDictionaryCount=-1&minLength=4&maxLength=-1&limit=";
	private $APIkey     = "c2e6bbf2be425a4b4d30201da7906023ae280054e4b48876a";
	private $alphabet   = array();
	
	// Session-related	
	private $maxTries   = 5;
	private $victories  = 0;
	private $gamesDone  = 0;
	private $words      = null;

	// Game-related
	private $word       = null;
	private $current    = null;
	private $tried      = null;
	private $tries      = 0;		

	// Constructor
	// $games = number of games
	// $max   = number of tries for a word
	public function __construct($games = 20, $max = 5) {

		// Get words from Wordnik API
		$words = $this->get_data($this->APIurl.$games, array('api_key: '.$this->APIkey));
		
		// Set variables
		$this->maxTries = $max;
		$this->words    = json_decode($words);

		// Fill the alphabet
		for($i = 65; $i < 91; $i++){
		    $this->alphabet[] = chr($i); }
			$this->alphabet[] = "-";
	}

	// Create a new word
	private function new_word() {
		// A whole new woooooord...
		$word = $this->words[$this->gamesDone]->word;

		// Array all the things!
		$this->word    = array();
		$this->current = array();
		$this->tried   = array();	
		
		// Fill the arrays
		for($i = 0, $len = strlen($word); $i < $len; $i++) {
			$this->word[]    = strtoupper($word[$i]);
			$this->current[] = '_';
		}

		// Show first letter
		$this->current[0] = $this->word[0];
	}

	// Enable a new game session
	public function new_game() {
		$this->new_word();  // Pick new word
		$this->gamesDone++; // Increment number of games
		$this->tries = 0;   // Reset number of tries
	}

	// Check letter and do what needs to be done
	// $letter = letter to check
	public function check_letter($letter) {

		// If not an allowed character, break
		if(!in_array($letter, $this->alphabet))
			return false;
		
		else {
			// If not tried yet, push it
			if(!in_array($letter, $this->tried)) {
				$this->tried[] = $letter;

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
		
		// Compare word and guess
		$done = $this->word === $this->current;

		// If word guessed
		if($done) {
			$this->victories++; // Increment victories
			$this->new_game();  // Launch new game
		}

		// If 0 try left
		if($this->tries >= $this->maxTries)
			$this->new_game();  // Launch new game

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
		$dump .= $this->gamesDone;
		$dump .= " - ";
		$dump .= count($this->words) - $this->gamesDone;
		$dump .= " games left.";
		$dump .= "</p>";
		return $dump;
	}

	// Display the current tries
	public function display_tries() {
		$dump  = "<p class='tries'>";
		$dump .= "<span class='try try-".$this->get_remaining_tries()."'>";
		$dump .= $this->get_remaining_tries();
		$dump .= "</span>";
		$dump .= " tries remaining.";
		$dump .= "</p>";
		return $dump;
	}

	// Display reset button
	public function display_reset() { 
		return "<a class='restart' href='".$this->controler."?destroy=true'>Restart</a>";
	}

	// Make a CURL
	private function get_data($url, $headers = null) {
		$ch = curl_init();
		$timeout = 5;
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
		if($headers !== null) curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
		$data = curl_exec($ch);
		curl_close($ch);
		return $data;
	}
}