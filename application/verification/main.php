<?php
session_start();
require dirname(dirname(dirname(__FILE__))) . '/vendor/autoload.php';

use application\config\Config;
use application\verification\tokens\TokenHandler;


if($_SERVER['REQUEST_METHOD'] === "GET"){
    $reqHeaders = apache_request_headers();
};

if($_SERVER['REQUEST_METHOD'] === "POST"){
    $reqHeaders = $_POST;
};


$csrf_token    = filter_var($reqHeaders['csrf_token'], FILTER_SANITIZE_STRING);
$tokenType     = filter_var($reqHeaders['tokenType'], FILTER_SANITIZE_STRING);
$dataType     = filter_var($reqHeaders['dataType'], FILTER_SANITIZE_STRING);
$playerId;
$chatMessage;

if(isset($reqHeaders['playerId'])){
    $playerId    = filter_var($reqHeaders['playerId'], FILTER_SANITIZE_NUMBER_INT);
};

if(isset($reqHeaders['playerName'])){
    $playerName    = filter_var($reqHeaders['playerName'], FILTER_SANITIZE_STRING);
};

if(isset($reqHeaders['playerAge'])){
    $playerAge     = filter_var($reqHeaders['playerAge'], FILTER_SANITIZE_NUMBER_INT);
};

if(isset($reqHeaders['chatMessage'])){
    $chatMessage   = filter_var($reqHeaders['chatMessage'], FILTER_SANITIZE_SPECIAL_CHARS);
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
        include "../gameSetup/addPlayerToGame.php";

        break;
    case "checkTableStatus":
        include "../gameSetup/setup.php";

        break;
    case "startGame":
        include "../gameSetup/startSign.php";

        break;
    case "chatMessage":
        include "../chat/handleChat.php";

        break;
    case "startPlaying":
        include "../gameSetup/setup.php";

        break;
    case "chatMessage":
        include "";

        break;
    default:
        throw new Exception("no valid cases");
}
