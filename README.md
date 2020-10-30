# myFirstPokerGame

###
For now: this is NOT yet a finished project! 

## terminology and abbreviations
1. NOP = Number Of Player (ex client)


## cheat choices
things to consider:

### cards
I don't want the client to be able to see of manipulate the cards of the other players untill the game is over.
1. the actual cards of the players stay hidden on the server.
2. the deck from which all the card are dealt stays hidden on the server.

### money
I is part of the game to see the amount of money the other players have. Having to manage their wallets is safe to do in JavaScript. This way we decrease the amount of data to and from the server every time a change is made. 
On the other hand we need a check to make sure the client has not been tempering with any of the wallets. For now I think an update of status of every wallet is needed. Making contact with the server for this, we can just as well manage wallets on the server...
so:
1. every round we will update the wallets on the server.

### player name
If the player is goning to be able to safe a game we need to be sure an up-to-date situation of the game exisit in the database. It is unclear what this means for data trafic.
Choice: If a game is aborted, the game is lost.
   - which means a saved game can be restarted by aborting? (potential cheating)
so:
1. every move any player makes needs to be updated into the database. This way a game is always stored in a fair way.

## chairs
depending on the number of chosen players, an x amount of chairs have to be selected for an equaly filled table.
(# inlc client / chairs)
2 / user-5
3 / user-3-6
4 / user-2-5-7
5 / user-2-4-6-8
6 / user-1-3-5-7-9
7 / user-1-2-4-5-7-8
8 / user-1-2-4-5-6-7-9
9 / user-1-2-3-4-6-7-8-9
10/ user-1-2-3-4-5-6-7-8-9

