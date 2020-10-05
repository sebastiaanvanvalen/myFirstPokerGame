<?php

namespace application\common;

class Player{
   public $playerId;
   public $playerName;
   public $blindType;
   public $wallet;
   public $walletContent;
   public $card_1;
   public $card_2;
   public $status;

   public function __construct($playerId, $playerName){
      $this->playerId     = $playerId;
      $this->playerName = $playerName;
      $this->status = 1;
   }

   public function setPlayer(){
      // 
   }

   public function setCards($cards){
      $this->card_1 = $cards[0];
      $this->card_2 = $cards[1];
   }

   public function setBlind($blindType){
      $this->blindType = $blindType;
      if ($blindType === "BB"){
         $this->wallet = $this->wallet - 20;
      }
      if ($blindType === "SB"){
         $this->wallet = $this->wallet - 10;
      }
   }

   public function createWallet($wallet){
      $this->wallet = $wallet;
   }

   public function bet($bet){
      $this->bet          = $this->bet + $bet;
      $this->transContent = $this->transContent - $bet;

      return array($this->transContent, $bet);
   }

   public function lose($bet){
      $this->content      = $this->content - $bet;
      $this->transContent = $this->content;
      $this->bet          = 0;
      
      return $this->content;
   }
   
   public function win($win){
      $this->content      = $this->content + $win;
      $this->transContent = $this->content;
      $this->bet          = 0;

      return $this->content;
   }

   public function getPlayerStatus(){
      return $this->status;
   }

   public function deactivatePlayer(){
      $this->status = 0;
   }

   public function handleChoice(){
      // create record in DB
      // send message through socket to all players
   }

   /* PRIVATE */

   private function createPlayerType(){
      // player characteristics... gonna be fun
   }

}