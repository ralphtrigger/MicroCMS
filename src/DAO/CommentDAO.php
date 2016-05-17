<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace MicroCMS\DAO;

use MicroCMS\Domain\Comment;

/**
 * Description of CommentDAO.
 *
 * @author trigger
 */
class CommentDAO extends DAO {

    /**
     *
     * @var ArticleDAO
     */
    private $articleDAO;

    public function setArticleDAO(ArticleDAO $articleDAO) {
        $this->articleDAO = $articleDAO;
    }

    /**
     * Return a list of all comments for an article, sorted by name (most recent first).
     * 
     * @param integer $articleId The article id.
     * 
     * return array The list of all comments for the article.
     * 
     */
    public function findAllByArticle($articleId) {
        // The associated article is retrieved only once.
        $article = $this->articleDAO->find($articleId);

        // art_id is not selected by the query.
        // The article won't be retrieved during domain object construction.
        $sql = "select com_id, com_author, com_content from t_comment "
                . "where art_id=? order by com_id";
        $result = $this->getDb()->fetchAll($sql, array($articleId));

        // Convert query result to an array of domain objects
        $comments = array();
        foreach ($result as $row) {
            $comId = $row['com_id'];
            $comment = $this->buildDomainObject($row);
            // The associated article is defined for the constructed comment
            $comment->setArticle($article);
            $comments[$comId] = $comment;
        }
        
        return $comments;
    }

    /**
     * Create a comment object base on a DB row
     * 
     * @param array $row The DB row containing Comment data.
     * @return Comment
     * 
     */
    protected function buildDomainObject($row) {
        $comment = new Comment();
        $comment->setId($row['com_id']);
        $comment->setAuthor($row['com_author']);
        $comment->setContent($row['com_content']);
        
        if(array_key_exists('art_id', $row)){
            // Find and set the associated article
            $articleId =$row['art_id'];
            $article = $this->articleDAO->find($articleId);
            $comment->setArticle($article);
        }
        
        return $comment;
    }

}
