<?php

use MicroCMS\Domain\Article;
use MicroCMS\Domain\Comment;
use MicroCMS\Domain\User;
use MicroCMS\Form\Type\ArticleType;
use MicroCMS\Form\Type\CommentType;
use MicroCMS\Form\Type\UserType;
use Symfony\Component\HttpFoundation\Request;

// Home page
$app->get('/', function () use ($app) {
    $articles = $app['dao.article']->findAll();

    return $app['twig']->render('index.html.twig', array('articles' => $articles));
})->bind('home');

// Articles details with comments
$app->match('article/{id}', function ($id, Request $request) use ($app) {
    $article = $app['dao.article']->find($id);
    $commentFormView = null;
    if ($app['security.authorization_checker']->isGranted('IS_AUTHENTICATED_FULLY')) {
        // A user is fully authencated : he can add comments
        $comment = new Comment();
        $comment->setArticle($article);
        $user = $app['user'];
        $comment->setAuthor($user);
        $commentForm = $app['form.factory']->create(new CommentType(), $comment);
        $commentForm->handleRequest($request);
        if ($commentForm->isSubmitted() && $commentForm->isValid()) {
            $app['dao.comment']->save($comment);
            $app['session']->getFlashBag()->add('success', 'Your comment was succesfully add.');
        }
        $commentFormView = $commentForm->createView();
    }
    $comments = $app['dao.comment']->findAllByArticle($id);
    return $app['twig']->render('article.html.twig', array(
                'article'     => $article,
                'comments'    => $comments,
                'commentForm' => $commentFormView));
})->bind('article');

// Login form
$app->get('/login', function(Request $request) use ($app) {
    return $app['twig']->render('login.html.twig', array(
                'error'         => $app['security.last_error']($request),
                'last_username' => $app['session']->get('_security.last_username'),
    ));
})->bind('login');

// Admin home page
$app->get('/admin', function() use ($app) {
    $articles = $app['dao.article']->findAll();
    $comments = $app['dao.comment']->findAll();
    $users = $app['dao.user']->findAll();

    return $app['twig']->render('admin.html.twig', array(
                'articles' => $articles,
                'comments' => $comments,
                'users'    => $users,
    ));
})->bind('admin');

// Add a new article
$app->match('/admin/article/add', function(Request $request) use ($app) {
    $article = new Article();
    $articleForm = $app['form.factory']->create(new ArticleType(), $article);
    $articleForm->handleRequest($request);
    if ($articleForm->isSubmitted() && $articleForm->isValid()) {
        $app['dao.article']->save($article);
        $app['session']->getFlashBag()->add('success', 'The article was succesfully created.');
    }

    return $app['twig']->render('article_form.html.twig', array(
                'title'       => "New Article",
                'articleForm' => $articleForm->createView(),
    ));
})->bind('admin_article_add');

// Edit an article
$app->match('/admin/article/{id}/edit', function($id, Request $request) use ($app) {
    $article = $app['dao.article']->find($id);
    $articleForm = $app['form.factory']->create(new ArticleType(), $article);
    $articleForm->handleRequest($request);
    if ($articleForm->isSubmitted() && $articleForm->isValid()) {
        $app['dao.article']->save($article);
        $app['session']->getFlashBag()->add('success', 'The article was succesfully update.');
    }

    return $app['twig']->render('article_form.html.twig', array(
                'title'       => 'Edit article',
                'articleForm' => $articleForm->createView(),
    ));
})->bind('admin_article_edit');

// Remove an article
$app->get('/admin/article/{id}/delete', function($id, Request $request) use ($app) {
// Delete all associated comments
    $app['dao.comment']->deleteAllByArticle($id);
// Delete the article
    $app['dao.article']->delete($id);
    $app['session']->getFlashBag()->add('success', 'The article was succesfully removed.');

// Redirect to admin home page
    return $app->redirect($app['url_generator']->generate('admin'));
})->bind('admin_article_delete');

// Edit an existing comment
$app->match('/admin/comment/{id}/edit', function($id, Request $request) use ($app) {
    $comment = $app['dao.comment']->find($id);
    $commentForm = $app['form.factory']->create(new CommentType, $comment);
    $commentForm->handleRequest($request);
    if ($commentForm->isSubmitted() && $commentForm->isValid()) {
        $app['dao.comment']->save($comment);
        $app['session']->getFlashBag()->add('success', 'The comment was succesfully updated.');
    }

    return $app['twig']->render('comment_form.html.twig', array(
                'title'       => 'Edit comment',
                'commentForm' => $commentForm->createView(),
    ));
})->bind('admin_comment_edit');

// Remove a comment
$app->get('/admin/comment/{id}/delete', function($id, Request $request) use ($app) {
    $app['dao.comment']->delete($id);
    $app['session']->getFlashBag()->add('success', 'The comment was succesfully removed');

    // Redirect to admin home page
    return $app->redirect($app['url_generator']->generate('admin'));
})->bind('admin_comment_delete');


// Add a user
$app->match('/admin/user/add', function(Request $request) use ($app) {
    $user = new User();
    $userForm = $app['form.factory']->create(new UserType(), $user);
    $userForm->handleRequest($request);
    if ($userForm->isSubmitted() && $userForm->isValid()) {
        // generate a random salt
        $salt = substr(md5(time()), 0, 23);
        $user->setSalt($salt);
        $plainPassword = $user->getPassword();
        // find the default encoder
        $encoder = $app['security.encoder.digest'];
        // computer the encoded password
        $password = $encoder->encodePassword($plainPassword, $user->getSalt());
        $user->setPassword($password);
        $app['dao.user']->save($user);
        $app['session']->getFlashBag()->add('success', 'The user was succesfully created.');
    }

    return $app['twig']->render('user_form.html.twig', array(
                'title'    => 'New user',
                'userForm' => $userForm->createView()
    ));
})->bind('admin_user_add');

// Edit an existing user
$app->match('/admin/user/{id}/edit', function($id, Request $request) use ($app) {
    $user = $app['dao.user']->find($id);
    $userForm = $app['form.factory']->create(new UserType(), $user);
    $userForm->handleRequest($request);
    if ($userForm->isSubmitted() && $userForm->isValid()) {
        $plainPassword = $user->getPassword();
        // find the encoder for the user
        $encoder = $app['security.encoder_factory']->getEncoder($user);
        // compute the encoded password
        $password = $encoder->encodePassword($plainPassword, $user->getSalt());
        $user->setPassword($password);
        $app['dao.user']->save($user);
        $app['session']->getFlashBag()->add('success', 'The user was succesfully update.');
    }

    return $app['twig']->render('user_form.html.twig', array(
                'title'    => 'Edit user',
                'userForm' => $userForm->createView(),
    ));
})->bind('admin_user_edit');

// Remove a user
$app->get('/admin/user/{id}/delete', function($id, Request $request) use ($app) {
// Delete all associated comments
    $app['dao.comment']->deleteAllByUser($id);
// Delete the user
    $app['dao.user']->delete($id);
    $app['session']->getFlashBag()->add('success', 'The user was succesfully removed.');

// Redirect to admin home page
    return $app->redirect($app['url_generator']->generate('admin'));
})->bind('admin_user_delete');


// API : Get all article
$app->get('/api/articles', function () use($app) {
    $articles = $app['dao.article']->findAll();
    // convert an array of objects ($articles) into an associative arrays ($responseData)
    $responseData = array();
    foreach ($articles as $article) {
        $responseData[] = array(
            'id'      => $article->getId(),
            'title'   => $article->getTitle(),
            'content' => $article->getContent(),
        );
    }
    // Create and return a JSON response
    return $app->json($responseData);
})->bind('api_articles');

// API : Get an article
$app->get('/api/article/{id}', function($id) use ($app) {
    $article = $app['dao.article']->find($id);
    // convert an object ($article) to an associative array ($responseData)
    $responseData = array(
        'id'      => $article->getId(),
        'title'   => $article->getTitle(),
        'content' => $article->getContent(),
    );
    // Create and return a JSON response
    return $app->json($responseData);
})->bind('api_article');

// API : Create a new article
$app->post('/api/article', function(Request $request) use ($app) {
    // check request parameters
    if (!$request->request->has('title')) {
        return $app->json('Missing required parameter : title', 400);
    }
    if (!$request->request->has('content')) {
        return $app->json('Missing required parameter : content', 400);
    }
    // Build and save the new article
    $article = new Article();
    $article->setTitle($request->request->get('title'));
    $article->setContent($request->request->get('content'));
    $app['dao.article']->save($article);
    // convert an object ($article) to an associative array ($responseData)
    $responseData = array(
        'id'      => $article->getId(),
        'title'   => $article->getTitle(),
        'content' => $article->getContent(),
    );
    return $app->json($responseData, 201); // 201 = created
})->bind('api_article_add');

// API : Delete an existing article
$app->delete('/api/article/{id}', function($id, Request $request) use ($app) {
    // Delete all associated comment
    $app['dao.comment']->deleteAllByArticle($id);
    // Delete the article
    $app['dao.article']->delete($id);
    return $app->json('No Content', 204); // 204 = No content
})->bind('api_article_delete');

// Register JSON data decoder for JSON request
$app->before(function (Request $request) {
    if (0 === strpos($request->headers->get('Content-Type'), 'application/json')) {
        $data = json_decode($request->getContent(), true);
        $request->request->replace(is_array($data) ? $data : array());
    }
});
