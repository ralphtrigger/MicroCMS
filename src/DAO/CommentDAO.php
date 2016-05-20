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
class CommentDAO extends DAO
{
    /**
     *
     * @var \MicroCMS\DAO\ArticleDAO
     */
    private $articleDAO;

    /**
     *
     * @var \MicroCMS\DAO\UserDAO
     */
    private $userDAO;

    public function setArticleDAO(ArticleDAO $articleDAO)
    {
        $this->articleDAO = $articleDAO;
    }

    public function setUserDAO(UserDAO $userDAO)
    {
        $this->userDAO = $userDAO;
    }

    /**
     * Return a list of all comments for an article, sorted by name (most recent first).
     * 
     * @param integer $articleId The article id.
     * 
     * return array The list of all comments for the article.
     * 
     */
    public function findAllByArticle($articleId)
    {
        // The associated article is retrieved only once.
        $article = $this->articleDAO->find($articleId);

        // art_id is not selected by the query.
        // The article won't be retrieved during domain object construction.
        $sql = "select com_id, com_content, usr_id from t_comment "
                ."where art_id=? order by com_id";
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

    public function save(Comment $comment)
    {
        $commentData = array(
            'art_id' => $comment->getArticle()->getId(),
            'usr_id' => $comment->getAuthor()->getId(),
            'com_content' => $comment->getContent()
        );

        if ($comment->getId()) {
            // The comment has already been saved : update it
            $this->getDb()->update('t_comment', $commentData, array(
                'com_id' => $comment->getId()
            ));
        }
        else {
            // The comment has never been saved : insert it
            $this->getDb()->insert('t_comment', $commentData);
            // Get the id of the newly created comment and set it on the entity.
            $id = $this->getDb()->lastInsertId();
            $comment->setId($id);
        }
    }

    /**
     * Return a list of all comments, sorted by date (most recent first)
     * 
     * @return array A list of all comment.
     */
    public function findAll()
    {
        $sql = "select * from t_comment order by com_id desc";
        $result = $this->getDb()->fetchAll($sql);

        // convert query result to an array of domain object
        $entities = array();
        foreach ($result as $row) {
            $id = $row['com_id'];
            $entities[$id] = $this->buildDomainObject($row);
        }

        return $entities;
    }

    /**
     * Remove all comments for an article.
     * 
     * @param int $articleId The id of the article.
     */
    public function deleteAllByArticle($articleId)
    {
        $this->getDb()->delete('t_comment', array('art_id' => $articleId));
    }

    /**
     * Remove all comments for a user.
     * 
     * @param int $userId The id of the article.
     */
    public function deleteAllByUser($userId)
    {
        $this->getDb()->delete('t_comment', array('usr_id' => $userId));
    }

    /**
     * Return a comment matchin the supplied id.
     * 
     * @param int $id The comment id.
     * @return \MicroCMS\DAO\Comment 
     * @throws Exception throw an exception if no matching comment is found
     */
    public function find($id)
    {
        $sql = "select * from t_comment where com_id=?";
        $row = $this->getDb()->fetchAssoc($sql, [$id]);

        if ($row) {
            return $this->buildDomainObject($row);
        }
        else {
            throw new \Exception("No comment matching id ".$id);
        }
    }

    /**
     * Remove a comment from the database.
     * 
     * @param int $id
     */
    public function delete($id)
    {
        // Delete the comment
        $this->getDb()->delete('t_comment', array('com_id' => $id));
    }

    /**
     * Create a comment object base on a DB row
     * 
     * @param array $row The DB row containing Comment data.
     * @return Comment
     * 
     */
    protected function buildDomainObject($row)
    {
        $comment = new Comment();
        $comment->setId($row['com_id']);
        $comment->setContent($row['com_content']);

        if (array_key_exists('art_id', $row)) {
            // Find and set the associated article
            $articleId = $row['art_id'];
            $article = $this->articleDAO->find($articleId);
            $comment->setArticle($article);
        }

        if (array_key_exists('usr_id', $row)) {
            // Find and set the associated author
            $userId = $row['usr_id'];
            $user = $this->userDAO->find($userId);
            $comment->setAuthor($user);
        }

        return $comment;
    }

}
