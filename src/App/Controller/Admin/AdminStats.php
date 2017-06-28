<?php
namespace App\Controller\Admin;
#src/App/Controller/Admin/AdminStats.php

//use App\Model\Post;
//use App\Model\User;
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

//use Symfony\Component\Serializer\Normalizer\NormalizerInterface;



class AdminStats
{
  private $twig;
  private $log;
  private $em;

//  private $postRepository; // объект класса PostRepository

  public function __construct(
    \Twig_Environment $twig,
    \Monolog\Logger $log,
    EntityManagerInterface $em
    )
  {
    $this->twig = $twig;
    $this->log = $log;
    $this->em = $em;

    //$this->postRepository = $this->em->getRepository('App\Model\Post');
  }// end of __construct


  /**
   *
   */
  public function indexAction(): Response
  {
    //$this->log->addDebug('Class: ' . __CLASS__ . "; Method: " . __METHOD__);
    $a_visits = $this->getVisits();
    $a_actions = $this->getActions();

    $c_html = $this->twig->render('admin/admin_stats.html.twig',
      [
        'visits' => $a_visits,
        'actions' => $a_actions
      ]);

    return new Response($c_html);
  }//end of indexAction


  /**
   */
  private function getVisits(): array
  {
    $qb = $this->em->createQueryBuilder();

    $qb->select('v.date, count(v.id) as visits_num')
      ->from('App\Model\Visit', 'v')
      ->groupBy('v.date')
      ->add('orderBy', 'v.date ASC')
      ->setFirstResult(0)
      ->setMaxResults(10);

    $o_query = $qb->getQuery();

    return $o_query->getScalarResult(); // ????
  }//end of function


  /**
   *
   */
   private function getActions() : array
   {
     $a_actions = [];

     //
     $qb = $this->em->createQueryBuilder()
        ->select('count(p.id)')
        ->from('App\Model\Post', 'p');
     $a_actions['posts'] = $qb->getQuery()->getSingleScalarResult();

     //
     $qb = $this->em->createQueryBuilder()
        ->select('count(c.id)')
        ->from('App\Model\Comment', 'c');
     $a_actions['comments'] = $qb->getQuery()->getSingleScalarResult();

     //
     $qb = $this->em->createQueryBuilder()
        ->select('count(l.id)')
        ->from('App\Model\Like', 'l');
     $a_actions['likes'] = $qb->getQuery()->getSingleScalarResult();

     //
     $qb = $this->em->createQueryBuilder()
        ->select('count(u.id)')
        ->from('App\Model\User', 'u');
     $a_actions['users'] = $qb->getQuery()->getSingleScalarResult();

     return $a_actions;
   } //end of function



  /**
   *
   */
  public function deleteAction(Request $request): JsonResponse
  {
    try {
      $post = $this->postRepository->getPostById($request->get('id'));

      // проверить, есть ли у поста картинка - если да, удалить её
      $postImageFilename = $post->getImage();
      if ($postImageFilename) {
          $imageFilepath = $this->uploadDir . '/' . $postImageFilename;
          unlink($imageFilepath);
      }//endif

      $this->em->remove($post);
      $this->em->flush();

      return AdminSuccessJson::createResponse();

    } catch (\Throwable $t) {
      return AdminErrorJson::createResponse($t->getMessage());
    }//end of try&catch

  }//end of function



}//end of class
