<?php

namespace App\Controller\Admin;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/admin")
 */
class DefaultController extends AbstractController
{
    /**
     * @Route("/", name="admin_index")
     * @Template()
    */
    public function index()
    {
        return[
            "title" => "Dashboard"
        ];
    }

    /**
     * @Route("/admin_users", name="admin_users")
     * @Template()
     */
    public function users()
    {
        return[
            "title" => "Users"
        ];
    }
}
