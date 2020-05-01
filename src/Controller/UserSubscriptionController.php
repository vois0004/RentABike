<?php

namespace App\Controller;
use App\Form\UserSubscriptionType;
use App\Entity\UserSubscription;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class UserSubscriptionController extends AbstractController
{
    /**
     * @Route("/user_subscription", name="app_user_subscription")
     * @param Request $request
     * @return RedirectResponse|Response
     */
    public function new(Request $request)
    {
        // creates a userSubscription object and initializes some data for this example
        $userSubscription = new UserSubscription();

        $form = $this->createForm(UserSubscriptionType::class, $userSubscription);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $userSubscription = $form->getData();
            $userSubscription->setUser($this->getUser());
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($userSubscription);
            $entityManager->flush();

            return $this->redirectToRoute('app_home');
        }

        return $this->render('user/userSubscription.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}