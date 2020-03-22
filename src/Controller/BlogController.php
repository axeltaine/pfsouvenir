<?php

namespace App\Controller;

use App\Entity\Article;
use App\Entity\Comment;
use App\Form\ArticleType;
use App\Form\CommentType;
use App\Repository\ArticleRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class BlogController extends Controller
{
    /**
     * @Route("/avis-de-deces", name="blog")
     */
    public function index(ArticleRepository $repo)
    {

        $articles = $repo->findAll();

        return $this->render('blog/index.html.twig', [
            'controller_name' => 'BlogController',
            'articles' => $articles
        ]);
    }
    /**
     * @Route("/", name="home")
     */
    public function home() {
        return $this->render('blog/home.html.twig');
    }
     /**
     * @Route("/equipe", name="team")
     */
    public function team() {
        return $this->render('blog/team.html.twig');
    }
      /**
     * @Route("/galerie", name="galerie")
     */
    public function galerie() {
        return $this->render('blog/galerie.html.twig');
    }
       /**
     * @Route("/contact", name="contact")
     */
    public function contact() {
        return $this->render('blog/contact.html.twig');
    }
     /**
     * @Route("/avis-de-deces/new", name="create_deces")
     * * @Route("/avis-de-deces/{id}/edit", name="edit_deces")
     */
    public function create(Article $article = null, Request $request, EntityManagerInterface $entityManager) {
        if(!$article){
            $article = new Article();
        }
        
        $form = $this->createForm(ArticleType::class, $article);

        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()) {
            if(!$article->getId()){
                $article->setCreatedAt(new \DateTime());
            }
            

            $entityManager->persist($article);
            $entityManager->flush();

            return $this->redirectToRoute('blog_show', ['id' => $article->getId()]);
        }

        return $this->render('blog/create.html.twig', [
            'formArticle' => $form->createView(),
            'editMode' => $article->getId() !== null
        ]);
    }

    /**
     * @Route("/avis-de-deces/{id}", name="blog_show")
     */
    public function show(Article $article, Request $request, EntityManagerInterface $entityManager){
        $comment = new Comment();
        $form = $this->createForm(CommentType::class, $comment);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()) {
            $comment->setCreatedAt(new \DateTime())
                    ->setArticle($article);
            $entityManager->persist($comment);
            $entityManager->flush();
            

            return $this->redirectToRoute('blog_show', ['id' => $article->getId()]);
        }

        return $this->render('blog/show.html.twig', [
            'article' => $article,
            'commentForm' => $form->createView()
        ]);
    }
   

}
