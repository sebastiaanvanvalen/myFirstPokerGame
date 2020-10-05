<?php
// page extends from main.php

use application\common\Player;
use application\common\Wallet;

// match playerId with Players
$output      = array();

$players = unserialize($_SESSION['PLAYERS']);

      // echo "\n-----(start vardump)\n";
      // var_dump($players);
      // echo "\n-----(end vardump)\n";
foreach($players as $player){

   if($playerId === $player['player_id']){
      $client = unserialize($_SESSION['player_'.$playerId]);
      $details = array($client->playerId, $client->playerName, $client->wallet, $client->blindType, $client->card_1, $client->card_2);

      array_push($output, $details);
   } else {
      $client = unserialize($_SESSION['player_'.$player['player_id']]);
      $details = array($client->playerId, $client->playerName, $client->wallet, $client->blindType);

      array_push($output, $details);
   }
}

array_push($output);


echo json_encode($output);
