<?php

namespace App\Controller;
use App\Form\UserSubscriptionType;
use App\Entity\UserSubscription;
use Stripe\Customer;
use Stripe\Stripe;
use Stripe\Subscription;
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
     * @throws \Stripe\Exception\ApiErrorException
     */
    public function new(Request $request)
    {
        // creates a userSubscription object and initializes some data for this example
        $userSubscription = new UserSubscription();

        $form = $this->createForm(UserSubscriptionType::class, $userSubscription);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {

            $userSubscription = $form->getData();

            Stripe::setApiKey('sk_test_0V6MS5gcKG7l3dmk7htPzLLs00CBLB0DQH');

              try {

                  $customer = Customer::retrieve($this->getUser()->getStripeCustomer());
              }

              catch (\Exception $e){

            $customer = Customer::create([
                'payment_method' => $form->get("paiementId")->getData(),
                'email' => $this->getUser()->getEmail(),
                'invoice_settings' => [
                    'default_payment_method' => $form->get("paiementId")->getData()
                ]
            ]);
        }


            $subscription = \Stripe\Subscription::create([
                'customer' => $customer->id,
                'items' => [
                    [
                        'price' => $userSubscription->getSubscription()->getStripePlan(),
                    ],
                ],
                'expand' => ['latest_invoice.payment_intent'],
            ]);



            $this->getUser()->setStripeCustomer($customer->id);

            $userSubscription->setDateBeginning(new \DateTime());
            $userSubscription->setUser($this->getUser());
            $userSubscription->setStripeSubscription($subscription->id);

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($userSubscription);
            $entityManager->flush();

            return $this->redirectToRoute('app_confirm_user_subscription');
        }

        return $this->render('user/userSubscription.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/confirm_user_subscription", name="app_confirm_user_subscription")
     * @param Request $request
     * @return RedirectResponse|Response
     * @throws \Stripe\Exception\ApiErrorException
     */
    public function confirm(Request $request)
    {
        \Stripe\Stripe::setApiKey('sk_test_0V6MS5gcKG7l3dmk7htPzLLs00CBLB0DQH');

        $subscription = $this->getUser()->getUserSubscription()->getStripeSubscription();
        $subscription = \Stripe\Subscription::retrieve($subscription);

        if($subscription->status=="active")
        return $this->render('user/confirm_userSubscription.html.twig');
        else
        return $this->render('user/userSubscription.html.twig');

    }

    /**
     * @Route("/update_user_subscription", name="app_update_user_subscription")
     * @param Request $request
     * @return RedirectResponse|Response
     * @throws \Stripe\Exception\ApiErrorException
     */
    public function update(Request $request)
    {
        \Stripe\Stripe::setApiKey('sk_test_0V6MS5gcKG7l3dmk7htPzLLs00CBLB0DQH');

        $userSubscription = $this->getUser()->getUserSubscription();

        $form = $this->createForm(UserSubscriptionType::class, $userSubscription);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            /** @var UserSubscription $userSubscription */
            $userSubscription = $form->getData();

            $subscription = \Stripe\Subscription::retrieve($userSubscription->getStripeSubscription());

            \Stripe\Subscription::update($subscription->id, [
                'cancel_at_period_end' => false,
                'proration_behavior' => 'create_prorations',
                'items' => [
                    [
                        'id' => $subscription->items->data[0]->id,
                        'price' => $subscription->price,
                    ],
                ],
            ]);

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($userSubscription);
            $entityManager->flush();

            return $this->redirectToRoute('app_confirm_user_subscription');
        }

        return $this->render('user/update_userSubscription.html.twig', [
            'form' => $form->createView(),
        ]);

    }
    /**
     * @Route("/cancel_user_subscription", name="app_cancel_user_subscription")
     * @param Request $request
     * @return RedirectResponse|Response
     * @throws \Stripe\Exception\ApiErrorException
     */
    public function cancel(Request $request)
    {
        \Stripe\Stripe::setApiKey('sk_test_0V6MS5gcKG7l3dmk7htPzLLs00CBLB0DQH');

        $userSubscription = $this->getUser()->getUserSubscription();


        if ($request->getMethod() == Request::METHOD_POST) {
            $subscription = \Stripe\Subscription::retrieve($userSubscription->getStripeSubscription());
            $subscription->cancel();

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($userSubscription);
            $entityManager->flush();

            return $this->redirectToRoute('app_home');
        }

        return $this->render('user/cancel_userSubscription.html.twig', [
            'subscription'=>$userSubscription
        ]);

    }
}