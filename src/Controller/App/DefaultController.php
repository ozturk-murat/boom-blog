<?php

namespace App\Controller\App;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DefaultController extends AbstractController
{
    /**
     * @Route("/", name="index")
     * @Template()
    */
    public function index()
    {
        return[
          "title" => "Home",
        ];
    }

    /**
     * @Route("/contact", name="contact")
     * @Template()
    */
    public function contact()
    {
        return[
            "title" => "Contact",
        ];
    }


}
