$(document).ready(function () {

   let playerId   = $(".player_id").val();
   let playerName = $(".player_name").val();
   let playerAge  = $(".player_age").val();
   let startQue   = $(".start_game").val();
   let raiseBet   = parseInt($(".raise_input").val());
   let messages   = [];
   let players    = [];
   let modalMsg   = "";
   let fold       = false;
   let overRaiseCount = 0;
   let modalCount     = 0;
   let callBet, bet, wallet, state;
   
   startQue === "true" ? startGame() : '';
   document.title = "welcome " + playerName;


   // web socket incomming data
   window.WebSocket   = window.WebSocket || window.MozWebSocket;

   var connection     = new WebSocket('ws://localhost:8080');
   var connectingSpan = document.getElementById("connecting");
   connection.onopen  = function () {
      connectingSpan.style.display = "none";
   };
   connection.onconnect_failed = function (err) {
      console.log(err)
   };
   connection.onerror = function (error) {
      console.log(error);
      connectingSpan.innerHTML = "An error occured: <br>" + error;
   };
   connection.onmessage = function (message) {
      var data = JSON.parse(message.data);
      let msg;
      let player;
      console.log(data.dataType)
      console.log(data)

      switch (data.dataType) {
         case "loginMessage":
            msg = "<div class='chat_message msg_info'>" + data.playerName + " (" + data.playerAge + ") joined the table</div>";
            $(".msg_list").prepend(msg);
            messages.push(msg);

            break;
         case "chatMessage":
            let cssId;
            if (playerId === data.playerId) {
               cssId = "this_user";
               player = "(me) : "
            } else {
               cssId = "other_user";
               player = data.playerName + ": "
            }

            msg = "<p class='chat_message " + cssId + "'>" + player + data.chatMessage + "</p>";
            $(".msg_list").prepend(msg);
            messages.push(msg);

            break;
         case "nextCard":
            updateCards(data.cards);
            updateTable();

            break;
         case "nextPlayer":
            updateTable();

            break;
         case "roundWinner":
            showWinner(data);
            fold = false;
            setTimeout(() => updateTable("removeCards"), 4000);

            break;
         case "winnerByFolds":
            showWinner(data);
            fold = false;
            setTimeout(() => updateTable("removeCards"), 4000);

            break;
         case "definitiveWinner":
            showWinner(data);

            break;
         default:
            console.log(" no cases in socket node.js");
      }
   }

   $(".info_btn").on('click', function () {

      if (modalCount === 0){
         modalMsg = "hi " + playerName + ", <br>" +
         "Here's a quick cheatsheet for all hands and their scores <br><br>" +
         "<div class='cheatsheet'><img src='./images/cheatSheet.png'></div> <br><br>" +
         "If you need to check the rules for Texas hold 'em, maybe start <a href='https://en.wikipedia.org/wiki/Texas_hold_%27em#Rules'>here</a> <br> "
         $(".text_container").append(modalMsg);
         $(".modal_container").show('fast');
         modalCount++;  
      } else {
         $(".modal_container").hide('fast');
         $(".text_container").empty();
         modalMsg = "";
         modalCount = 0;
      }
   })
   
   $(".confirm_btn").on('click', function (){
      $(".modal_container").hide('fast');
      $(".text_container").empty();
      modalMsg = "";
      modalCount = 0;
   })

   $(".check_btn").on('click', function () {
      let count = 0;
      if (state === "playing" && fold === false && count === 0) {
         count++;
         if (callBet === 0) {
            sendChoice("check", callBet);
         }
         if (callBet > 0) {
            if (wallet < callBet) {
               alert("you will have to go ALL-IN to ")
               count = 0;
            }
            alert("to CALL you have to bet: €" + (callBet))
            count = 0;
         }
         // if client's currentBet is equal to highest currentBet
      }

   })

   $(".call_btn").on('click', function () {
      let count = 0;
      if (state === "playing" && fold === false && count === 0) {
         count++;

         // playerBet is equal to the tableBet
         if (callBet === 0) {
            if (wallet === 0) {
               alert("you went All-In! At this point you can either check or, if you are giving up, fold your cards")
               count = 0;
            }
         }

         if (callBet === wallet) {
            // player needs to go all in to match the BET on the table exactly.
            sendChoice("All-In", callBet)
         }

         if (callBet > wallet) {
            // player does NOT have enough money to match the bet on the table. So the player goes all-in. (modal yes/no)
            alert("you will have to go ALL-IN to ")
            count = 0;
         }

         if (callBet < wallet) {
            // this is the most common call...
            sendChoice("called", callBet)
         }
      }

   })

   $(".fold_btn").on('click', function () {
      let count = 0;
      if (state === "playing" && fold === false && count === 0) {
         console.log("folded");
         if (callBet === 0) {
            alert('at this point there is no risk in calling... Call it!')
            count++;

         } else {
            sendChoice("folded");
            fold = true;
         }
      }
   })

   $(".raise_btn").on('click', function () {
      let count = 0;
      if (state === "playing" && fold === false && count === 0) {
         count++;
         let raise = $(".raise_input").val();
         console.log("raise");

         if (raise === 0) {
            alert("you did not bet any money");
            count = 0;
         }

         if (raise < callBet) {
            alert("you will have to raise with more than €" + callBet);
         }

         if (raise > wallet) {
            alert("at this point you can bet maximal: €" + wallet);
         }

         if (wallet < callBet && raise > 0) {
            // chatmessage: you are going all in
            sendChoice("all-in", wallet);
         }

         if (wallet > callBet && raise > 0) {
            sendChoice("raise", raise)
         }
      }
      $(".raise_input").val(0);
   })

   $(".all_in_btn").on('click', function () {
      let count = 0;
      if (state === "playing" && fold === false && count === 0) {
         count++;

         let allIn = players[0]['wallet'];
         console.log("all in");

         if (allIn < callBet) {
            alert("You are going all-in but \n are €" + players[0]['callbet'] - players[0]['wallet'] + " short.");
         }

         sendChoice("all-in", allIn)
      }

   })

   $(".min_btn").on('click', function () {
      if (raiseBet >= 10) {
         // $(".raise_input").val() = $(".raise_input").val() - 10;         
         raiseBet -= 10;
      } else if ($(".raise_input").val() >= 1) {
         // $(".raise_input").val() = $(".raise_input").val() - 1;         
         raiseBet -= 1;
      } else {
         console.log("you can't go lower than zero!")
      }
      $(".raise_input").val(raiseBet);
   })

   $(".plus_btn").on('click', function () {
      let msg = "";
      if (players[0]['wallet'] === raiseBet) {
         if(overRaiseCount === 0){
            msg = "you are allready going All-In!";
            $(".text_container").append(msg);
            $(".modal_container").toggle('fast');
            $(".modal_container").delay(1500).toggle('fast');
            overRaiseCount++
         }
      } else if (players[0]['wallet'] - raiseBet <= 10) {
         raiseBet += 1;
         overRaiseCount = 0;
      } else {
         raiseBet += 10;
         overRaiseCount = 0;
      }
      $(".raise_input").val(raiseBet);


   })

   $(".player_message").keypress(function (e) {
      let chatMessage = $(".player_message").val();
      if (e.which === 13) {
         sendMessage(chatMessage);
         $(".player_message").val('');
      }
   })

   $(".submit_message").on('click', function () {
      let chatMessage = $(".player_message").val();
      sendMessage(chatMessage);
      $(".player_message").val('');
   })

   function sendMessage(chatMessage) {
      let chatToken = $(".chat_token").val();
      let playerId = $(".player_id").val();
      let playerName = $(".player_name").val();

      $.post("/application/main", {
         "csrf_token": chatToken,
         "tokenType": "chatToken",
         "dataType": "chatMessage",
         "playerId": playerId,
         "playerName": playerName,
         "chatMessage": chatMessage
      }, function (data) {
         // console.log(data);
      }).fail(function (data) {
         alert("AJAX request failed");
         console.log(data);
      });
   }

   function startGame() {
      // change "true" to "false". Else the game starts over when player refreshes the page!
      let csrfToken = $(".csrf_token").val();
      $.ajax({
         type: 'GET',
         url: '/application/main',
         headers: {
            'csrf_token': csrfToken,
            'tokenType': 'userToken',
            'playerId': playerId,
            'dataType': 'startGame'
         },
         async: true,
      }).done(function (data) {
         if (data === "") {
            console.log("no problems initiating startGame()")
         } else {
            console.log(data);
         }

      }).fail(function (data) {
         alert("AJAX request failed! <br> The game could not me started");
         console.log(data);
      });
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
            if (data[x]['playerId'] === playerId) {
               return data.splice(0, x);
            }
         }
      }
      players = data.concat(splitPlayers());
      callBet = players[0]['callBet'];
      wallet  = players[0]['wallet'];
      state   = players[0]['state'];
      bet     = players[0]['bet'];

      // console.log(players)


      // do better!
      $(".chair").remove();
      let chair = 0;

      players.forEach(() => {
         let playerTemplate = "";
         let playerWallet   = "";
         let activePlayer   = "";
         let playerCards    = "";
         let dealer = "";
         let cards  = "";
         let blind  = "";

         if (players[chair]['dealer'] === "dealer") {
            dealer = "<div class='dealer'>D</div>";
         }

         if (players[chair]['state'] === "playing") {
            activePlayer = "<div class='player_state playing'>Now Playing</div>"
         }

         if (players[chair]['state'] === "folded") {
            activePlayer = "<div class='player_state folded'>folded</div>"
         }

         if (players[chair]['state'] === "broke") {
            activePlayer = "<div class='player_state broke'>Broke...</div>"
         }

         if (players[chair]['blindType'] === "BB") {
            blind = "<div class='blind big_blind user_blind'>" + players[chair]['blindType'] + "</div>";
         }

         if (players[chair]['blindType'] === "SB") {
            blind = "<div class='blind small_blind user_blind'>" + players[chair]['blindType'] + "</div>";
         }

         if (players[chair]['bet'] === 0 ){
            bet = "";
         } else {
            bet = "<div class='bet'>€ " + players[chair]['bet'] + "</div>"
         }


         if (players[chair]['state'] === 'broke'){
            // img from action
            cards       = "<div class='player_cards'><img class='action_card' src='./images/actions/action" + getRandomInt(1, 13) +".png'>"
            playerCards = "<div class='player_cards'><img class='action_card' src='./images/actions/action" + getRandomInt(1, 13) +".png'>"
         } else {
            cards = "<div class='player_cards'><img src='./images/playing_cards/" + players[0]['card_1']['pth'] + players[0]['card_1']['suit'] + ".png' class='card_c user_card_1'><img src='./images/playing_cards/" + players[0]['card_2']['pth'] + players[0]['card_2']['suit'] + ".png' class='card_c user_card_2'></div>"; 
            playerCards = "<div class='player_cards'><img src='./images/playing_cards/blue_back.png' class='card_c user_card_1'><img src='./images/playing_cards/blue_back.png' class='card_c user_card_1'></div>"
         }

         if (chair === 0) {
            playerTemplate = "<div class='chair chair_0'><div class='name " + playerName + "'>" + playerName + "</div>"+ cards + blind + dealer + activePlayer + "<div class='user_wallet'><div class='wallet'>€ " + players[chair]['wallet'] + "</div>" + bet + "</div></div>";
         } else {
            playerTemplate = "<div class='chair chair_" + noc[chair] + "'><div class='name " + players[chair]['playerName'] + "'>" + players[chair]['playerName'] + "</div>" + playerCards + blind + dealer + activePlayer + "<div class='user_wallet'><div class='wallet'>€ " + players[chair]['wallet'] + "</div>" + bet + "</div></div>";
         }
         chair++
         $(".table_container").after(playerTemplate);
      })

      $(".raise_input").attr({
         "min": players[0]['callBet'],
         "max": players[0]['wallet']
      })
      $(".table_pot").text("pot: € " + players[0]['tablePot']);
   }

   function updateCards(cards) {
      console.log(cards);
      if (cards === "") {} else {
         cards.forEach((card) => {
            $(".card_container").append('<div class="card_c c_1"><img src="./images/playing_cards/' + card['pth'] + card['suit'] + '.png" class="card_c user_card_1"></div>')
         })
      }
   }

   function showWinner(data) {
      let msg = "";
      if (data.dataType === "winnerByFolds") {
         msg = data.winner.playerName + " won €" + data.winner.winnings + " because all players folded."
      }
      if (data.dataType === "roundWinner") {
         msg = data.winner.playerName + " won €" + data.winner.winnings + " with: " + data.winner.hand;
      }

      $(".text_container").append(msg)
      $(".modal_container").toggle('fast');
      $(".modal_container").delay(2500).toggle('fast');

      msg = "<div class='chat_message msg_info'>" + msg + "</div>";
      $(".msg_list").prepend(msg);

      console.log(data.winner + " is the winner and all. Some modal with happy joy joys")
   }

   function sendChoice(playerChoice, playerBet = 0) {
      $.ajax({
         type: 'get',
         url: '/application/main',
         headers: {
            'csrf_token': $(".user_token").val(),
            'tokenType': 'userToken',
            'dataType': 'sendChoice',
            'playerId': $(".player_id").val(),
            'playerChoice': playerChoice,
            'playerBet': playerBet
         },
         async: true,
      }).done(function (data) {
         if (data === "") {
            console.log("no problems initiating sendChoice()")
         } else {
            console.log(data);
         }

      }).fail(function (input) {
         alert("AJAX request failed");
         console.log(data);
      });

      msg = "<p class='chat_message msg_info'> you just " + playerChoice + " with € " + playerBet + "</p>";
      $(".msg_list").prepend(msg);
      messages.push(msg);
      $(".raise_input").val('');

   }

   function updateTable(removeCards = "") {
      if (removeCards === "removeCards") {
         $(".card_container").empty();
      }

      $.ajax({
         type: 'get',
         url: '/application/main',
         headers: {
            'csrf_token': $(".csrf_token").val(),
            'tokenType': 'loginToken',
            'dataType': "updateTable",
            'playerId': $(".player_id").val()
         },
         async: true,
      }).done(function (datas) {
         if (datas === "") {
            console.log("no response after initiating updatetable()")
         } else {

            let data = JSON.parse(datas);
            console.log(data);

            data.forEach((item) => {
               if (item['playerId'] === playerId) {
                  if (fold === true && item['state'] === "playing") {
                     console.log("auto: sendChoice('folded')");
                     sendChoice("folded");
                  }
               }
            })
            createTable(data);

         }
      }).fail(function (input) {
         alert("AJAX request failed");
         console.log(data);
      });
   }

   function getRandomInt(min, max) {
      min = Math.ceil(min);
      max = Math.floor(max);
      return Math.floor(Math.random() * (max - min) + min);
    }
})