<?php

namespace application\player;

class Player{
   private $card_1;
   private $card_2;
   private $playerType;

   public function __construct($name, $wallet, $position){
      $this->name     = $name;
      $this->wallet   = $wallet;
      $this->position = $position;
   }

   public function setPlayer($card_1, $card_2){
      $this->card_1 = $card_1;
      $this->card_2 = $card_2;
      $this->playerType = $this->createPlayerType();
   }

   public function getCards(){
      return array($this->card_1, $this->card_2);
   }

   public function makeChoice(){
      // argument are combined with playerType to produce a choice (bet, fold, check or raise)
   }

   /* PRIVATE */

   private function createPlayerType(){
      // player characteristics... gonna be fun
   }

}