<?php
declare(strict_types = 1);

namespace App\Controller;

use App\Entity\Debt;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Session\SessionInterface;


class ListController extends AbstractController
{
    private $entityManager;
    private $session;

    public function __construct(EntityManagerInterface $entityManager, SessionInterface $session)
    {
        $this->entityManager = $entityManager;
        $this->session = $session;
    }


    /**
     * @Route("/list", name="list")
     */
    public function index(Request $request): Response
    {
        $limits = [
            'date_from' => $this->session->get('date_from', new \DateTime('5 days ago')),
            'date_to'   => $this->session->get('date_to',   new \DateTime('+5 days')),
            'symbol'    => $this->session->get('symbol', ''),
        ];
        $form = $this->createFormBuilder($limits)
            ->add('symbol',    TextType::class, ['required' => false])
            ->add('date_from', DateType::class, ['widget' => 'single_text'])
            ->add('date_to',   DateType::class, ['widget' => 'single_text'])
            ->add('search',    SubmitType::class, ['label' => 'Search'])
            ->add('delete',    SubmitType::class, ['label' => 'Delete selected'])
            ->getForm();

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $limits = $form->getData();
            $tick = $this->sanitizeTick($request->request->get('tick'));
            if ($form->get('delete')->isClicked()) {
                $this->entityManager->getRepository(Debt::class)->deletByIdArray($tick);
            }
            $this->session->set('date_from', $limits['date_from']);
            $this->session->set('date_to',   $limits['date_to']);
            $this->session->set('symbol',    $limits['symbol']);
        }
        else {
            $tick = [];
        }

        $list = $this->entityManager->getRepository(Debt::class)->findByDate($limits['date_from'], $limits['date_to'], $limits['symbol']);

        return $this->render('list/index.html.twig', [
            'form' => $form->createView(),
            'list' => $list,
            'tick' => $tick,
        ]);
    }


    private function sanitizeTick($T) : array
    {
        $RET = [];
        if ( ! is_array($T) ) {
            return $RET;
        }
        foreach ($T as $id) {
            if ( ! preg_match("/^[0-9]+$/", $id) ) {
                continue;
            }
            $RET[] = $id;
        }
        return $RET;
    }
}
