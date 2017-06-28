<?php
/*
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

$app
  ->get('/', function () use($app) {
    return $app['twig']->render('index.html.twig', array());   })
  ->bind('homepage');
*/

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\RedirectResponse;

$controllerPath = '../src/App/Controller/'; // NB! в источнике - ../src/Controller/

require 'services.php';



// Firewall - ban access to anonymous users
$userAuth = function (Request $request, Silex\Application $app) {
  if (!($app['user_session']->hasRole(\App\Model\UserRole::ROLE_USER) // , $request->getSession()
      || $app['user_session']->hasRole(\App\Model\UserRole::ROLE_ADMIN))
  ) {
    exit('см функцию userAuth в файле routing.php');
    return new RedirectResponse($app['url_generator']->generate('login'));
  }//endif
};

/* Сохранение данных о визите пользователя */
$fStatVisitRecord = function(Request $request, Response $response, Silex\Application $app) {
  $app['stat_visit_record']->saveStat();
}; //



// Routing schema
// 'homepage.controller:indexAction'
$app->get('/', 'homepage.controller:indexAction')
  ->before($userAuth)
  ->after($fStatVisitRecord) // записать данные о визите пользователя в табличку visits
  ->bind('home');

$app->get('/next_page/{page}', 'homepage.controller:nextPageAction')
  ->value('page', 2)->bind('next_home_page')->before($userAuth);

$app->get('/signup', 'signup.controller:showFormAction')->bind('signup');
$app->post('/signup/registration', 'signup.controller:userRegistrationAction')
  ->bind('registration');

$app->get('/login', 'login.controller:showFormAction')->bind('login');
$app->post('/login', 'login.controller:loginCheckAction')->bind('login_check');
$app->get('/logout', 'login.controller:logoutAction')->bind('logout');

$app->get('/post', 'user_post.controller:showPostFormAction')
  ->bind('edit_new_user_post')->before($userAuth);
$app->post('/post', 'user_post.controller:submitPostAction')
  ->bind('submit_user_post')->before($userAuth);
$app->put('/post', 'user_post.controller:submitPostAction')
    ->bind('submit_user_post_ajax')->before($userAuth);
// загрузка картинки средствами AJAX
$app->post('/post/upload', 'user_post.controller:uploadImageAction')
        ->bind('upload_image')->before($userAuth);

$app->get('/my_posts', 'dashboard.controller:indexAction')->bind('dashboard')
  ->before($userAuth);

// Комментарии - route с параметром {postId}
$app->get('/comments/post/{postId}', 'comment.controller:getCommentsAction')->bind('get_comments')->before($userAuth);
$app->post('/comment', 'comment.controller:postCommentAction')->bind('post_comment')->before($userAuth);

// Liking/Unliking
$app->post('/like/post/{postId}', 'postLike.controller:likeAction')->bind('likeUserPost')->before($userAuth);
$app->delete('/like/post/{postId}', 'postLike.controller:unlikeAction')->bind('unlikeUserPost')->before($userAuth);

// =====================
// Firewall Admin
$f_admin_auth = function (Request $request, Silex\Application $app) {
  if (!$app['user_session']->hasRole(\App\Model\UserRole::ROLE_ADMIN) ) {
    return new RedirectResponse($app['url_generator']->generate('login'));
  }//endif
};


// Admin Users
$admin = $app['controllers_factory'];
$app->mount('/admin', $admin);

// Admin Home Page
$admin
  ->before($f_admin_auth)
  ->get('/', 'adminHome.controller:indexAction')->bind('admin_page');

/* Администрирование пользователей */
$admin
  ->mount('/users', function($users) use ($f_admin_auth) {
    $users->before($f_admin_auth);

    // URL в браузере
    $users->get('/', 'admin_users.controller:indexAction')->bind('admin_users');

    // AJAX from jtable.js
    $users->post('/list', 'admin_users.controller:listAction')->bind('admin_users_list');
    $users->post('/create', 'admin_users.controller:createAction')->bind('admin_users_create');
    $users->post('/update', 'admin_users.controller:updateAction')->bind('admin_users_update');
    $users->post('/delete', 'admin_users.controller:deleteAction')->bind('admin_users_delete');
  });


/* Администрирование пользовательских постов */
$admin
  ->mount('/posts', function($posts) use ($f_admin_auth) {
    $posts->before($f_admin_auth);

    // URL в браузере
    $posts->get('/', 'admin_posts.controller:indexAction')->bind('admin_posts_list');

    // AJAX from jtable.js
    $posts->post('/list', 'admin_posts.controller:listAction')->bind('admin_post_list');
    $posts->post('/delete', 'admin_posts.controller:deleteAction')->bind('admin_post_delete');
  });


/* Администрирование пользовательских комментариев */
$admin
  ->mount('/comments', function($comments) use ($f_admin_auth) {
    $comments->before($f_admin_auth);

    // URL в браузере - это ветки нет
    // $comments->get('/', 'admin_posts.controller:indexAction')->bind('admin_posts_list');

    // AJAX from jtable.js
    $comments->post('/list', 'admin_comments.controller:listAction');
    $comments->post('/delete', 'admin_comments.controller:deleteAction')->bind('admin_comment_delete');
  });

$admin->get('/stats', 'admin_stats.controller:indexAction')->bind('admin_stats')->before($f_admin_auth);
