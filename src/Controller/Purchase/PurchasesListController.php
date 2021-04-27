<?php

namespace App\Controller\Purchase;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class PurchasesListController extends AbstractController
{
    /**
     * @Route("/purchases",name="purchase_index")
     * @IsGranted("ROLE_USER")
     */
    public function index()
    {
        /** @var User */
        $user = $this->getUser();
        return $this->render("purchase/index.html.twig", [
            "purchases" => $user->getPurchases()
        ]);
    }
}
