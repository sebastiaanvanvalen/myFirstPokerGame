<?php

// create Players and PlayerStyles
// create Deck with 52 Cards
// determine blinds position
// 
// require_once dirname(dirname(dirname(__FILE__))) . '/vendor/autoload.php';

use application\common\Deck;
use application\common\Player;
use application\common\Wallet;

$Deck = new Deck();
// at this point the Deck is shuffled

// these variables will be passed as player input via main.php
$NOP         = 10;
$moneyAmount = 1800;
$players     = array();
$output      = array();

$noc    = [];
$noc_2  = [0, 5];
$noc_3  = [0, 3, 6];
$noc_4  = [0, 2, 5, 7];
$noc_5  = [0, 2, 4, 6, 8];
$noc_6  = [0, 1, 3, 5, 7, 9];
$noc_7  = [0, 1, 2, 4, 5, 7, 8];
$noc_8  = [0, 1, 2, 4, 5, 6, 7, 9];
$noc_9  = [0, 1, 2, 3, 4, 6, 7, 8, 9];
$noc_10 = [0, 1, 2, 3, 4, 5, 6, 7, 8, 9];

$noc = ${"noc_" . $NOP};

// set Blinds
$BB = rand(0, $NOP - 1);
if($BB === 0){
   $SB = $NOP - 1;
} else {
   $SB = $BB - 1;
}

for($x=0; $x<$NOP; $x++){
   $chair = $noc[$x];
   $player = new Player($x, $chair);
   $player->setCards($Deck->getHand());
   $player->createWallet(new Wallet($player->id, $moneyAmount));

   if($x === $BB){
      $player->setBlind("BB");
   }elseif($x === $SB){
      $player->setBlind("SB");
   } else {
      $player->setBlind("NB");
   };



   $players[] = $player;

   if($player->status === 1){
      if($player->id === 0){
         $output[] = array($player->id, $player->chair, $player->blindType, $player->walletContent, $player->card_1, $player->card_2);
      } else {
         $output[] = array($player->id, $player->chair, $player->blindType, $player->walletContent);
      }
   }
}
      // echo "\n-----(start vardump)\n";
      // var_dump($output);
      // echo "\n-----(end vardump)\n";

echo json_encode(array($output));

