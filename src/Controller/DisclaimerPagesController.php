<?php

declare(strict_types=1);

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DisclaimerPagesController extends AbstractController
{
    /**
     * @Route("/privacy-policy", name="privacy_policy")
     */
    public function privacyPolicy(): Response
    {
        return $this->render('Disclaimers/privacy.html.twig');
    }

    /**
     * @Route("/cookie-policy", name="cookie_policy")
     */
    public function cookiePolicy(): Response
    {
        return $this->render('Disclaimers/cookies.html.twig');
    }

    /**
     * @Route("/disclaimer", name="disclaimer")
     */
    public function disclaimer(): Response
    {
        return $this->render('Disclaimers/disclaimer.html.twig');
    }
}
