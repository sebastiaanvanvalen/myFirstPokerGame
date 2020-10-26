<?php
// extends main.php
use application\common\Deck;
use application\common\SocketMsg;
use application\common\Table;

/*
* Sebastiaan
* date: "2020-10-04
* comment: this scripts is instantiated after the last player joined the table. From here the node server is instructed to signal all players the table is full and the game is ready to start. From there all players request the server to get cards / be a player object etc
*/

// default settings:


$smallBlind  = 5;
$bigBlind    = 10;
$ante        = 0;
$timer       = "60";
$moneyAmount = 100;

$table = new Table($smallBlind, $bigBlind, $ante, $timer);
$deck        = new Deck();
$socketMsg   = new SocketMsg();
$deck->shuffle();


$stmt   = $conn->prepare((\file_get_contents(dirname(__FILE__) . '/sql/getActivePlayers.sql')));
$stmt->bindParam(":temp_id", $tempId);
$stmt->execute();
$results     = $stmt->fetchAll();

foreach ($results as $result) {

   $cards = array();
   $card1 = $deck->next();
   $card2 = $deck->next();
   array_push($cards, $card1);
   array_push($cards, $card2);
   $table->addPlayer($result['player_id'], $result['player_name'], $result['player_age'], $moneyAmount, $cards);

}
$table->setPositions();
$_SESSION['TABLE'] = serialize($table);
$_SESSION['DECK'] = serialize($deck);

$data = array(
   'dataType' => 'nextPlayer'
);

$socketMsg->sendData($data);
