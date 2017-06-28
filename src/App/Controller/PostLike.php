<?php
namespace App\Controller;

use App\Model\Like;

use App\Session\UserSessionInterface;
//use App\Form\FormValidatorInterface;

use Psr\Log\LoggerInterface;

use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\HttpFoundation\{
//  Request
  Response,
  JsonResponse
//  RedirectResponse,
//  Session\SessionInterface
};
use Doctrine\ORM\EntityManagerInterface;

/*
use App\Traits\{
  CreateFormTrait,
  FlashMessagesTrait
};
*/

class PostLike
{
//  use CreateFormTrait;
//  use FlashMessagesTrait;

  private $twig;
  private $log;
  //private $urlGenerator;
  private $em;
  //private $session;
  private $userSession;
//  private $formValidator;

  /**
  *
  */
  public function __construct(
    \Twig_Environment $twig,
    \Monolog\Logger $log,
    //UrlGeneratorInterface $urlGenerator,
    EntityManagerInterface $em,
    //SessionInterface $session,
    UserSessionInterface $userSession
    //FormValidatorInterface $formValidator
  ) {
    $this->twig = $twig;
    $this->log = $log;
    //$this->urlGenerator = $urlGenerator;
    $this->em = $em;
    //$this->session = $session;
    $this->userSession = $userSession;
    //$this->formValidator = $formValidator;
  }//end of function

  /**
   * Переменная $postId передается в closure из routing
   */
  public function likeAction(int $postId) : Response
  {
      $userFromSession = $this->userSession->getUser();
      $user = $this->em->find('App\Model\User', $userFromSession->getId());
      $post = $this->em->getRepository('App\Model\Post')->find($postId);
      if ($post->isLikedByUser($user)) {
        return new JsonResponse(['error' => 'preference already registered']);
      }//endif

      $like = new Like();
      $like->setUser($user);
      $like->setAssociatedPost($post);
      $like->setPostedAt(new \DateTime('now'));
      $this->em->persist($like);

      $post->addLike($like);
      $this->em->persist($post);
      $this->em->flush();

      return new JsonResponse(['num_likes' => count($post->getLike())], 201);
  }//end of funciton

  /**
   * Переменная $postId передается в closure из routing
   */
  public function unlikeAction(int $postId) : Response
  {
    $userFromSession = $this->userSession->getUser();
    $user = $this->em->find('App\Model\User', $userFromSession->getId());

    $post = $this->em->getRepository('App\Model\Post')->find($postId);
    if (!$post->isLikedByUser($user)) {
      return new JsonResponse(['error' => 'preference already registered']);
    }//endif

    $userLikes = $post->getAssociatedLikes()->filter(function($o_this_like) use ($user) {
      return $o_this_like->getUser() == $user;
    });

    $this->em->remove($userLikes[0]);
    $this->em->flush();

    return new JsonResponse(['num_likes' => count($userLikes) - 1], 200);
  }//end of function


}//end of class
