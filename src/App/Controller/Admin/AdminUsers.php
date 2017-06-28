<?php
namespace App\Controller\Admin;
#src/App/Controller/Admin/AdminUsers.php

use App\Model\User;
use App\Password\HasherInterface;
use Doctrine\ORM\EntityManagerInterface;
use App\Controller\Admin\Response\{
  AdminSuccessJson,
  AdminErrorJson
};

use Symfony\Component\HttpFoundation\{
  Response,
  JsonResponse,
  Request
};
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;



class AdminUsers
{
  private $twig;
  private $log;
  private $em;
  private $hasher;
  private $normalizer;

  private $userRepository; // объект класса UserRepository

  public function __construct(
    \Twig_Environment $twig,
    \Monolog\Logger $log,
    EntityManagerInterface $em,
    HasherInterface $hasher,
    NormalizerInterface $normalizer
    )
  {
    $this->twig = $twig;
    $this->log = $log;
    $this->em = $em;
    $this->hasher = $hasher;
    $this->normalizer = $normalizer;

    $this->userRepository = $this->em->getRepository('App\Model\User');
  }// end of __construct


  /**
   *
   */
  public function indexAction(): Response
  {
    //$this->log->addDebug('Class: ' . __CLASS__ . "; Method: " . __METHOD__);

    return new Response($this->twig->render('admin/admin_users.html.twig'));

  }//end of indexAction

  /**
   * Returns a JSON encoded array with a list of users objects
   */
  public function listAction(Request $request): JsonResponse
  {
    // расширение jtable возвращает в запросе AJAX два ключа
    $n_start_index = $request->get('jtStartIndex');
    $n_max_per_page = $request->get('jtPageSize');
    $c_sorting_params = $request->get('jtSorting');

    $a_users_array = $this->userRepository->getUsersBatchArray(
      $n_start_index, $n_max_per_page, $c_sorting_params);

    $n_total_records_count = $this->userRepository->getTotalUsersCount();

    return AdminSuccessJson::createResponse($a_users_array, $n_total_records_count);
  }//end of function


  /**
   *
   */
  public function createAction(Request $request): JsonResponse
  {
    try {
      $user = new User();
      // см. метод класса ниже
      $this->setValueFromRequest($user, $request);

      $this->em->persist($user);
      $this->em->flush();

      $n_total_records_count = $this->userRepository->getTotalUsersCount();

      $c_json = $this->normalizer->normalize($user);
      return AdminSuccessJson::createResponse($c_json, $n_total_records_count);

    } catch (\Throwable $t) {
      return AdminErrorJson::createResponse($t->getMessage());
    } //end of try&catch

  }//end of function

  /**
   * Редактирование данных пользователя
   */
  public function updateAction(Request $request): JsonResponse
  {
    try {
      $user_id = $request->get('id');
      //$this->log->addDebug(__METHOD__. ': Ищем пользователя с user id from AJAX request: ' . $user_id);
      $user = $this->userRepository->getUserById($user_id);

      $this->setValueFromRequest($user, $request);
      $this->em->persist($user); // не было
      $this->em->flush();

      $n_total_records_count = $this->userRepository->getTotalUsersCount();

      $c_json = $this->normalizer->normalize($user);
      return AdminSuccessJson::createResponse($c_json, $n_total_records_count);

    } catch (\Throwable $t) {
      return AdminErrorJson::createResponse($t->getMessage());
    }//end of try&catch
  }//end of function


  /**
   *
   */
  public function deleteAction(Request $request): JsonResponse
  {
    try {
      $user = $this->userRepository->getUserById($request->get('id'));
      $this->em->remove($user);
      $this->em->flush();

      return AdminSuccessJson::createResponse();

    } catch (\Throwable $t) {
      return AdminErrorJson::createResponse($t->getMessage());
    }//end of function
  }//end of function



   /**
    *
    */
    private function setValueFromRequest(User $user, Request $request)
    {
      $a_fields = ['email', 'role', 'name'];

      foreach ($a_fields as $c_this_field) {
        $c_field_value = $request->get($c_this_field);
        if (!empty($c_field_value)) {
          call_user_func([$user, 'set' . mb_convert_case($c_this_field, MB_CASE_TITLE, 'UTF-8')], $c_field_value);
          //$user->setEmail($request->get('email'));
        }//endif
      }//endforeach

      $c_field_value = $request->get('password');
      if (!empty($c_field_value)) {
        $user->setPassword($this->hasher->hashPassword($c_field_value));
      }//endif

    }//end of fucntion


}//end of class
