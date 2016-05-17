<?php

namespace MicroCMS\DAO;

use MicroCMS\Domain\Article;

/**
 *
 * @author trigger
 *        
 */
class ArticleDAO extends DAO {

    /**
     * Return a list of all articles, sorted by date (most recent first).
     *
     * @return array A list of all articles.
     */
    public function findAll() {
        $sql = "select * from t_article order by art_id desc";
        $result = $this->getDb()->fetchAll($sql);

        // convert query result to an array of domain objects
        $articles = array();
        foreach ($result as $row) {
            $articleId = $row['art_id'];
            $articles[$articleId] = $this->buildDomainObject($row);
        }
        return $articles;
    }
    /**
     * Return an article matching the supplied id.
     * 
     * @param int $id
     * @return \MicroCMS\Domain\Article
     * @throws Exception If not matching article is found
     */
    public function find($id) {
        $sql = "select * from t_article where art_id=?";
        $row = $this->getDb()->fetchAssoc($sql, array($id));

        if ($row) {
            return $this->buildDomainObject($row);
        } else {
            throw new Exception("No article matching id " . $id);
        }
    }

    /**
     * Create an Article object base on a DB row.
     *
     * @param array $row
     *            The DB row containing Article data.
     * @return Article
     */
    protected function buildDomainObject($row) {
        $article = new Article();
        $article->setId($row['art_id']);
        $article->setTitle($row['art_title']);
        $article->setContent($row['art_content']);

        return $article;
    }

}
