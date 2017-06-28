<?php
namespace App\Controller;

use App\Form\FormValidatorInterface;
/*
use App\Traits\{
  CreateFromTrait,
  FlashMessagesTrait
};
*/

//voltel
use App\Session\UserSessionInterface;
use App\Password\HasherInterface;

use Psr\Log\LoggerInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface; //??
use Symfony\Component\HttpFoundation\{
  RedirectResponse,
  Request,
  Response,
  Session\Session,
  Session\SessionInterface
};
use Doctrine\ORM\EntityManagerInterface;



class Login
{
  use \App\Traits\CreateFormTrait;
  use \App\Traits\FlashMessagesTrait;

  private $twig;
  private $log;
  private $session;
  private $urlGenerator; //??
  private $em;
  private $hasher;
  private $form;

  /**
   *
   */
  public function __construct(
    \Twig_Environment $twig,
    LoggerInterface $log,
    SessionInterface $session,
    UrlGeneratorInterface $urlGenerator,
    EntityManagerInterface $em,
    HasherInterface $hasher,
    FormValidatorInterface $formValidator,
    UserSessionInterface $userSession
    )
  {
    $this->twig = $twig;
    $this->log = $log;
    $this->session = $session;
    $this->urlGenerator = $urlGenerator;
    $this->em = $em;
    $this->hasher = $hasher;
    $this->formValidator = $formValidator;
    $this->userSession = $userSession;
  }//end of function

  /**
   *
   */
  public function showFormAction() : Response
  {
    exit('Login::showFormAction');
    return $this->createForm('login.html.twig');
  }//end of function

  /**
   *
   */
  public function loginCheckAction(Request $request) : Response
  {
    if ($this->formValidator->isValid($request)) {
      if ($this->areCredentialsValid($request->get('email'), $request->get('password'))) {
        $location = $this->urlGenerator->generate('home');
        return new RedirectResponse($location);
      }//endif

      $this->formValidator->addError('Wrong credentials.');
    }//endif

    // This branch is executed if credentials are invalid
    $this->setFlashMessages('errors', $this->formValidator->getErrors());
    $this->log->debug('Errors in form');
    return new RedirectResponse($this->urlGenerator->generate('login'));
  }//end of function

  /**
   *
   */
   public function logoutAction() : Response
   {
     $this->session->clear();

     return new RedirectResponse($this->urlGenerator->generate('login'));
   }//end of function


   /**
    *
    */
    private function areCredentialsValid(string $email, string $password) : bool
    {
      $user = $this->em->getRepository('App\Model\User')->findOneByEmail($email);
      if ($user) {
        if ($this->hasher->isPasswordValid($password, $user->getPassword())) {
          $this->userSession->setUser($user);
          return true;
        }//endif
      }//endif

      return false;
    }//end of function






}//end of class
