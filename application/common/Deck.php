<?php

namespace application\common;

class Deck
{
	private $nextCard   = 0; # protected
	private $cards      = array(); # protected
	private $tableCards = array();

	public function __construct()
	{
		foreach (range(0, 51) as $card) {
			array_push($this->cards, new Card($card));
		}
	}

	public function next()
	{
		if (!isset($this->cards[$this->nextCard])) {
			return null;
		}
		$card = $this->cards[$this->nextCard++];
		array_push($this->tableCards, $card);
		
		return $card;
	}

	public function size()
	{
		return (count($this->cards) - $this->nextCard);
	}

	public function add_deck(Deck $objDeck)
	{
		$this->cards = array_merge($this->cards, $objDeck->cards);
		return $this;
	}

	public function add_card(Card $objCard)
	{
		array_push($this->cards, $objCard);
		return $this;
	}

	public function shuffle()

	{
		shuffle($this->cards);
		return shuffle($this->cards);
	}

	public function replenish()
	{
		$this->iNextCard = 0;
		$this->shuffle();
	}

	public function __tostring()
	{
		return implode("\n", $this->cards);
	}
}





// class Deck{
//    private $suits;
// 	private $deck;
// 	private $tableCards = array();

//    public function __construct(){
// 		$this->values = array('2', '3', '4', '5', '6', '7', '8', '9', '10', 'J', 'Q', 'K', 'A');
// 		$this->suits  = array('spades', 'hearts', 'diamonds', 'clubs');
// 		$this->createDeck();
//    }

// 	public function getHand(){
// 		// $output = array(array_slice($this->deck, 0, 2));
// 		$output = array();
// 		$output[] = array_shift($this->deck);
// 		$output[] = array_shift($this->deck);
// 		return $output;
// 	}

// 	public function getFlop(){
// 		$output = array();
// 		$output[] = array_shift($this->deck);
// 		array_push($this->tableCards, $output[0]);
// 		$output[] = array_shift($this->deck);
// 		array_push($this->tableCards, $output[1]);
// 		$output[] = array_shift($this->deck);
// 		array_push($this->tableCards, $output[2]);
// 		return $output;
// 	}
	
// 	public function getRiver(){
// 		$output = array();
// 		$output[] = array_shift($this->deck);
// 		array_push($this->tableCards, $output[0]);
// 		return $output;
// 	}
	
// 	public function getTurn(){
// 		$output = array();
// 		$output[] = array_shift($this->deck);
// 		array_push($this->tableCards, $output[0]);
// 		return $output;
// 	}

// 	public function getTableCards(){
// 		return $this->tableCards;
// 	}

// 	/* PRIVATE */

// 	private function createDeck(){
// 		$this->deck = array();
// 		foreach ($this->suits as $suit) {
// 			foreach ($this->values as $value) {
// 				$this->deck[] = array("pth" => $value, "suit" =>  $suit);
// 			}
// 		}
// 		return shuffle($this->deck);
// 	}

// }
