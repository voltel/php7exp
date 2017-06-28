<?php
namespace App\Controller;

//use App\Form\FormValidatorInterface;
use App\Model\Post;
use App\Session\UserSessionInterface;

//use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\HttpFoundation\{
  //Request,
  Response,
  //RedirectResponse,
  Session\SessionInterface
};
use Doctrine\ORM\EntityManagerInterface;
use App\Traits\{
  CreateFormTrait,
  FlashMessagesTrait
};


class Dashboard
{
  //use CreateFormTrait;
  //use FlashMessagesTrait;

  private $twig;
  //private $urlGenerator;
  private $em;
  private $session;
  private $userSession;
  //private $formValidator;

  /**
  *
  */
  public function __construct(
    \Twig_Environment $twig,
//    UrlGeneratorInterface $urlGenerator,
    EntityManagerInterface $em,
    SessionInterface $session,
    UserSessionInterface $userSession
//    FormValidatorInterface $formValidator
  ) {
    $this->twig = $twig;
    //$this->urlGemerator = $urlGenerator;
    $this->em = $em;
    $this->session = $session;
    $this->userSession = $userSession;
    //$this->formValidator = $formValidator;
  }//end of function


  /**
  *
  */
  public function indexAction() : Response
  {
    $o_user = $this->userSession->getUser();

    $a_posts = $this->em->getRepository('App\Model\Post')
      ->getTopPosts($o_user, 5);

    // $a_posts = $this->em->getRepository('App\Model\Post')
    //  ->findByUser($o_user->getId(), ['id' => 'DESC']);


    return new Response(
      $this->twig->render('dashboard.html.twig',
        ['posts' => $a_posts]
      )
    );
  }//end of function



}//end of class
