<?php
namespace App\Controller;

use App\Form\FormValidatorInterface;
use App\Model\Comment;
use App\Session\UserSessionInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\HttpFoundation\{
  Request,
  Response,
  JsonResponse,
  RedirectResponse,
  Session\SessionInterface
};
use Doctrine\ORM\EntityManagerInterface;

/*
use App\Traits\{
  CreateFormTrait,
  FlashMessagesTrait
};
*/

class PostComment
{
//  use CreateFormTrait;
//  use FlashMessagesTrait;

  private $twig;
  private $log;
  //private $urlGenerator;
  private $em;
  //private $session;
  private $userSession;
  private $formValidator;

  /**
  *
  */
  public function __construct(
    \Twig_Environment $twig,
    \Monolog\Logger $log,
    //UrlGeneratorInterface $urlGenerator,
    EntityManagerInterface $em,
    //SessionInterface $session,
    UserSessionInterface $userSession,
    FormValidatorInterface $formValidator
  ) {
    $this->twig = $twig;
    $this->log = $log;
    //$this->urlGenerator = $urlGenerator;
    $this->em = $em;
    //$this->session = $session;
    $this->userSession = $userSession;
    $this->formValidator = $formValidator;
  }//end of function


  /**
  *
  */
  public function getCommentsAction($postId) : Response
  {
    $comments = $this->em->getRepository('App\Model\Comment')
      ->findByPost($postId, ['id' => 'DESC']);



    $c_html = $this->twig->render('comments.html.twig', ['comments' => $comments]);
    return new Response($c_html);
  }//end of function


  /**
  * могло называться submitCommentAction
  */
  public function postCommentAction(Request $request) : Response
  {
    if ($this->formValidator->isValid($request)) {
        $comment = $this->saveComment($request);

        $c_html = $this->twig->render('single_comment.html.twig', ['comment' => $comment]);
        return new Response($c_html, 201);
    }//endif

    //errors
    $errors = $this->formValidator->getErrors();
    return new Response(implode(', ', $errors), 400);
  }//end of function




  /**
  *
  */
  private function saveComment(Request $request) : Comment
  {
    $this->log->debug(sprintf('%s: attempting to save a comment to user post at %s', __CLASS__, __METHOD__));

    $comment = new Comment();
    $user = $this->userSession->getUser();
    $user = $this->em->find('App\Model\User', $user->getId());

    $comment->setUser($user);
    $comment->setComment($request->get('comment'));
    $post = $this->em->find('App\Model\Post', $request->get('postId'));

    $post->setCommentsNum($post->getCommentsNum() + 1);
    $comment->setPost($post);
    $comment->setPostedAt(new \DateTime('now'));

    $this->em->persist($comment);
    $this->em->flush();

    return $comment;
  }//end of function


  /**
  * это с другого класса Post
  */
  public function submitPostAction(Request $request) : Response
  {
    if ($this->formValidator->isValid($request)) {
      $post = $this->savePost($request);
      // OK, AJAX
      if ($request->getMethod() == 'PUT') {
        $c_html = $this->twig->render('post.html.twig', ['post' => $post]);
        return new Response($c_html, 201);
      }//endif

      // OK, not AJAX - show user's Dashboard
      $this->setFlashMessages('errors', $this->formValidator->getErrors());
      $location = $this->urlGenerator->generate('dashboard');
      return new RedirectResponse($location);
    }//endif

    /*
    $this->setFlashMessages('errors', $this->formValidator->getErrors());

    $location = $this->urlGenerator->generate('post');
    return new RedirectResponse($location);
    */

    $c_html = implode('<br/>', $this->formValidator->getErrors());
    return new Response($c_html, 400);
  }//end of funciton

}//end of class
