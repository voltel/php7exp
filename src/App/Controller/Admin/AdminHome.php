<?php
namespace App\Controller\Admin;
#src/App/Controller/Admin/AdminHome.php

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Doctrine\ORM\EntityManagerInterface;

class AdminHome
{
  private $twig;
  private $log;
  private $em;

  public function __construct(
    \Twig_Environment $twig,
    \Monolog\Logger $log,
    EntityManagerInterface $em
    )
  {
    $this->twig = $twig;
    $this->log = $log;
    $this->em = $em;
  }// end of __construct


  /**
   *
   */
  public function indexAction(): Response
  {
    //$this->log->addDebug('Testing the Monolog logging.');

    return new Response($this->twig->render('admin/admin_index.html.twig'));

  }//end of indexAction


}//end of class
