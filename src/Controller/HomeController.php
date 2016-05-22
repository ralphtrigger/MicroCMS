<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace MicroCMS\Controller;

use MicroCMS\Domain\Comment;
use MicroCMS\Form\Type\CommentType;
use Silex\Application;
use Symfony\Component\HttpFoundation\Request;

/**
 * Description of HomeController
 *
 * @author trigger
 */
class HomeController
{
    /**
     * Home page controller.
     * 
     * @param Application $app Silex application
     */
    public function indexAction(Application $app)
    {
        $articles = $app['dao.article']->findAll();
        return $app['twig']->render('index.html.twig', array(
                    'articles' => $articles
        ));
    }

    /**
     * Article details controller.
     * 
     * @param int $id Article id.
     * @param Request $request Incomming request
     * @param \Silex\Appliclation $app Silex application
     */
    public function articleAction($id, Request $request, Application $app)
    {
        $article = $app['dao.article']->find($id);
        $commentFormView = null;
        if ($app['security.authorization_checker']->isGranted('IS_AUTHENTICATED_FULLY')) {
            // A user is fully authenticated : he can add a comment
            $comment = new Comment();
            $comment->setArticle($article);
            $user = $app['user'];
            $comment->setAuthor($user);
            $commentForm = $app['form.factory']->create(new CommentType(), $comment);
            $commentForm->handleRequest($request);
            if ($commentForm->isSubmitted() && $commentForm->isValid()) {
                $app['dao.comment']->save($comment);
                $app['session']->getFlashBag()->add('success', 'Your comment was succesfully added.');
            }
            $commentFormView = $commentForm->createView();
        }
        $comments = $app['dao.comment']->findAllByArticle($id);

        return $app['twig']->render('article.html.twig', array(
                    'article'     => $article,
                    'comments'    => $comments,
                    'commentForm' => $commentFormView,));
    }

    /**
     * User login controller.
     * 
     * @param Request $request Incomming request
     * @param Application $app Silex application
     */
    public function loginAction(Request $request, Application $app)
    {
        return $app['twig']->render('login.html.twig', array(
                    'error'         => $app['security.last_error']($request),
                    'last_username' => $app['session']->get('_security.last_username'),
        ));
    }

}
