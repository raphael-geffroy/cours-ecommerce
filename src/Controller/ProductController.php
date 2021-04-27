<?php

namespace App\Controller;

use App\Entity\Product;
use App\Entity\Category;
use App\Form\ProductType;
use App\Repository\ProductRepository;
use App\Repository\CategoryRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Service\Persistence\GlobalDataPersister;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class ProductController extends AbstractController
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
     * @Route("/{category_slug<[\w-]+>}/{slug<[\w-]+>}", name="product_show", priority=-1)
     */
    public function show(Product $product)
    {
        if (!$product) throw $this->createNotFoundException("Produit inexistante !");
        return $this->render('product/show.html.twig', [
            "product" => $product
        ]);
    }

    /**
     * @Route("/admin/product/create", name="product_create")
     */
    public function create(Request $request)
    {
        $product = new Product();
        $form = $this->createForm(ProductType::class, $product);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $product->setSlug($this->slugger->slug($product->getName()));
            $this->persister->persist($product);
            return $this->redirectToRoute('product_show', [
                'category_slug' => $product->getCategory()->getSlug(),
                'slug' => $product->getSlug()
            ]);
        }
        return $this->render('product/create.html.twig', [
            'formView' => $form->createView()
        ]);
    }

    /**
     * @Route("/admin/product/{id<\d+>}/edit",name="product_edit")
     */
    public function edit(Product $product, Request $request)
    {
        $form = $this->createForm(ProductType::class, $product);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $product->setSlug($this->slugger->slug($product->getName()));
            $this->persister->flush();
            return $this->redirectToRoute('product_show', [
                'category_slug' => $product->getCategory()->getSlug(),
                'slug' => $product->getSlug()
            ]);
        }
        return $this->render('product/edit.html.twig', [
            'formView' => $form->createView()
        ]);
    }
}
