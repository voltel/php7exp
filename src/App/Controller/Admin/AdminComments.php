<?php
namespace App\Controller\Admin;
#src/App/Controller/Admin/AdminComments.php

use App\Model\Comment;

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



class AdminComments
{
  private $twig;
  private $log;
  private $em;
  //private $normalizer;

  private $commentRepository; // объект класса CommentRepository

  public function __construct(
    \Twig_Environment $twig,
    \Monolog\Logger $log,
    EntityManagerInterface $em
    //NormalizerInterface $normalizer,
    )
  {
    $this->twig = $twig;
    $this->log = $log;
    $this->em = $em;

    //$this->normalizer = $normalizer;

    $this->commentRepository = $this->em->getRepository('App\Model\Comment');
  }// end of __construct


  /**
   * Нет отдельной страницы с комментами
   */
  // public function indexAction(): Response
  // {
  //   //$this->log->addDebug('Class: ' . __CLASS__ . "; Method: " . __METHOD__);
  //
  //   return new Response($this->twig->render('admin/admin_posts.html.twig'));
  // }//end of indexAction


  /**
   * Returns a JSON encoded array with a list of Comment objects
   */
  public function listAction(Request $request): JsonResponse
  {
    // для комментов нет постаничного отображения

    $postId = $request->get('postId');
    if (empty($postId)) {
      $c_message = "Не удалось получить postId из запроса. ";
      $this->log->addDebug(__CLASS__ . ": " . $c_message);
      return AdminErrorJson::createResponse($c_message);
    }//endif

    $a_comments_array = $this->commentRepository->getCommentsArrayByPostId($postId);

  return AdminSuccessJson::createResponse($a_comments_array/*, $n_total_records_count*/);
  }//end of function




  /**
   *
   */
  public function deleteAction(Request $request): JsonResponse
  {
    try {
      $commentId = $request->get('id');
      if (empty($commentId)) {
        throw new \Exception("Пустой параметр id для Comment в методе " . __METHOD__);
      }//endif

      $postId = $request->get('postId');

      $this->commentRepository->deleteCommentById($commentId);

      $post = $this->em->getRepository('App\Model\Post')->findOneById($postId);
      $post->setCommentsNum($post->getCommentsNum() - 1);

      $this->em->persist($post);
      $this->em->flush();

      return AdminSuccessJson::createResponse();

    } catch (\Throwable $t) {
      return AdminErrorJson::createResponse($t->getMessage() . ' in ' . __METHOD__);
    }//end of try&catch

  }//end of function



}//end of class
