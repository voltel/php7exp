<?php
use App\Controller; // Интересно, что это папка, а не
use App\Session\UserSession;

use Symfony\Component\Security\Csrf\CsrfTokenManager;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Serializer\Normalizer;

/*
use Symfony\Component\HttpFoundation\{
  RedirectResponse,
  Request,
  Response,
  Session\Session,
  Session\SessionInterface
};
*/

$app['csrf_manager'] = function() {
  return new CsrfTokenManager();
};

$app['password_hasher'] = function() {
  return new App\Password\Hasher();
};


// voltel - это кусок я вставил сам с сайта Symfony
// http://symfony.com/doc/current/components/http_foundation/sessions.html
$symfony_session = new Session();
$symfony_session->start();
$app['session'] = $symfony_session;
$app['user_session'] = new UserSession($symfony_session);


$app['items_per_page'] = 5;

// ====== Configuring Controllers

// ====== Homepage
$app['homepage.controller'] = function() use ($app) {
  return new Controller\Homepage(
    $app['twig'],
    $app['monolog'],
    $app['em'],
    $app['items_per_page']
  );
};

// ====== Sign Up
$app['signup.controller'] = function() use ($app) {
  return new Controller\SignUp(
    $app['twig'],
    $app['monolog'],
    $app['csrf_manager'],
    $app['session'],
    $app['url_generator'],
    $app['validator'],
    $app['em'],
    $app['password_hasher']
  );
};

// ====== Login
// Controller's dependencies
$app['login.formValidator'] = function() use ($app) {
  return new \App\Form\LoginFormValidator($app['csrf_manager'], $app['validator']);
};

$app['login.controller'] = function() use ($app) {
  return new Controller\Login(
    $app['twig'],
    $app['monolog'],
    $app['session'],
    $app['url_generator'],
    $app['em'],
    $app['password_hasher'],
    $app['login.formValidator'],
    $app['user_session']
  );
};

// ======== Add User Post configuration
$app['user_post.formValidator'] = function() use ($app) {
  return new \App\Form\UserPostFormValidator($app['csrf_manager'], $app['validator']);
};

$app['user_post.controller'] = function() use ($app) {
  return new Controller\UserPost(
    $app['twig'],
    $app['monolog'],
    $app['url_generator'],
    $app['em'],
//    $app['session'],
    $app['user_session'],
    $app['user_post.formValidator'],
    $app['upload_dir']
  );
};



// ========= Add User Dashboard configuration
$app['dashboard.controller'] = function() use ($app) {
  return new Controller\Dashboard(
    $app['twig'],
    $app['em'],
    $app['session'],
    $app['user_session']
  );
};


// ========= Add Comment to post configuration
$app['postComment.formValidator'] = function() use ($app) {
  return new \App\Form\CommentFormValidator($app['csrf_manager'], $app['validator']);
};

$app['comment.controller'] = function() use ($app) {
  return new Controller\PostComment(
    $app['twig'],
    $app['monolog'],
    $app['em'],
    $app['user_session'],
    $app['postComment.formValidator']
  );
};

// ========= Add Comment to post configuration
//$app['postLike.formValidator'] = function() use ($app) {};

$app['postLike.controller'] = function() use ($app) {
  return new Controller\PostLike(
    $app['twig'],
    $app['monolog'],
    $app['em'],
    $app['user_session']
  );
};

// ====== Admin
$app['normalizer'] = new Normalizer\ObjectNormalizer();


// ====== AdminHome
$app['adminHome.controller'] = function() use ($app) {
  return new Controller\Admin\AdminHome(
    $app['twig'],
    $app['monolog'],
    $app['em']
  );
};


// ====== AdminUsers
$app['admin_users.controller'] = function() use ($app) {
return new Controller\Admin\AdminUsers(
    $app['twig'],
    $app['monolog'],
    $app['em'],
    $app['password_hasher'],
    $app['normalizer']
  );
};


// ====== AdminPosts
$app['admin_posts.controller'] = function() use ($app) {
  return new Controller\Admin\AdminPosts(
    $app['twig'],
    $app['monolog'],
    $app['em'],
    $app['normalizer'],
    $app['upload_dir']
  );
};



// ====== AdminComments
$app['admin_comments.controller'] = function() use ($app) {
  return new Controller\Admin\AdminComments(
    $app['twig'],
    $app['monolog'],
    $app['em']
//    $app['normalizer'],
  );
};



// ====== Statistics - Visit Record
$app['stat_visit_record'] = function() use ($app) {
  return new \App\Stat\StatService(
    $app['monolog'],
    $app['em'],
    $app['user_session']
  );
};


$app['admin_stats.controller'] = function() use ($app) {
  return new Controller\Admin\AdminStats(
    $app['twig'],
    $app['monolog'],
    $app['em']
  );
};
