<?php
// extends main.php
use application\common\Deck;
use application\common\Player;
use application\common\Wallet;

/*
* Sebastiaan
* date: "2020-10-04
* comment: this scripts is instantiated after the last player joined the table. From here the node server is instructed to signal all players the table is full and the game is ready to start. From there all players request the server to get cards / be a player object etc
*/

$stmt   = $conn->prepare((\file_get_contents(dirname(__FILE__) . '/sql/getActivePlayers.sql')));
$stmt->bindParam(":temp_id", $tempId);
$stmt->execute();
$results = $stmt->fetchAll();

$NOP = count($results);

// create and shuffle deck
$deck = new Deck();
$moneyAmount = 1800;

// Store blinds in Session to manage blinds while creating playerObjects...
$BB = rand(0, $NOP - 1);
if ($BB === 0) {
   $SB = $NOP - 1;
} else {
   $SB = $BB - 1;
}

$playerNumber = 0;

foreach ($results as $result) {
   $player = new Player($result['player_id'], $result['player_name']);
   $player->setCards($deck->getHand());
   $player->createWallet($moneyAmount);

   if ($playerNumber === $BB) {
      $player->setBlind("BB");
   }
   if ($playerNumber === $SB) {
      $player->setBlind("SB");
   }

   $playerNumber++;
   $_SESSION['player_' . $result['player_id']] = serialize($player);

}

$_SESSION['deck']    = serialize($deck);
$_SESSION['NOP']     = serialize($NOP);
$_SESSION['PLAYERS'] = serialize($results);


// send startSign to all clients
$ch = curl_init('http://localhost:8080');
curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
$jsonData = json_encode([
   'dataType' => 'startSign'
]);

$query = http_build_query(['data' => $jsonData]);
curl_setopt($ch, CURLOPT_POSTFIELDS, $query);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

$response = curl_exec($ch);
curl_close($ch);
