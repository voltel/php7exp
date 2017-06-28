<?php
namespace App\Controller\Admin\Response;

use Symfony\Component\HttpFoundation\JsonResponse;

interface AdminResponseInterface
{
  public static function createResponse($a_records = null) : JsonResponse;
}//end of interface
