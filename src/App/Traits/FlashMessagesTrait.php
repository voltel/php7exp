<?php
namespace App\Traits;

trait FlashMessagesTrait
{
  /**
   * 'errors'
   */
   private function setFlashMessages($c_key, $a_messages)
   {
     foreach ($a_messages as $c_this_message) {
       $this->session->getBag('flashes')->add($c_key, $c_this_message);
     }//endfor
   }//end of function


}//end of trait
