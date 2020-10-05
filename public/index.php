<?php
session_start();
require dirname(dirname(__FILE__)) . '/vendor/autoload.php';

use application\verification\tokens\LoginToken;

$newCsrfToken = new LoginToken();
$csrfToken = $newCsrfToken->create();


?>

<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
   <link rel="stylesheet" href="./styles/index.css">
   <title>welcome to baxxie poker</title>
</head>
<body>

   <div class="login_container">
      <div class="opening_text">Welcome to this little poker game</div>
      <form class="login_form">

         <div class="form_container">
           <label for="player_name"><b>Username</b></label>
           <input type="text" class="player_name" placeholder="Enter name" name="player_name" required>
       
           <label for="player_age"><b>Age</b></label>
           <input type="number" class="player_age" placeholder="Enter Age" name="player_age" required>
           <input type="hidden" class="csrf_token" name="csrf_token" value="<?php echo $csrfToken; ?>">
       
           <button class="submit">Login</button>
         </div>
      
      </form>
   </div>

   <script src="./scripts/index.js"></script>
</body>
</html>