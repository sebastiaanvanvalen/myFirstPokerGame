<?php
// page extends from main.php

$stmt   = $conn->prepare((\file_get_contents(dirname(__FILE__) . '/sql/saveChat.sql')));
$stmt->bindParam(":player_id", $playerId);
$stmt->bindParam(":player_name", $playerName);
$stmt->bindParam(":msg", $chatMessage);
$stmt->execute();




$ch = curl_init('http://localhost:8080');

curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");

$jsonData = json_encode([
    'dataType'    => 'chatMessage',
    'playerId'    => $playerId,
    'chatMessage' => $chatMessage,
    'playerName'  => $playerName
]);

$query = http_build_query(['data' => $jsonData]);
curl_setopt($ch, CURLOPT_POSTFIELDS, $query);

curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

$response = curl_exec($ch);
curl_close($ch);