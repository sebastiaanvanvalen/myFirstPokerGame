<?php

namespace application\common;

class Wallet{
   private $owner;
   private $content;
   private $transContent;
   private $bet;

   public function __construct($owner, $content){
      $this->owner        = $owner;
      $this->content      = $content;
      $this->transContent = $content;
      $this->bet          = 0;
   }

   public function getContent(){
      return $this->content;
   }

   public function placeBigBlind(){
      

   }

   public function placeSmallBlind(){

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
}