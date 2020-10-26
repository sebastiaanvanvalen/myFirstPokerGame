<?php

namespace application\common;

class Table
{
   private $players = array();
   private $smallBlind;
   private $bigBlind;
   private $position;
   private $SBPos;
   private $BBPos;
   private $dealerPos;
   private $playerPos;
   private $ante;
   private $timer;
   private $tablePot;
   private $tableStatus;
   private $tableCards = array();

   public function __construct($smallBlind, $bigBlind, $ante, $timer)
   {
      $this->smallBlind = $smallBlind;
      $this->bigBlind = $bigBlind;
      $this->position = 0;
      $this->SBpos = null;
      $this->BBpos = null;
      $this->dealerPos = null;
      $this->playerPos = null;
      $this->ante = $ante;
      $this->timer = $timer;
      $this->tablePot = 0;
      $this->tableStatus = 0;
   }

   public function addPlayer($playerId, $playerName, $playerAge, $wallet, $cards)
   {
      $player = (object)[
         "playerId"   => $playerId,
         "playerName" => $playerName,
         'playerAge'  => $playerAge,
         "position"   => $this->position,
         "blindType"  => "",
         "dealer"     => "",
         "state"      => "waiting",
         "wallet"     => $wallet,
         "bets"       => 0,
         "card_1"     => $cards[0],
         "card_2"     => $cards[1],
         "round"      => 0
      ];

      $this->position++;
      array_push($this->players, $player);
   }

   public function checkChoice($playerId, $playerChoice, $playerBet)
   {
      foreach ($this->players as $player) {
         if ($player->playerId === $playerId && $player->state !== "playing") {
            return "notYourTurn";
         }
         if ($player->playerId === $playerId && $player->wallet < $playerBet) {
            return "invalidAmount";
         }
      }
   }

   public function setPlayerChoice($playerId, $playerChoice, $playerBet)
   {
      $loopCount = 0;
      foreach ($this->players as $player) {
         if ($player->playerId === $playerId) {

            $player->state = $playerChoice;

            $this->tablePot += $playerBet;
            $player->bets   += $playerBet;
            $player->wallet -= $playerBet;
            $player->round++;
            return $loopCount;
         }
         $loopCount++;
      }
   }

   public function evaluateTable()
   {
      if ($this->getWinByFolds() === true) {

         return "winByFolds";

         exit;
      } else if ($this->getBetBalance() === true && $this->tableStatus === 3 && $this->getPlayersInFirstRound() === 0) {

         return "evaluateHands";

         exit;
      } else if ($this->getNumberofPlayers() === 1) {

         return "definitiveWinner";

         exit;
      } else if ($this->getPlayersInFirstRound() > 0) {

         return "nextPlayer";

         exit;
      } else  if ($this->getBetBalance() === true) {

         return "nextCard";
         exit;
      } else {
         return "nextPlayer";
      }
   }

   public function setGameProgress($deck)
   {
      $this->tableStatus++;
      foreach ($this->players as $player) {
         $player->round = 0;
         $player->bets  = 0;

         if ($player->state === "folded") {
            $player->state = "folded";
         } else if ($player->state === "broke") {
            $player->state = "broke";
         } else {
            $player->state = "waiting";
         }
      }

      switch ($this->tableStatus) {
         case "1":
            $card1 = $deck->next();
            array_push($this->tableCards, $card1);
            $card2 = $deck->next();
            array_push($this->tableCards, $card2);
            $card3 = $deck->next();
            array_push($this->tableCards, $card3);

            return array($card1, $card2, $card3);

            break;
         case "2":
            $card4 = $deck->next();
            array_push($this->tableCards, $card4);
            return array($card4);

            break;
         case "3":
            $card5 = $deck->next();
            array_push($this->tableCards, $card5);
            return array($card5);

            break;
         case "4":
            return "determineWinner";

            break;
         default:
            throw new \Exception("no valid cases");
      }
   }

   // get players own status WITH cards
   public function getOwnStatus($playerId)
   {
      foreach ($this->players as $player) {
         if ($player->playerId === $playerId) {
            return array(
               "playerId"   => $player->playerId,
               "playerName" => $player->playerName,
               "playerAge"  => $player->playerAge,
               "blindType"  => $player->blindType,
               "dealer"     => $player->dealer,
               "state"      => $player->state,
               "wallet"     => $player->wallet,
               "bet"        => $player->bets,
               "card_1"     => $player->card_1,
               "card_2"     => $player->card_2,
               "round"      => $player->round,
               "tablePot"   => $this->tablePot,
               "callBet"    => $this->getCallBet($playerId),
               "tableStatus" => $this->tableStatus
            );
         }
      }
   }

   // get co-players status WITHOUT cards
   public function getCoPlayersStatus($playerId)
   {
      $output = array();
      foreach ($this->players as $player) {

         if ($player->playerId === $playerId) {
            return array(
               "playerId"   => $player->playerId,
               "playerName" => $player->playerName,
               "playerAge"  =>$player->playerAge,
               "blindType"  => $player->blindType,
               "dealer"     => $player->dealer,
               "state"      => $player->state,
               "wallet"     => $player->wallet,
               "bet"        => $player->bets,
               "round"      => $player->round,
            );
         }
      }
   }

   public function getPlayers()
   {
      return $this->players;
   }

   public function getWinner()
   {
      $winner = "";
      foreach ($this->players as $player) {
         if ($player->state !== "broke") {
            $winner = $player->playerName;
         }
      }
      return $winner;
   }

   public function getWinnerByFolds()
   {
      // $winner = "";
      foreach ($this->players as $player) {
         if ($player->state !== "folded" && $player->state !== "broke") {
            $winner = array(
               "playerName" => $player->playerName,
               "winnings" => $this->tablePot
            );

         }
      }
      return $winner;
   }

   public function setWinner($case, $winners)
   {
      switch ($case) {
         case "winner":
            foreach ($this->players as $player) {
               if ($player->playerId === $winners) {
                  $this->setMoney("winner", $winners);
               }
            }
            break;
         case "evaluateHands":
            $maxScore = array();
            $winner   = array();
            $maxScore = max(array_column($winners, 'score'));

            foreach ($winners as $player) {
               if ($player['score'] === $maxScore) {
                  array_push($winner, $player);
               }
            }
            //how to handle if we have 1 winner.
            if (count($winner) === 1) {
               foreach ($this->players as $player) {
                  if ($player->playerId === $winners[0]['playerId']) {
                     $tablePot = $this->tablePot;
                     $this->setMoney("winner", $player->playerId);
                     return array(
                        'playerId' => $player->playerId,  
                        'playerName' => $player->playerName,
                        'hand' => $winner[0]['cardsValue'],
                        'winnings' => $tablePot
                     );
                  }
               }
            } else {
               // splitPotSituation
            }
            break;
         case "":

            break;
         default:
            throw new \Exception("no valid cases");
      }
   }

   public function setPositions()
   {
      $NOP       = count($this->players) - 1;

      if ($this->SBPos === null) {

         $this->SBPos  = rand(0, $NOP);
         if ($this->SBPos === $NOP) {
            $this->BBPos = 0;
         } else {
            $this->BBPos = $this->SBPos + 1;
         }

         if ($this->BBPos === $NOP) {
            $this->dealerPos = 0;
            $this->playerPos = 0;
         } else {
            $this->dealerPos = $this->BBPos + 1;
            $this->playerPos = $this->BBPos + 1;
         }
         $this->players[$this->SBPos]->blindType = "SB";
         $this->players[$this->BBPos]->blindType  = "BB";
         $this->players[$this->dealerPos]->dealer = "dealer";
         $this->players[$this->playerPos]->state = "playing";

         $this->players[$this->SBPos]->wallet -= $this->smallBlind;
         $this->players[$this->BBPos]->wallet -= $this->bigBlind;

         $this->players[$this->SBPos]->bets += $this->smallBlind;
         $this->players[$this->BBPos]->bets += $this->bigBlind;

         $this->tablePot += $this->smallBlind + $this->bigBlind;
      }
   }

   // initiated when player hands need to be evaluated
   public function getTableCards()
   {
      return $this->tableCards;
   }

   public function setPlayer($case)
   {
      if ($case === "nextCard") {
         $this->playerPos = $this->dealerPos;
      }

      $tempArr = array_merge(array_slice($this->players, $this->playerPos, null, true), array_slice($this->players, 0, $this->playerPos, true));
      $highestBet = array();
      foreach ($this->players as $player) {
         array_push($highestBet, $player->bets);
      }

      foreach ($tempArr as $player) {
         // not all players had the chance to BET
         if ($player->bets < max($highestBet) && $player->state !== "folded" && $player->state !== 'broke' || $player->round === 0) {

            $player->state = "playing";
            $this->playerPos = $player->position;
            break;
         }
      }
   }

   public function setSmallBlind()
   {
      if ($this->SBPos === count($this->players) - 1) {
         $this->SBPos = 0;
      } else {
         $this->SBPos++;
      }
      $tempArr = array_merge(array_slice($this->players, $this->SBPos, null, true), array_slice($this->players, 0, $this->SBPos, true));

      foreach ($tempArr as $player) {
         if ($player->state !== 'broke') {
            $player->blindType = "SB";
            $this->SBPos = $player->position;
            break;
         }
      }

      $this->players[$this->SBPos]->wallet -= $this->smallBlind;
      $this->players[$this->SBPos]->bets += $this->smallBlind;
      $this->tablePot += $this->smallBlind;
   }

   public function setBigBlind()
   {
      if ($this->SBPos === count($this->players) - 1) {
         $this->BBPos = 0;
      } else {
         $this->BBPos = $this->SBPos + 1;
      }

      $tempArr = array_merge(array_slice($this->players, $this->BBPos, null, true), array_slice($this->players, 0, $this->BBPos, true));

      foreach ($tempArr as $player) {
         if ($player->state !== 'broke') {
            $player->blindType = "BB";
            $this->BBPos = $player->position;
            break;
         }
      }

      $this->players[$this->BBPos]->wallet -= $this->bigBlind;
      $this->players[$this->BBPos]->bets += $this->bigBlind;
      $this->tablePot += $this->bigBlind;
   }

   public function setDealer()
   {

      if ($this->BBPos === count($this->players) - 1) {
         $this->dealerPos = 0;
      } else {
         $this->dealerPos = $this->BBPos + 1;
      }
      $tempArr = array_merge(array_slice($this->players, $this->dealerPos, null, true), array_slice($this->players, 0, $this->dealerPos, true));

      foreach ($tempArr as $player) {

         if ($player->state !== 'broke') {
            $player->dealer = "dealer";
            $player->state = "playing";
            $this->dealerPos = $player->position;
            $this->playerPos = $player->position;
            break;
         }
      }
   }

   public function setMoney($case, $playerId = "")
   {
      switch ($case) {
         case "nextCard":
            foreach ($this->players as $player) {
            }
            break;
         case "winner":
            foreach ($this->players as $player) {
               if ($player->playerId === $playerId) {
                  $player->wallet += $this->tablePot;
               }
            }
            $this->tablePot = 0;
            break;

         default:
            throw new \Exception("no valid cases");
      }
      foreach ($this->players as $player) {
         if ($player->wallet === 0 || $player->state === "broke") {
            $player->state = "broke";
         }
      }


   }

   public function updatePlayers($deck)
   {
      foreach ($this->players as $player) {

         if ($player->state !== "broke") {
            $player->state     = "waiting";
            $player->dealer    = "";
            $player->blindType = "";
            $player->bets      = 0;
            $player->round     = 0;
            $player->card_1 = $deck->next();
            $player->card_2 = $deck->next();
         }
      }
      $this->tableStatus = 0;
   }

   // PRIVATE //
   private function getCallBet($playerId)
   {
      $allBets = array();
      $myBet = 0;
      foreach ($this->players as $player) {
         array_push($allBets, $player->bets);
         if ($player->playerId === $playerId) {
            $myBet = $player->bets;
         }
      }
      return max($allBets) - $myBet;
   }

   private function getNumberOfPlayers()
   {
      $players = 0;
      foreach ($this->players as $player) {
         if ($player->state !== "broke") {
            $players++;
         }
      }
      return $players;
   }

   private function getBetBalance()
   {
      $playersBets = array();
      foreach ($this->players as $player) {
         if ($player->state !== "folded" && $player->state !== "broke" && $player->state !== "all-in-low") {
            array_push($playersBets, $player->bets);
         }
      }
      if (count(array_unique($playersBets)) === 1) {
         return true;
      } else {
         return false;
      }
   }

   private function getWinByFolds()
   {
      $foldedPlayers = 0;
      foreach ($this->players as $player) {
         if ($player->state !== "folded") {
            $foldedPlayers++;
         }
      }
      if ($foldedPlayers === 1) {
         return true;
      } else {

         return false;
      }
   }

   private function getPlayersInFirstRound()
   {
      $playersInFirstRound = 0;
      foreach ($this->players as $player) {
         if ($player->round === 0) {
            $playersInFirstRound++;
         }
      }
      return $playersInFirstRound;
   }


}
