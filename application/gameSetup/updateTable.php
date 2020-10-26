<?php
// page extends from main.php

/*
* Sebastiaan
* date: "2020-10-06
* comment: this script is called by every client to get the status of the game at the start of the game
*/

$table = unserialize($_SESSION['TABLE']);
$players     = $table->getPlayers();
$output      = array();

foreach ($players as $player) {
   //
   if ($playerId === $player->playerId) {
      array_push($output, $table->getOwnStatus($playerId));
   }
   if ($playerId !== $player->playerId) {
      array_push($output, $table->getCoplayersStatus($player->playerId));
   }
}

$_SESSION['TABLE'] = serialize($table);

//echo's back to public/scripts/node.js
echo json_encode($output);
