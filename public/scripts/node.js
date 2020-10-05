$(document).ready(function () {

   let playerId = $(".player_id").val();
   let playerName = $(".player_name").val();
   let messages = [];


   // web sockets
   window.WebSocket = window.WebSocket || window.MozWebSocket;

   var connection = new WebSocket('ws://localhost:8080');
   var connectingSpan = document.getElementById("connecting");
   connection.onopen = function () {
      connectingSpan.style.display = "none";
   };
   connection.onerror = function (error) {
      connectingSpan.innerHTML = "Error occured";
   };

   connection.onmessage = function (message) {
      var data = JSON.parse(message.data);
      // console.log(data);
      let cssId;
      let msg;
      let player;

      if (playerId === data.playerId) {
         cssId = "this_user";
         player = "(me) : "
      } else {
         cssId = "other_user";
         player = data.playerName + ": "
      }

      switch (data.dataType) {
         case "loginMessage":
            msg = "<p class='chat_message " + cssId + "'>" + data.playerName + " (" + data.playerAge + ") joined the table</p>";
            $(".msg_list").append(msg);
            messages.push(msg);

            break;
         case "chatMessage":
            msg = "<p class='chat_message " + cssId + "'>" + player + data.chatMessage + "</p>";
            $(".msg_list").append(msg);
            messages.push(msg);

            break;
         case "startSign":

            $.ajax({
               type: 'get',
               url: '/application/main',
               headers: {
                  'csrf_token': $csrf_token = $(".csrf_token").val(),
                  'tokenType': 'loginToken',
                  'dataType': "startPlaying",
                  'playerId': playerId = $(".player_id").val()
               },
               async: true,
            }).done(function (datas) {
               console.log(datas);
               let data = JSON.parse(datas);
               console.log(data);
               createTable(data);
            }).fail(function (input) {
               alert("AJAX request failed");
               console.log(data);
            });

            break;
         default:
            throw new CustomException("no valid cases");
      }
   }

   function createTable(data) {
      // active NOP (players from DB) so table can be set properly

      let noc_2 = [0, 5];
      let noc_3 = [0, 3, 6];
      let noc_4 = [0, 2, 5, 7];
      let noc_5 = [0, 2, 4, 6, 8];
      let noc_6 = [0, 1, 3, 5, 7, 9];
      let noc_7 = [0, 1, 2, 4, 5, 7, 8];
      let noc_8 = [0, 1, 2, 4, 5, 6, 7, 9];
      let noc_9 = [0, 1, 2, 3, 4, 6, 7, 8, 9];
      let noc_10 = [0, 1, 2, 3, 4, 5, 6, 7, 8, 9];

      let NOP = data.length;
      let noc = eval("noc_" + NOP);

      function splitPlayers() {
         for (let x = 0; x < data.length; x++) {
            if (data[x][0] === playerId) {
               return data.splice(0, x);
            }
         }
      }


      let players = data.concat(splitPlayers());
      console.log(players);

      let playerTemplate = '';
      let chair = 0;

      players.forEach(() => {
         let blind = '';
         if(players[chair][3] !== null){
            blind = players[chair][3];
         } else {
            blind = '';
         }

         if (chair === 0) {
            playerTemplate = "<div class='chair chair_0'><div class='name " + playerName + "'>" + playerName + "</div><div class='player_cards'><img src='./images/playing_cards/" + players[chair][4] + ".png' class='card user_card_1'><img src='./images/playing_cards/" + players[chair][5] + ".png' class='card user_card_2'></div><div class='blind user_blind'>" + blind + "</div><div class='user_wallet'><div class='wallet'>" + players[chair][2] + "</div><div class='bet'></div></div></div>";
         } else {

            playerTemplate = "<div class='chair chair_" + noc[chair] + "'><div class='name " + players[chair][1] + "'>" + players[chair][1] + "</div><div class='player_cards'><img src='./images/playing_cards/blue_back.png' class='card user_card_1'><img src='./images/playing_cards/blue_back.png' class='card user_card_1'></div><div class='blind user_blind'>" + blind + "</div><div class='user_wallet'><div class='wallet'>" + players[chair][2] + "</div><div class='bet'></div></div></div>";
         }
         chair++
         $(".table_container").after(playerTemplate);
      })

   }

})