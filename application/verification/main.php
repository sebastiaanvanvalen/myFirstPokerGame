<?php
session_start();
require dirname(dirname(dirname(__FILE__))) . '/vendor/autoload.php';

use application\common\PlayerRound;
use application\config\Config;
use application\verification\tokens\TokenHandler;


if ($_SERVER['REQUEST_METHOD'] === "GET") {
    $reqHeaders = apache_request_headers();
};

if ($_SERVER['REQUEST_METHOD'] === "POST") {
    $reqHeaders = $_POST;
};




$csrf_token    = filter_var($reqHeaders['csrf_token'], FILTER_SANITIZE_STRING);
$tokenType     = filter_var($reqHeaders['tokenType'], FILTER_SANITIZE_STRING);
$dataType      = filter_var($reqHeaders['dataType'], FILTER_SANITIZE_STRING);
$playerId;
$chatMessage;

if (isset($reqHeaders['playerId'])) {
    $playerId    = filter_var($reqHeaders['playerId'], FILTER_SANITIZE_NUMBER_INT);
};

if (isset($reqHeaders['playerName'])) {
    $playerName    = filter_var($reqHeaders['playerName'], FILTER_SANITIZE_STRING);
};

if (isset($reqHeaders['playerAge'])) {
    $playerAge     = filter_var($reqHeaders['playerAge'], FILTER_SANITIZE_NUMBER_INT);
};

if (isset($reqHeaders['chatMessage'])) {
    $chatMessage   = filter_var($reqHeaders['chatMessage'], FILTER_SANITIZE_SPECIAL_CHARS);
};

if (isset($reqHeaders['playerChoice'])) {
    $playerChoice   = filter_var($reqHeaders['playerChoice'], FILTER_SANITIZE_STRING);
};

if (isset($reqHeaders['playerBet'])) {
    $playerBet   = filter_var($reqHeaders['playerBet'], FILTER_SANITIZE_NUMBER_INT);
};


// constant variables that will be set by Users in later versions of this game
$walletContent = 2200;
$tableType = "act_table_1";
$nop = 3;

$Config        = new Config();
$token         = new TokenHandler($tokenType, $csrf_token);
$conn          = $Config->getConnection("baxxie", "admin");


if ($token->handle() !== "valid") {
    $response = array('response' => 'invalid');
    print_r($response);
    exit;
}

switch ($dataType) {
    case "addPlayerToGame":
        // comes from all joining players.
        // public/scripts/index.js
        include "../gameSetup/addPlayerToGame.php";

        break;
    case "startGame":
        // comming from the last joining player.
        // public/scripts/act_table1.js
        include "../gameSetup/startNewTable.php";

        break;
    case "newRound":
        // comes from all players after the games start of any other change on the table like when a player makes a choice
        // public/scripts/node.js
        include "../gameProgress/startNextRound.php";
        // include "../gameProgress/setupNewRound.php";

        break;
    case "chatMessage":
        // comes from active player that submitted a chatMessage.
        // public/scripts/node.js
        include "../chat/handleChat.php";

        break;
    case "sendChoice":
        // send from the player who made a choice (bet, fold, etc)
        // public/scripts/node.js
        include "../gameProgress/processChoice.php";

        break;
    case "updateTable":
        // comes from all players after the games start or any other change on the table like when a player makes a choice
        // public/scripts/node.js
        include "../gameSetup/updateTable.php";

        break;
    // case "evaluateHands":
    //     include "../gameProgress/evaluateHands.php";

    //     break;
    // case "getCards":
    //     include "../gameProgress/getCards.php";

    //     break;
    default:
        throw new Exception("no valid cases");
}
