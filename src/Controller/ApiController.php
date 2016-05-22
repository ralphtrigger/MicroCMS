<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace MicroCMS\Controller;

use MicroCMS\Domain\Article;
use Silex\Application;
use Symfony\Component\HttpFoundation\Request;

/**
 * Description of ApiController
 *
 * @author trigger
 */
class ApiController
{
    /**
     * API article controller.
     * 
     * @param \MicroCMS\Controller\Application $app Silex application
     * 
     * @return all articles in JSON format
     */
    public function getArticlesAction(Application $app)
    {
        $articles = $app['dao.article']->findAll();
        // Convert an array ($articles) into an array of associative arrays ($responseData)
        $responseData = array();
        foreach ($articles as $articles) {
            $responseData[] = $this->buildArticleArray($articles);
        }
        // Create and return JSON response
        return $app->json($responseData);
    }

    /**
     * API : article details controller.
     * 
     * @param int $id Article id
     * @param \MicroCMS\Controller\Application $app Silex application
     * 
     * @return Article details in JSON format
     */
    public function getArticleAction($id, Application $app)
    {
        $article = $app['dao.article']->find($id);
        $responseData = $this->buildArticleArray($article);
        // Create and return a JSON response
        return $app->json($responseData);
    }

    /**
     * API create article controller.
     * 
     * @param \MicroCMS\Controller\Request $request Incomming request
     * @param \MicroCMS\Controller\Application $app Silex application
     * 
     * @return Article details in JSON format
     */
    public function addArticleAction(Request $request, Application $app)
    {
        // check request parameters
        if (!$request->request->has('title')) {
            return $app->json('Missing required parameter: title', 400);
        }
        if (!$request->request->has('content')) {
            return $app->json('Missing required parameter: content', 400);
        }
        // Build and save the new article
        $article = new Article();
        $article->setTitle($request->request->get('title'));
        $article->setContent($request->request->get('content'));
        $app['dao.article']->save($article);
        $responseData = $this->buildArticleArray($article);
        return $app->json($responseData, 201); // 201 = Created
    }

    /**
     * API delete article controller.
     * 
     * @param int $id Article id
     * @param \MicroCMS\Controller\Application $app Silex application
     */
    public function deleteArticleAction($id, Application $app)
    {
        // Delete all associated comments
        $app['dao.comment']->deleteAllByArticle($id);
        // Delete the article
        $app['dao.article']->delete($id);
        return $app->json('No content', 204); // 204 = No content
    }

    /**
     * Converts an Article object into associative array for JSON encoding
     * 
     * @param Article $article Article object
     * 
     * @return array Associative array whose field are the article properties.
     */
    private function buildArticleArray(Article $article)
    {
        $data = array(
            'id'      => $article->getId(),
            'title'   => $article->getTitle(),
            'content' => $article->getContent(),
        );

        return $data;
    }

}
