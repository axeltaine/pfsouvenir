<?php

namespace App\Controller;

use App\Entity\Article;
use App\Entity\Comment;
use App\Entity\Contact;
use App\Form\ArticleType;
use App\Form\CommentType;
use App\Form\ContactType;
use App\Repository\ArticleRepository;
use Doctrine\ORM\EntityManagerInterface;
use App\Notification\ContactNotification;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class BlogController extends AbstractController
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
    public function home(Request $request, ContactNotification $notification) {
        $contact = new Contact ();
        $form = $this->createForm(ContactType::class, $contact);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()){
            $notification->notify($contact);
            $this->addFlash('success', 'Votre email a bien ete envoye');
            return $this->redirectToRoute('home');
        }

        return $this->render('blog/home.html.twig', [
            'form' => $form->createView()
        ]);
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
    public function contact(Request $request, ContactNotification $notification) {
        $contact = new Contact ();
        $form = $this->createForm(ContactType::class, $contact);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()){
            $notification->notify($contact);
            $this->addFlash('success', 'Votre email a bien ete envoye');
            return $this->redirectToRoute('home');
        }
        return $this->render('blog/contact.html.twig'
        , [
            'form' => $form->createView()
        ]);
    }
     /**
     * @Route("/avis-de-deces/new", name="create_deces")
    * @Route("/avis-de-deces/{id}/edit", name="edit_deces")
     */
    public function create(Article $article = null, Request $request, EntityManagerInterface $entityManager) {
        if(!$article){
            $article = new Article();
        }
        
        $form = $this->createForm(ArticleType::class, $article);

        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()) {
            $uploadedFile = $form['image']->getData();
            if ($uploadedFile) {
                $destination = $this->getParameter('kernel.project_dir').'/public/img/';
                $originalFilename = pathinfo($uploadedFile->getClientOriginalName(), PATHINFO_FILENAME);
                $newFilename = $originalFilename.'-'.uniqid().'.'.$uploadedFile->guessExtension();
                $uploadedFile->move(
                    $destination,
                    $newFilename
                );
            if(!$article->getId()){
                $article->setCreatedAt(new \DateTime());
            }
            
            $article->setImage($newFilename);
            $entityManager->persist($article);
            $entityManager->flush();

            return $this->redirectToRoute('blog_show', ['id' => $article->getId()]);
        }}

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

    /**
     * @Route("/connexion", name="security_login")
     */
   public function login(){
       return $this->render('blog/login.html.twig');
   }

   /**
    * @Route("/deconnexion", name="security_logout")
    */
    public function logout() {}




/**
     * @Route("/obseques", name="obseques")
     */

    public function obseques() {
        return $this->render('blog/obseques.html.twig');
    }

/**
     * @Route("/transport", name="transport")
     */

    public function transport() {
        return $this->render('blog/transport.html.twig');
    }



/**
     * @Route("/administratives", name="administratives")
     */

    public function administratives() {
        return $this->render('blog/administratives.html.twig');
    }
    /**
     * @Route("/marbrerie", name="marbrerie")
     */

    public function marbrerie() {
        return $this->render('blog/marbrerie.html.twig');
    }
   
     /**
     * @Route("/fournitures", name="fournitures")
     */

    public function fournitures() {
        return $this->render('blog/fournitures.html.twig');
    }

    /**
     * @Route("/delete/{id}" , name="delete")
     * @Method({"DELETE"})
     */
    public function delete(Comment $comment, EntityManagerInterface $entityManager) {
        $entityManager -> remove($comment);
        
        $entityManager->flush();
        $this->addFlash('success', "ok");
        return $this->redirectToRoute('blog');
      }

      /**
     * @Route("/avis-de-deces/delete/{id}" , name="delete_article")
     * @Method({"DELETE"})
     */
    public function deletearticle (Article $article, Request $request, EntityManagerInterface $entityManager, $id) {
        $article = $this->getDoctrine()->getRepository(article::class)->find($id);
        $entityManager = $this->getDoctrine()->getManager();
        foreach($article->getComments() as $comments){
            $entityManager->remove($comments);
        }

        $entityManager->remove($article);
        
        $entityManager->flush();
        $this->addFlash('success', "ok");
        return $this->redirectToRoute('blog');
      }
}