<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace MicroCMS\Domain;

/**
 * Description of Comment
 *
 * @author trigger
 */
class Comment
{
    /**
     * Comment id.
     * 
     * @var integer
     */
    private $id;

    /**
     * Comment author.
     * 
     * @var MicroCMS/Domain/User
     */
    private $author;

    /**
     * Comment content.
     * 
     * @var string
     */
    private $content;

    /**
     * Associated article.
     * 
     * @var MicroCMS\Domain\Article
     */
    private $article;

    public function getId()
    {
        return $this->id;
    }

    public function getAuthor()
    {
        return $this->author;
    }

    public function getContent()
    {
        return $this->content;
    }

    public function getArticle()
    {
        return $this->article;
    }

    public function setId($id)
    {
        $this->id = $id;
    }

    public function setAuthor(User $author)
    {
        $this->author = $author;
    }

    public function setContent($content)
    {
        $this->content = $content;
    }

    public function setArticle($article)
    {
        $this->article = $article;
    }

}
