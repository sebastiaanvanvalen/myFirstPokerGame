<?php

namespace application\common;

class Deck{
   private $suits;
   private $cards;
   private $deck;

   public function __construct(){
		$this->values = array('2', '3', '4', '5', '6', '7', '8', '9', '10', 'J', 'Q', 'K', 'A');
		$this->suits  = array('S', 'H', 'D', 'C');
		$this->createDeck();
   }

	public function getHand(){
		// $output = array(array_slice($this->deck, 0, 2));
		$output =array();
		$output[] = array_shift($this->deck);
		$output[] = array_shift($this->deck);
		return $output;
	}

	public function getFlop(){

	}

	public function getRiver(){

	}

	public function getTurn(){

	}

	/* PRIVATE */

	private function createDeck(){
		$this->deck = array();
		foreach ($this->suits as $suit) {
			foreach ($this->values as $value) {
				$this->deck[] = $value . $suit;
			}
		}
		return shuffle($this->deck);
	}

}