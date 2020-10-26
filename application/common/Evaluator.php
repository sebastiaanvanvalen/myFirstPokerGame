<?php

namespace application\common;

/*
* Sebastiaan
* date: "2020-10-13
* comment: this script gives an unique score for the 7 cards in a players hand. This script is a copy of the free-to-use poker-deck-cards-evaluation from phphulp.nl
link: https://www.phphulp.nl/php/script/data-verwerking/poker-deck-cards-evaluation/1347/
*/

class Evaluator
{
   // private $cards = array();
   private $values = array();
   private $num_values = array();
   private $suits = array();
   private $num_suits = array();
   private function __construct($f_arrCards)
   {
      $this->cards = $f_arrCards;

      $tmp = array();
      foreach ($f_arrCards as $objCard) {
         $tmp[] = array(
            'value'    => (int)$objCard->pth,
            'suit'    => (string)$objCard->suit,
         );
      }

      $rev2d = self::flip_2d_array($tmp);

      $this->values = $rev2d['value'];
      arsort($this->values);
      $this->num_values = array_count_values($this->values);

      $this->suits = $rev2d['suit'];
      asort($this->suits);
      $this->num_suits = array_count_values($this->suits);
      arsort($this->num_suits);
   }

   public static function readable_hand($f_fHand)
   {
      $arrCardsText = array(2 => 'Twos', 'Threes', 'Fours', 'Fives', 'Sixes', 'Sevens', 'Eights', 'Nines', 'Tens', 'Jacks', 'Queens', 'Kings', 'Aces');
      $arrCardsShort = array(2 => '2', '3', '4', '5', '6', '7', '8', '9', '10', 'J', 'Q', 'K', 'A');
      $x = explode('.', (string)$f_fHand, 2);
      $szExtra = isset($x[1]) ? $x[1] : '';
      $szHand = '';
      switch ((int)$x[0]) {
         case 9:
            $szHand = 'Royal Flush of ' . ucfirst(strtolower($szExtra)) . '';
            break;

         case 8:
            $szHand = 'Straight Flush of ' . ucfirst(strtolower(substr($szExtra, 2))) . ' - ' . $arrCardsText[(int)substr($szExtra, 0, 2)] . ' high';
            break;

         case 7:
            $szHand = 'Four Of A Kind - ' . $arrCardsText[(int)substr($szExtra, 0, 2)] . '';
            break;

         case 6:
            $szHand = 'Full House - ' . $arrCardsText[(int)substr($szExtra, 0, 2)] . ' over ' . $arrCardsText[(int)substr($szExtra, 2, 2)] . '';
            break;

         case 5:
            $szHand = 'Flush of ' . ucfirst(strtolower(substr($szExtra, 10))) . ' - ' . $arrCardsText[(int)substr($szExtra, 0, 2)] . ' high';
            break;

         case 4:
            $szHand = 'Straight - ' . $arrCardsText[(int)substr($szExtra, 0, 2)] . ' high';
            break;

         case 3:
            $szHand = 'Three Of A Kind - ' . $arrCardsText[(int)substr($szExtra, 0, 2)] . '';
            break;

         case 2:
            $szHand = 'Two Pairs - ' . $arrCardsText[(int)substr($szExtra, 0, 2)] . ' and ' . $arrCardsText[(int)substr($szExtra, 2, 2)] . '';
            break;

         case 1:
            $szHand = 'One Pair of ' . $arrCardsText[(int)substr($szExtra, 0, 2)] . '';
            break;

         case 0:
         default:
            $arrKickers = array($arrCardsShort[(int)substr($szExtra, 0, 2)]);
            if (0 < ($c = (int)substr($szExtra, 2, 2))) {
               $arrKickers[] = $arrCardsShort[$c];
            }
            if (0 < ($c = (int)substr($szExtra, 4, 2))) {
               $arrKickers[] = $arrCardsShort[$c];
            }
            if (0 < ($c = (int)substr($szExtra, 6, 2))) {
               $arrKickers[] = $arrCardsShort[$c];
            }
            if (0 < ($c = (int)substr($szExtra, 8, 2))) {
               $arrKickers[] = $arrCardsShort[$c];
            }
            $szHand = 'High Cards ' . implode(', ', $arrKickers) . '';
            break;
      }
      return $szHand;
   }

   # 9
   public function royal_flush()
   {
      $szStraigtFlush = $this->straight_flush();
      if ('14' === substr($szStraigtFlush, 0, 2)) {
         return substr($szStraigtFlush, 2);
      }
      return null;
   }
   # 8
   public function straight_flush()
   {
      if (null === ($szSuit = $this->flush(true))) {
         return null;
      }
      $arrCards = array();
      foreach ($this->values as $iCard => $iValue) {
         if ($szSuit == $this->suits[$iCard]) {
            $arrCards[] = $iValue;
         }
      }
      if (null !== ($szHiCard = $this->straight($arrCards))) {
         return $szHiCard . $szSuit;
      }
      return null;
   }
   # 7
   public function four_of_a_kind()
   {
      foreach ($this->num_values as $iValue => $iAmount) {
         if (4 <= $iAmount) {
            $szExtra = self::padleft($iValue);
            foreach ($this->values as $v) {
               if ($v != $iValue) {
                  $szExtra .= self::padleft($v);
                  return $szExtra;
               }
            }
         }
      }
      return null;
   }
   # 6
   public function full_house()
   {
      if (null !== ($szPair = $this->one_pair()) && null !== ($szThreeOfAKind = $this->three_of_a_kind())) {
         return substr($szThreeOfAKind, 0, 2) . substr($szPair, 0, 2);
      }
      return null;
   }
   # 5
   public function flush($f_bSimple = false)
   {
      if (5 <= reset($this->num_suits)) {
         $szSuit = key($this->num_suits);
         if ($f_bSimple) {
            return $szSuit;
         }
         $szExtra = '';
         foreach ($this->values as $iCard => $iValue) {
            if ($szSuit == $this->suits[$iCard] && 10 > strlen($szExtra)) {
               $szExtra .= self::padleft($iValue);
            }
            if (10 <= strlen($szExtra)) {
               break;
            }
         }
         return $szExtra . $szSuit;
      }
      return null;
   }
   # 4
   public function straight($f_arrValues = null)
   {
      $arrValues = is_array($f_arrValues) ? $f_arrValues : array_keys($this->num_values);
      if (5 > count($arrValues)) {
         // Not even 5 different cards
         return null;
      }
      for ($i = 0; $i <= count($arrValues) - 5; $i++) {
         // loop next 5 cards
         $iHiCard = $iPrevValue = $arrValues[$i];
         $bOk = true;
         for ($j = $i + 1; $j < $i + 5; $j++) {
            if ($arrValues[$j] != $iPrevValue - 1) {
               $bOk = false;
               break;
            }
            $iPrevValue = $arrValues[$j];
         }
         if ($bOk) {
            return self::padleft($iHiCard);
         }
      }
      # ace to 5
      if (in_array(14, $arrValues) && in_array(2, $arrValues) && in_array(3, $arrValues) && in_array(4, $arrValues) && in_array(5, $arrValues)) {
         return '05';
      }
      return null;
   }
   # 3
   public function three_of_a_kind()
   {
      foreach ($this->num_values as $iValue => $iAmount) {
         if (3 == $iAmount) {
            $szExtras = self::padleft($iValue);
            foreach ($this->values as $v) {
               if ($iValue != $v && 6 > strlen($szExtras)) {
                  $szExtras .= self::padleft($v);
               }
               if (6 <= strlen($szExtras)) {
                  break;
               }
            }
            return $szExtras;
         }
      }
      return null;
   }
   # 2
   public function two_pair()
   {
      $szExtras = '';
      $iVal1 = $iVal2 = 0;
      foreach ($this->num_values as $iValue => $iAmount) {
         if (2 == $iAmount && 4 > strlen($szExtras)) {
            $szExtras .= self::padleft($iValue);
         }
      }
      if (4 == strlen($szExtras)) {
         foreach ($this->values as $v) {
            if ($iVal1 != $v && $iVal2 != $v) {
               $szExtras .= self::padleft($v);
               break;
            }
         }
         return $szExtras;
      }
      return null;
   }
   # 1
   public function one_pair()
   {
      foreach ($this->num_values as $iValue => $iAmount) {
         if (2 == $iAmount) {
            $szExtras = self::padleft($iValue);
            foreach ($this->values as $v) {
               if ($iValue != $v && 8 > strlen($szExtras)) {
                  $szExtras .= self::padleft($v);
               }
               if (8 <= strlen($szExtras)) {
                  break;
               }
            }
            return $szExtras;
            break;
         }
      }
      return null;
   }

   public function _score()
   {
      $arrCheckingOrder = array(
         'royal_flush',
         'straight_flush',
         'four_of_a_kind',
         'full_house',
         'flush',
         'straight',
         'three_of_a_kind',
         'two_pair',
         'one_pair',
      );
      $iScore = 0;
      foreach ($arrCheckingOrder as $iRevHandValue => $szCall) {
         if (null !== ($szExtra = call_user_func(array($this, $szCall)))) {
            $iScore = 9 - $iRevHandValue;
            break;
         }
      }
      if (0 >= $iScore) {
         // high card
         $iScore = 0;
         $szExtra = '';
         foreach ($this->values as $v) {
            if (10 > strlen($szExtra)) {
               $szExtra .= self::padleft($v);
            }
            if (10 <= strlen($szExtra)) {
               break;
            }
         }
      }
      return ($iScore . '.' . (string)$szExtra);
   }
   public static function padleft($s)
   {
      return str_pad((string)$s, 2, '0', STR_PAD_LEFT);
   }
   public static function score($cards)
   {
      $hand = new self($cards);
      return $hand->_score();
   }
   public static function flip_2d_array($a)
   {
      $r = array();
      foreach ($a as $k1 => $v1) {
         foreach ($v1 as $k2 => $v2) {
            $r[$k2][$k1] = $v2;
         }
      }
      return $r;
   }
}