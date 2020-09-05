$(document).ready(function(){
    $(".new_game_btn").on('click', function(){
       $.ajax({
           type: 'GET',
           url: '/application/main/',
           headers: {
            //   csrf_token: xxx,
            reqType: "newGame",
            client_name: "Sebastiaan",
            n_o_p: 8 // number of players
            
           },
           async: true,
       }).done(function (data) {
           console.log(data);
       }).fail(function (data) {
           alert("AJAX request failed");
           console.log(data);
       });
})
})