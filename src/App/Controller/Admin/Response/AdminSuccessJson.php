<?php
namespace App\Controller\Admin\Response;

use Symfony\Component\HttpFoundation\JsonResponse;

class AdminSuccessJson implements AdminResponseInterface
{
  public static function createResponse($a_records = null, $n_total_records_count = null) : JsonResponse
  {
    $adminResponse = ['Result' => 'OK', 'TotalRecordCount' => null];

    // Если есть данные, связанные с ответом
    if ($a_records != null) {
      // ключ, в котором будут переданы данные, меняется в зависимости от того
      // это массив данных (одна запись) или массив массивов (несколько записей)
      $key = (count($a_records) == count($a_records, COUNT_RECURSIVE)) ?
        'Record' : 'Records';

      $adminResponse[$key] = $a_records;

      // сколько всего записей мы имеем в наличии (например, пользователей в системе)
      if (is_null($n_total_records_count)) {
        $n_total_records_count = count($a_records);

      } else if($n_total_records_count < count($a_records)) {
        throw new \InvalidArgumentException("Вторым параметром мы ожидали общее количество записей. Ошибка в методе " . __METHOD__ . " на строке " . __LINE__);
      }//endif


      if (! is_null($n_total_records_count)) {
        $adminResponse['TotalRecordCount'] = $n_total_records_count;
      }//endif
    }//endif

    return new JsonResponse($adminResponse);
  }//end of function


}//end of class
