<?php
namespace App\Controller;

use App\Form\FormValidatorInterface;
use App\Model\Post;
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

use App\Traits\{
  CreateFormTrait,
  FlashMessagesTrait
};


class UserPost
{
  use CreateFormTrait;
  use FlashMessagesTrait;

  private $twig;
  private $log;
  private $urlGenerator;
  private $em;
  //private $session;
  private $userSession;
  private $formValidator;

  private $uploadDir;
  /**
  *
  */
  public function __construct(
    \Twig_Environment $twig,
    \Monolog\Logger $log,
    UrlGeneratorInterface $urlGenerator,
    EntityManagerInterface $em,
    //SessionInterface $session,
    UserSessionInterface $userSession,
    FormValidatorInterface $formValidator,
    string $uploadDir
  ) {
    $this->twig = $twig;
    $this->log = $log;
    $this->urlGenerator = $urlGenerator;
    $this->em = $em;
    //$this->session = $session;
    $this->userSession = $userSession;
    $this->formValidator = $formValidator;
    $this->uploadDir = $uploadDir;
  }//end of function

  /**
  * Для вызова страницы с формой без AJAX (обычным перенаплавлением)
  */
  public function showPostFormAction() : Response
  {
    return $this->createForm('user_post_form.html.twig');
  }//end of funciton


  /**
  *
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

  /**
  *
  */
  private function savePost(Request $request) : Post
  {
    $this->log->debug(sprintf('%s: attempting to save a user post at %s', __CLASS__, __METHOD__));

    $post = new Post();
    $user = $this->userSession->getUser();// был параметр $this->session
    $user = $this->em->find('App\Model\User', $user->getId());


    $post->setUser($user);
    $post->setTitle($request->get('title'));
    $post->setDescription($request->get('description'));
    $post->setPostedAt(new \DateTime('now'));


    $imageName = $request->get('imageName');
    //
    if ($imageName) {
      rename($this->uploadDir . '/temp/' . $imageName,
        $this->uploadDir . '/' . $imageName);
      $post->setImage($imageName);
    }//endif

    $this->em->persist($post);
    $this->em->flush();

    return $post;
  }//end of function

  /**
  *
  */
  public function uploadImageAction(Request $request) : Response
  {
    $this->log->debug(sprintf('%s: attempting to create an image on line %s', __CLASS__, __LINE__));

    $imageFile = $request->files->get('image');
    $c_ext = $imageFile->guessExtension();
    $filename = sha1(uniqid(mt_rand(), true)) . time();
    $filePath = $filename . '.' . $c_ext;

    $this->log->debug(sprintf('%s: attempting to create an image at %s', __CLASS__, $filePath));


    if ('png' == $c_ext) {
      $imageSrc = imagecreatefrompng($imageFile->getRealPath());
    } else {
      $imageSrc = imagecreatefromjpeg($imageFile->getRealPath());
    }//endif

    $a_size = getimagesize($imageFile->getRealPath());
    if ($a_size[0] > 800 || $a_size[1] > 800) {
      $imageScaled = imagescale($imageSrc, 800);
    } else {
       $imageScaled = $imageSrc;
    }//endif

    imagejpeg($imageScaled, $this->uploadDir . '/temp/' . $filePath);

    $this->log->debug(sprintf('%s: image created at %s', __CLASS__, $filePath));

    return new JsonResponse(['image' => $filePath], 201);
  }//end of function


}//end of class
