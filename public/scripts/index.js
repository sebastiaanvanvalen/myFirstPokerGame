$(document).ready(function(){


   $(".submit").on('click', function(){
      event.preventDefault()
      let csrfToken = $(".csrf_token").val();
      let playerName = $(".player_name").val();
      let playerAge  = $(".player_age").val();

      $.ajax({
         type: 'get',
         url: '/application/main',
         headers: {
            "csrf_token" : csrfToken,
            "tokenType"  : "loginToken",
            "dataType"  :  "addPlayerToGame",
            "playerName" : playerName,
            "playerAge"  : playerAge
         },
         async: true,
     }).done(function (datas) {
        console.log(datas);
        data = JSON.parse(datas);
         console.log(data);
         if(data.response === "full"){
            console.log("the table is currently full. Please try again later")
         } else {
            // main.php needs to encrypt clientId and then check it at the table. + more
            window.location.href = "../act_table_1.php?id=" + data.playerId + "&name=" +playerName + " (" + playerAge +")&startGame=" + data.startGame;
            // window.location.href = "../act_table_1.php?id=" + data + "&name=" +playerName + " (" + playerAge +")&startGame=";

         }
     }).fail(function (data) {
         alert("AJAX request failed");
         console.log(data);
     });
   })


})

