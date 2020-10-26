<?php
session_start();
require dirname(dirname(__FILE__)) . '/vendor/autoload.php';

use application\verification\tokens\ChatToken;
use application\verification\tokens\LoginToken;
use application\verification\tokens\UserToken;

$newUserToken = new UserToken();
$userToken    = $newUserToken->create();

$newChatToken = new ChatToken();
$chatToken    = $newChatToken->create();

$newCsrfToken = new LoginToken();
$csrfToken    = $newCsrfToken->create();

$playerId     = $_GET['id'];
$playerName   = $_GET['name'];
$playerAge    = $_GET['age']; 
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

   <title>Welcome</title>
</head>

<body>
   <div class="game_container">
      <div class="opening_text"></div>

      <div class="chat_container">
         <div id="connecting">Connecting to server...</div>
         <div class="msg_list"></div>
         <div class="input_container">
            <input type="text" class="player_message" id="player_message"  minlength="1" maxlength="255" placeholder="talk...">
            <button class="btn btn-info submit_message">submit</button>
         </div>
      </div>

      <div class="menu_container">
         <button class="btn btn-info info_btn">Info</button>
      </div>
      <div class="table_container">
         <!-- table img-->
      </div>

      <div class="chair_container"></div>

      <div class="table_pot"></div>

      <div class="card_container"></div>

      <div class="modal_container">
         <div class="text_container"></div>

         <div class="yes_no_container">
            <button class="btn btn-success btn-small yes_btn">yes</button>
            <button class="btn btn-danger btn-small no_btn">no</button>
         </div>
         <div class="okay_container">
            <button class="btn btn-info btn-small confirm_btn">okay!</button>
         </div>

      </div>

      <div class="options_container">
         <button class="btn btn-secondary check_btn">Check</button>
         <button class="btn btn-secondary call_btn">Call</button>
         <button class="btn btn-secondary fold_btn">Fold</button>
         <button class="btn btn-secondary raise_btn">Bet / Raise</button>
         <button class="btn btn-secondary all_in_btn">All-In</button>
      </div>

      <div class="raise_container">
         <div class="raise_text">Your bet: €</div>
         <button class="min_btn r_b">&darr;</button>
         <input type="number" class="raise_input" min="" max="" step="10" value="0">
         <button class="plus_btn r_b">&uarr;</button>
      </div>

      <input type="hidden" name="csrf_token"  class="csrf_token" value="<?php echo $csrfToken; ?>" />
      <input type="hidden" name="chat_token"  class="chat_token" value="<?php echo $chatToken; ?>" />
      <input type="hidden" name="user_token"  class="user_token" value="<?php echo $userToken; ?>" />
      <input type="hidden" name="player_id"   class="player_id" value="<?php echo $playerId; ?>" />
      <input type="hidden" name="player_name" class="player_name" value="<?php echo $playerName; ?>" />
      <input type="hidden" name="player_age"  class="player_age" value="<?php echo $playerAge; ?>" />
      <input type="hidden" name="start_game"  class="start_game" value="<?php echo $startGame; ?>" />

   </div>

   <!-- <script src="./scripts/act_table_1.js"></script> -->
   <script src="./scripts/node.js"></script>
</body>

</html>