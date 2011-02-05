<?php
class Mongo_Test_Controller extends MiniMVC_Controller
{
    public function indexAction($params)
    {
//        $author = new MongoExampleAuthor();
//        $author->name = 'Horst';
//        $author->save();


        $article = MongoExampleArticleRepository::get()->findOne(array('slug' => 'test-artikel'));
//        $article = new MongoExampleArticle();
//        $article->title = 'Test-Artikel';
//        $article->content = '<p>Test</p>';
//        $article->save();
//
//        $comment = new MongoEmbeddedComment();
//        $comment->author = 'jesus';
//        $comment->message = 'hi!';
//
//        $article->setEComments($comment);

//        $comment2 = new MongoEmbeddedComment();
//        $comment2->author = 'paul';
//        $comment2->message = 'hi ich bin paul!';
//
//        $comment3 = new MongoEmbeddedComment();
//        $comment3->author = 'peter';
//        $comment3->message = 'hallo paul!';
//
//        $article->setEComments(array($comment2, $comment3));

        //$article->setAuthor($author);
        //$article->removeEComments(5);

        $author = $article->getAuthor();
        $author->name = 'PETER';
        $author->save();
        var_dump($article->getAuthor()->toArray());
        foreach ($article->getEComments() as $c) {
            var_dump($c);
        }

        $this->view->prepareEmpty();
    }
}
