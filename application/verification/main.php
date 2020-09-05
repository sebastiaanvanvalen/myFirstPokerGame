<?php

$reqHeaders = apache_request_headers();

switch ($reqHeaders){
    case "newGame":
      $clientName = $reqHeaders['name'];
      $NOP = $reqHeaders['n_o_p'];
      include "../gameSetup/setup.php";

        break;
    case "":

        break;
    case "":

        break;
    default:
        throw new Exception("no valid casses");
}