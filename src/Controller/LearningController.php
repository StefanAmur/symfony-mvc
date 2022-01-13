<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Validator\Constraints\Date;

class LearningController extends AbstractController
{

    private $requestStack;

    public function __construct(RequestStack $requestStack)
    {
        $this->requestStack = $requestStack;
    }

    #[Route('/', name: 'showMyName')]
    public function showMyName(Request $request): Response
    {
        $form = $this->createFormBuilder()
            ->add('name', TextType::class)
            ->add('save', SubmitType::class, ['label' => 'Submit'])
            ->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid())
        {
            $task = $form->getData();
            $name = $task['name'];
            $session = $this->requestStack->getSession();
            $session->set('name', $name);

            return $this->render('learning/showMyName.html.twig', [
                'name' => $name,
                'form' => $form->createView(),
            ]);
        }

        return $this->render('learning/showMyName.html.twig', [
            'name' => 'unknown',
            'form' => $form->createView(),
        ]);
    }

    #[Route('/aboutMe', name: 'aboutMe')]
    public function aboutMe(): Response
    {
        $session = $this->requestStack->getSession();
        $name = $session->get('name', '');
        if (empty($name))
        {
            return $this->forward('App\Controller\LearningController::showMyName');
        }
        else
        {
            return $this->render('learning/aboutMe.html.twig', [
                'name' => $name,
                'date' => date('Y/M/D'),
            ]);
        }
    }

    // wip redirect to /change-my-name. not sure I fully understand the requirement or why this is necessary
    #[Route('/change-my-name', name: 'change-my-name')]
    public function changeMyName(Request $request): Response
    {
        return $this->redirectToRoute('showMyName');
    }
}
