<?php
// page extends from main.php

// check database for number of players on act_table
// if not full -> add player -> response "wait" or "startGame"
// if full -> to bad...

use application\common\Player;

// TODO Build system to check for players that are logging in at the same time;

// check number of players that were added to the active table in the DB
$conn   = $Config->getConnection("baxxie", "admin");
$stmt   = $conn->prepare((\file_get_contents(dirname(__FILE__) . '/sql/checkPlayerCount.sql')));
$stmt->execute();
$result = $stmt->fetch();



// when table has more than 3 players, the new client gets a message the table is full.
if ($result[0] > 3) {
   $response = array('response' => 'full');
   echo json_encode($response);

   exit;
}

$tempId = mt_rand();
$output;

if ($result[0] === "0" || $result[0] === "1" || $result[0] === "2" || $result[0] === '3') {
   // ! chair should be reserved first... But we need a player_id for the player to take a chair

   // add player to DB
   $stmt01   = $conn->prepare((\file_get_contents(dirname(__FILE__) . '/sql/createPlayer.sql')));
   $stmt01->bindParam(":temp_id", $tempId);
   $stmt01->bindParam(":player_name", $playerName);
   $stmt01->bindParam(":player_age", $playerAge);
   $stmt01->bindParam(":wallet_content", $walletContent);
   $stmt01->execute();

   // retrieve player_id
   $stmt02   = $conn->prepare((\file_get_contents(dirname(__FILE__) . '/sql/getPlayerId.sql')));
   $stmt02->bindParam(":temp_id", $tempId);
   $stmt02->execute();
   $result02 = $stmt02->fetch();
   $newPlayerId = $result02['id'];

   // add player and id to table
   $stmt03   = $conn->prepare((\file_get_contents(dirname(__FILE__) . '/sql/addPlayerToTable.sql')));
   $stmt03->bindParam(":table_type", $tableType);
   $stmt03->bindParam(":nop", $nop);
   $stmt03->bindParam(":player_id", $newPlayerId);
   $stmt03->execute();
   $output =  array(
      'playerId'  => $newPlayerId,
      'startGame' => false);


   // when last player entered table:
   if ($result[0] === "3") {

      $stmt04     = $conn->prepare((\file_get_contents(dirname(__FILE__) . '/sql/getPlayerIds.sql')));
      $stmt04->execute();
      $stmt04->setFetchMode(PDO::FETCH_ASSOC);
      $results04  = $stmt04->fetchAll();

      $stmt05     = $conn->prepare((\file_get_contents(dirname(__FILE__) . '/sql/getPrevTableId.sql')));
      $stmt05->execute();
      $stmt05->setFetchMode(PDO::FETCH_ASSOC);
      $result05   = $stmt05->fetch();
      $newTableId = intval($result05['highestInactiveId']) + 1;

      $output = array(
         'playerId'  => $newPlayerId,
         'startGame' => true
      );

      $player = new Player($result[0], $newPlayerId);


      // make table record as an backup
      try {
         // ! add multiple rows to DB at once. But! How do we create prepared statements?

         $values = "";
         foreach ($results04 as $result) {
            $values .= "(" . $newTableId . ", " . $result['nop'] . ", " . $result['player_id'] . ", SYSDATE(), '501'), ";
         }

         $values = substr($values, 0, -2);
         $stmt06 = $conn->prepare("INSERT INTO table_records (table_id, nop, player_id, create_time, created_by) VALUES " . $values . "");
         $stmt06->execute();
      } catch (PDOException $e) {
         echo json_encode($e->getMessage() . "\n");
         echo json_encode($e->getLine()  . "\n");
      }
      // TODO create function to get the table started.
   }

   // echo id for index.html to add in the URL. Client has an own ID when joining the table





   //  client details are send to node.js and shared with all clients for "player had joined the table"-function in chat window
   $ch = curl_init('http://localhost:8080');
   curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
   $jsonData = json_encode([
      'dataType'       => 'loginMessage',
      'playerName'     => $playerName,
      'playerAge'      => $playerAge,
      'playerId'       => $newPlayerId
   ]);

   $query = http_build_query(['data' => $jsonData]);
   curl_setopt($ch, CURLOPT_POSTFIELDS, $query);
   curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
   $response = curl_exec($ch);
   curl_close($ch);
}



echo json_encode($output);
