<?php
session_start();
require dirname(dirname(__FILE__)) . '/vendor/autoload.php';

use application\verification\tokens\ChatToken;
use application\verification\tokens\LoginToken;

$newChatToken = new ChatToken();
$chatToken    = $newChatToken->create();

$newCsrfToken = new LoginToken();
$csrfToken    = $newCsrfToken->create();
$playerId     = $_GET['id'];
// echo $playerId . "\n";
$playerName   = $_GET['name'];
// echo $playerName . "\n";
$startGame    = $_GET['startGame'];

// $hash = new Hash;
// $check = hash->checkHash(clientId);
// if($check == false){

//    exit;
// }
// + more
?>


<!DOCTYPE html>
<html lang="en">

<head>
   <meta charset="UTF-8">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <link rel="stylesheet" href="./styles/act_table_1.css">
   <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>

   <title>Poker with friends</title>
</head>

<body>
   <div class="game_container">
      <div class="opening_text"></div>

      <div class="chat_container">
         <div class="open_close_btn">V</div>
         <div id="connecting">Connecting to server...</div>
         <div class="msg_list"></div>
         <div class="input_container">
            <input type="text" class="player_message" id="player_message" placeholder="Say it!" onkeyup="handleKeyUp(event)">
            <button class="btn btn-small btn-secundary submit_message">submit</button>
         </div>
      </div>

      <div class="menu_container">
         <button class="btn new_game_btn">New Game</button>
         <button class="btn info_btn">Info</button>
         <label for="NOP">Number of players</label>
         <input type="number" class="btn NOP" min="1" max="7" value="5">
         <button class="btn submit_NOP_btn">Submit</button>
      </div>
      <div class="table_container">
         <!-- table img-->
      </div>

      <div class="table_pot">€xxxx</div>

      <div class="card_container">
         <div class="flop_container">
            <div class="card flop_card c_1">card 1</div>
            <div class="card flop_card c_2">card 2</div>
            <div class="card flop_card c_3">card 3</div>
         </div>
         <div class="river_container">
            <div class="card river_card c_4">card 4</div>
         </div>
         <div class="turn_container">
            <div class="card turn_card c_5">card 5</div>

         </div>
      </div>

      <div class="option_container">
         <button class="opt check_btn">CHECK</button>
         <button class="opt call_btn">CALL</button>
         <button class="opt fold_btn">FOLD</button>
         <button class="opt bet_btn">BET</buton>
            <button class="opt raise_btn">RAISE</button>
      </div>

      <input type="hidden" name="csrf_token" class="csrf_token" value="<?php echo $csrfToken; ?>" />
      <input type="hidden" name="chat_token" class="chat_token" value="<?php echo $chatToken; ?>" />
      <input type="hidden" name="player_id" class="player_id" value="<?php echo $playerId; ?>" />
      <input type="hidden" name="player_name" class="player_name" value="<?php echo $playerName; ?>" />
      <input type="hidden" name="start_game" class="start_game" value="<?php echo $startGame; ?>" />


   </div>

   <script src="./scripts/act_table_1.js"></script>
   <script src="./scripts/node.js"></script>
</body>

</html>