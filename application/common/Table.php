<?php

namespace application\common;

class Table{
   private $tableName;
   private $playerCount;
   private $players = array();

   public function __construct($tableName){
      $this->tableName   = $tableName;
      $this->playerCount = 0;
   }

   public function dealCards(){
      //
   }

   public function addPlayer($player){
      $this->playerCount ++;
      array_push($this->players, $player);
   }

   public function removePlayer($player){
      $this->playerCount --;
      array_slice($this->players, $player);
   }

   public function getPlayers(){
      return $this->players;
   }

}