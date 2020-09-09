$(document).ready(function () {
    let csrf_token;
    let clientName;
    let NOP = $(".NOP").val();

    $(".submit_NOP_btn").on('click', function () {
        $.ajax({
            type: 'GET',
            url: '/application/main',
            headers: {
                //   csrf_token: xxx,
                reqType: "newGame",
                client_name: "Sebastiaan",
                NOP: NOP

            },
            async: true,
        }).done(function (data) {
            // data = json with all players id's, chairNumbers, walletContents and the client's cards
            let players = JSON.parse(data);
            console.log(data);
            setPlayers(players);
        }).fail(function (data) {
            alert("AJAX request failed");
            console.log(data);
        });
    })
})

// have x amount of player occupy the table nice and evenly
function setPlayers(players) {
    let NOP = players[0].length;
    let chairs = [];

    for (let x = 0; x < NOP; x++) {
        let name   = players[0][x][0];
        let chair  = players[0][x][1];
        let wallet = players[0][x][3];
        let blind;
        let card_1;
        let card_2;
        players[0][x][4] ? card_1 = '<img src="./images/playing_cards/'+ players[0][x][4] +'.png" class="card user_card_1">' : card_1 = '<img src="./images/playing_cards/blue_back.png" class="card user_card_1">';
        players[0][x][5] ? card_2 = '<img src="./images/playing_cards/'+ players[0][x][5] +'.png" class="card user_card_1">' : card_2 = '<img src="./images/playing_cards/blue_back.png" class="card user_card_2">';
        
        if (typeof (name) === 'number') {
            name = "player_" + players[0][x][0];
        }
        
        if (players[0][x][2] === "BB"){
            blind = "BB";
        } else if (players[0][x][2] === "SB") {
            blind = "sb"
        } else {
            blind = ""
        }

        let playerTemplate = "<div class='chair chair_" + chair + "'><div class='name " + name + "'>" + name + "</div><div class='player_cards'>" + card_1 + card_2 + "</div><div class='blind user_blind'>"+ blind +"</div><div class='user_wallet'><div class='wallet'>" + wallet + "</div><div class='bet'></div></div></div>";
        chairs.push(chair);
        $(".table_container").after(playerTemplate);
    }

}
