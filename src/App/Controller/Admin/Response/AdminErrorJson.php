<?php
namespace App\Controller\Admin\Response;

use Symfony\Component\HttpFoundation\JsonResponse;

class AdminErrorJson implements AdminResponseInterface
{
  public static function createResponse($mix_messages = null) : JsonResponse
  {
    $adminResponse = ['Result' => 'ERROR'];
    if ($mix_messages != null) {
      $adminResponse['Message'] = !is_array($mix_messages) ?
        $mix_messages :
        array_reduce ($mix_messages, function($carry, $c_this_message) {
          return $carry . "<br/>" . $c_this_message;
        }, "" );

    }//endif

    return new JsonResponse($adminResponse);
  }//end of function

}//end of class
