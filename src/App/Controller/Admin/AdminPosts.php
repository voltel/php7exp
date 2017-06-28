<?php
namespace App\Controller\Admin;
#src/App/Controller/Admin/AdminPosts.php

use App\Model\Post;
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
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;



class AdminPosts
{
  private $twig;
  private $log;
  private $em;
  private $normalizer;
  private $uploadDir;

  private $postRepository; // объект класса PostRepository

  public function __construct(
    \Twig_Environment $twig,
    \Monolog\Logger $log,
    EntityManagerInterface $em,
    NormalizerInterface $normalizer,
    string $uploadDir
    )
  {
    $this->twig = $twig;
    $this->log = $log;
    $this->em = $em;

    $this->normalizer = $normalizer;
    $this->uploadDir = $uploadDir;

    $this->postRepository = $this->em->getRepository('App\Model\Post');
  }// end of __construct


  /**
   *
   */
  public function indexAction(): Response
  {
    //$this->log->addDebug('Class: ' . __CLASS__ . "; Method: " . __METHOD__);

    return new Response($this->twig->render('admin/admin_posts.html.twig'));
  }//end of indexAction


  /**
   * Returns a JSON encoded array with a list of post objects
   * Без привязки к пользователю (все посты подряд)
   */
  public function listAction(Request $request): JsonResponse
  {
    // расширение jtable возвращает в запросе AJAX два ключа
    $n_start_index = $request->get('jtStartIndex');
    $n_max_per_page = $request->get('jtPageSize');
    $c_sorting_params = $request->get('jtSorting');

    $a_posts_array = $this->postRepository->getPostsBatchArray(
      $n_start_index, $n_max_per_page, $c_sorting_params);

      foreach ($a_posts_array as $key => $a_this_post) {
        if ($a_this_post['image']) {
          $a_posts_array[$key]['image'] = '/images/' . $a_this_post['image'];
        }//endif
      }// end foreach

    $n_total_records_count = $this->postRepository->getTotalPostsCount();

    return AdminSuccessJson::createResponse($a_posts_array, $n_total_records_count);
  }//end of function




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
