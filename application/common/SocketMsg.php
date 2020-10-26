<?php

namespace application\common;

class SocketMsg
{
   private $URL;
   private $method;

   public function __construct()
   {
      $this->URL    = 'http://localhost:8080';
      $this->method = 'POST';
   }

   public function sendData($data)
   {
      $jsonData = json_encode($data);
      $ch = curl_init($this->URL);
      curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $this->method);

      $query = http_build_query(['data' => $jsonData]);
      curl_setopt($ch, CURLOPT_POSTFIELDS, $query);
      curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

      $response = curl_exec($ch);
      curl_close($ch);
   }
}
