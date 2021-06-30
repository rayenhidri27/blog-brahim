<?php

namespace App\Controller;

use App\Entity\Article;
use App\Entity\Category;
use App\Repository\ArticleRepository;
use App\Repository\CategoryRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class HomeController extends AbstractController
{
    private $repoArticle;
    private $repoCategory;

    public function __construct(ArticleRepository $repoArticle, CategoryRepository $repoCategory)
    {
        $this->repoArticle = $repoArticle;
        $this->repoCategory = $repoCategory;
    }

    /**
     * @Route("/", name="home")
     */
    public function index(): Response
    {
        $categories = $this->repoCategory->findAll();
        $articles = $this->repoArticle->findAll();
        
        return $this->render('home/index.html.twig', [
            "articles" => $articles,
            "categories" => $categories
        ]);
    }

    /**
     * @Route("/show/{id}", name="show")
     */
    public function show(Article $article): Response
    {
        if (!$article) {
            $this->redirectToRoute("home");
        }
        return $this->render('show/index.html.twig', [
            "article" => $article
        ]);
    }

    /**
     * @Route("/showArticles/{id}", name="show_article")
     */
    public function showArticles(?Category $category): Response
    {
        if($category)
        {
            $articles = $category->getArticles()->getValues();
        }
        else {
            return $this->redirectToRoute("home");
        }
        return $this->render('home/index.html.twig', [
            "articles" => $articles,
            "categories" => $this->repoCategory->findAll()
        ]);
    }

    /**
     * @Route("/recherche", name="recherche")
     */
    public function recherche(Request $request): Response
    {
        $datePost=date($request->request->get('date'));
        //$request->request : eq POST (au complet)
        //$request->request->get('date')  : eq POST['date']

        $date = \DateTime::createFromFormat("Y-m-d", $datePost);

        //$date = \DateTime::createFromFormat("Y-m-d", date($request->request->get('date')));
        $title= $request->request->get('title');
        
        $articles = $this->repoArticle->findByTitleLike($title, $date);


        return $this->render('home/index.html.twig', [
            "articles" => $articles,
            "categories" => $this->repoCategory->findAll()
        ]);
    }
}
