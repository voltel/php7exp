<?php
namespace App\Controller;
#src/App/Controller/Homepage.php

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Doctrine\ORM\EntityManagerInterface;

class Homepage
{
  private $twig;
  private $log;
  private $em;
  private $n_items_per_page;

  public function __construct(
    \Twig_Environment $twig,
    \Monolog\Logger $log,
    EntityManagerInterface $em,
    int $n_items_per_page
    )
  {
    $this->twig = $twig;
    $this->log = $log;
    $this->em = $em;
    $this->n_items_per_page = $n_items_per_page;
  }// end of __construct


  public function indexAction(): Response
  {
    //$this->log->addDebug('Testing the Monolog logging.');
    $a_posts = $this->getPosts(0);

    return new Response($this->twig->render('index.html.twig',
      array('posts' => $a_posts)
    ));

  }//end of indexAction

  /**
   *
   */
  private function getPosts(int $n_page_index = 0) : array
  {
    /*
    $a_posts = array();
    array_push($a_posts, array(
      'title' => 'first article',
      'description' => 'first description',
      'image' => 'http://placehold.it/600x270',
      'likes' => 1,
      'comments' => 1
    ));

    array_push($a_posts, array(
      'title' => 'second article',
      'description' => 'second description',
      'image' => 'http://placehold.it/600x270',
      'likes' => 2,
      'comments' => 2
    ));


    return $a_posts;
    return $posts = $this->em->getRepository('App\Model\Post')->findAll();
    */

    return $posts = $this->em->getRepository('App\Model\Post')->findBy(
      [],
      ['id' => 'DESC'],
      $this->n_items_per_page,
      $this->n_items_per_page * $n_page_index
    );
  }//end of function getPosts




  /**
  * Возвращает разметку дополнительных постов
  * Получает параметр $page из closure, при вызове routing
  */
  public function nextPageAction($page)
  {
    $n_page_index = (int) $page - 1;
    $n_page_index = $n_page_index < 0 ? 0 : $n_page_index;

    $a_posts = $this->getPosts($n_page_index);
    $c_html = $this->twig->render('posts.html.twig',
      array('posts' => $a_posts));
    //$c_html .= "<h2>\$page = {$page}; \$n_page_index = {$n_page_index}</h2>";

    return new Response($c_html, 201);
  }//end of function

}//end of class
