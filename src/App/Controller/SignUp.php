<?php
namespace App\Controller;

use App\Model\User;
use App\Model\UserRole;

use App\Password\HasherInterface;
use Psr\Log\LoggerInterface;

use Symfony\Component\{
  Routing\Generator\UrlGeneratorInterface, //??
  Security\Csrf\CsrfTokenManagerInterface
};

use Symfony\Component\HttpFoundation\{
  RedirectResponse,
  Request,
  Response,
  Session\Session,
  Session\SessionInterface
};

use Symfony\Component\Security\Csrf\CsrfToken;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Doctrine\ORM\EntityManagerInterface;
use App\Traits\UserFormTraits;
use Symfony\Component\Validator\Constraints as Assert;

class SignUp
{
  private $twig;
  private $log;
  private $csrfProvider;
  private $session;
  private $urlGenerator;
  private $validator;
  private $em;
  private $hasher;


  public function __construct(
    \Twig_Environment $twig,
    LoggerInterface $log,
    CsrfTokenManagerInterface $csrfProvider,
    SessionInterface $session,
    UrlGeneratorInterface $urlGenerator,
    ValidatorInterface $validator,
    EntityManagerInterface $em,
    HasherInterface $hasher
    )
  {
    $this->twig = $twig;
    $this->log = $log;
    $this->csrfProvider = $csrfProvider;
    $this->session = $session;
    $this->urlGenerator = $urlGenerator;
    $this->validator = $validator;
    $this->em = $em;
    $this->hasher = $hasher;
  }//end of function

  /**
   *
   */
  public function showFormAction() : Response
  {
    $csrfToken = $this->csrfProvider->refreshToken('token');
    $errors = $this->session->getBag('flashes')->get('errors');

    $c_html = $this->twig->render('signup.html.twig', ['token' =>$csrfToken,
      'errors' => $errors]);

    return new Response($c_html);
  }//end of function

  /**
   *
   */
   public function userRegistrationAction(Request $request) : Response
   {
     if ($this->isTokenValid('token', $request)) {
       $errors = $this->validateInputs($request);
       if (count($errors) == 0) {
         $email = $request->get('email');
         $user = $this->em->getRepository('App\Model\User')->findOneByEmail($email);

         if ($user) {
           $errors[] = 'Email already present.';
         } else {
           $this->insertUser($request);

           $location = $this->urlGenerator->generate('home');
           return new RedirectResponse($location);
         }//endif
       }//endif
       $this->setFlashErrors($errors);
     }//endif

     $this->log->debug('token or request not valid');

     $location = $this->urlGenerator->generate('signup');
     return new RedirectResponse($location);
   }//end of function

   /**
   *
   */
   private function isTokenValid(string $tokenId, Request $request) : bool
   {
     $token = new CsrfToken($tokenId, $request->get($tokenId));
     if ($this->csrfProvider->isTokenValid($token)) {
       return true;
     }//endif
     return false;
   }//end of function



   /**
   *
   */
   private function validateInputs(Request $request) : array
   {
     $errorMessages = array();

     $email = $request->get('email');
     $password = $request->get('password');

     $errors = array();
     $errors[] = $this->validator->validateValue($email,
      new Assert\NotBlank(['message' => 'The email should not be blank.'])
     );

     $errors[] = $this->validator->validateValue($email,
      new Assert\Email(['message' => 'Please, provide a valid email address.'])
     );

     $errors[] = $this->validator->validateValue($password,
      new Assert\NotBlank(['message' => 'The password should not be blank.'])
     );

     foreach ($errors as $error) {
       if (count($error) > 0) {
         $errorMessages[] = $error->get(0)->getMessage(0);
       }//endif
     }//end foreach

    return $errorMessages;
   }//end of function


   /**
    *
    */
   private function insertUser(Request $request)
   {
     $email = $request->get('email');
     $password = $request->get('password');

     $user = new User();
     $user->setEmail($email);
     $user->setPassword($this->hasher->hashPassword($password));
     $user->setRole(UserRole::ROLE_USER);

     $this->em->persist($user);
     $this->em->flush();
   }//end of funciton

   /**
   *
   */
   private function setFlashErrors(array $errors)
   {
     foreach ($errors as $error) {
       $this->session->getBag('flashes')->add('errors', $error);
     }//endfor
   }//end of function

}//end of class
