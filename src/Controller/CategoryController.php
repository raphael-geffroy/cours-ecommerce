<?php

namespace App\Controller;

use App\Entity\Product;
use App\Entity\Category;
use App\Cart\CartService;
use App\Form\ProductType;
use App\Form\CategoryType;
use App\Repository\CategoryRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Service\Persistence\GlobalDataPersister;
use Symfony\Component\String\Slugger\SluggerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class CategoryController extends AbstractController
{

    /** @var GlobalDataPersister */
    protected $persister;
    /** @var SluggerInterface */
    protected $slugger;

    public function __construct(GlobalDataPersister $persister, SluggerInterface $slugger)
    {
        $this->persister = $persister;
        $this->slugger = $slugger;
    }

    /**
     * @Route("/{slug<[\w-]+>}", name="category_show", priority=-1)
     */
    public function show(Category $category)
    {

        if (!$category) throw $this->createNotFoundException("Catégorie inexistante !");
        return $this->render('category/show.html.twig', [
            'category' => $category,
        ]);
    }

    /**
     * @Route("/admin/category/create", name="category_create")
     */
    public function create(Request $request)
    {
        $category = new Category();
        $form = $this->createForm(CategoryType::class, $category);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $category->setSlug(strtolower($this->slugger->slug($category->getName())));
            $this->persister->persist($category);
            return $this->redirectToRoute('homepage');
        }
        return $this->render('category/create.html.twig', [
            'formView' => $form->createView()
        ]);
    }

    /**
     * @Route("/admin/category/{id<\d+>}/edit",name="category_edit")
     */
    public function edit(Category $category, Request $request)
    {
        $this->denyAccessUnlessGranted('CAN_EDIT', $category);
        $form = $this->createForm(CategoryType::class, $category);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $category->setSlug(strtolower($this->slugger->slug($category->getName())));
            $this->persister->flush();
            return $this->redirectToRoute('homepage');
        }
        return $this->render('category/edit.html.twig', [
            'formView' => $form->createView()
        ]);
    }
}
