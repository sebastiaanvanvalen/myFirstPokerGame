<?php
namespace application\common;

class Card
{
   public $id = -1;
   public $name = '';
   public $suit = '';
   public $value = -1;
   public $short = '';
   public $pth = -1;
   public function __construct($f_iCard)
   {
      $iCard = (int)$f_iCard % 52;
      $iSuit = floor($iCard / 13);
      $iName = $iCard % 13;

      $arrSuits = array('clubs', 'diamonds', 'hearts', 'spades');
      $arrNames = array('ace', 'two', 'three', 'four', 'five', 'six', 'seven', 'eight', 'nine', 'ten', 'jack', 'queen', 'king');
      $arrShorts = array('a', '2', '3', '4', '5', '6', '7', '8', '9', '10', 'j', 'q', 'k');
      $arrValues = array(11, 2, 3, 4, 5, 6, 7, 8, 9, 10, 10, 10, 10);
      $arrPTHValues = array(14, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13);

      $this->id = $iCard;
      $this->suit = $arrSuits[$iSuit];
      $this->name = $arrNames[$iName];
      $this->value = $arrValues[$iName];
      $this->short = $arrShorts[$iName];
      $this->pth = $arrPTHValues[$iName];
   }
   public function __tostring()
   {
      return call_user_func(array($this, self::$tostring));
      return $this->image();
   }

   public function small_image()
   {
      return '<img src="' . str_replace('__SUIT_____SHORT__', $this->suit, self::image_path) . '" /> ' . strtoupper($this->short);
   }
   
   public function image()
   {
      $r = array(
         '__SHORT__'    => $this->short,
         '__NAME__'    => $this->name,
         '__SUIT__'    => $this->suit,
         '__CARD__'    => $this->id,
      );
      return '<img title="' . $this->id . '" src="' . strtr(self::image_path, $r) . '" />';
   }
   public function html()
   {
      return strtoupper($this->short) . ' of ' . $this->suit;
   }
   public static function random()
   {
      return new Card(rand(0, 51));
   }
}