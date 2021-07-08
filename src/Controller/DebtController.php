<?php
declare(strict_types = 1);

namespace App\Controller;

use App\Entity\Debt;
use App\Form\DebtType;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;


class DebtController extends AbstractController
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * @Route("/debt", name="debt")
     */
    public function new(Request $request): Response
    {
        $debt = new Debt();

        $form = $this->createForm(DebtType::class, $debt);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $debt = $form->getData();
            $this->entityManager->persist($debt);
            $this->entityManager->flush();
            return $this->redirectToRoute('list');
        }

        return $this->render('debt/new.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
