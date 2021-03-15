<?php

namespace App\Controller\v1;

use App\Entity\ResourceSwitch;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/collect-status")
 */
class StatusController extends AbstractFOSRestController
{

    /**
     * 
     * @Rest\Get(name="collect_status")
     * 
     * @return Response
     */
    public function getStatus()
    {
        $token = $this->getUser()->getToken();
        $em = $this->getDoctrine()->getManager();
        $switch = $em->getRepository(ResourceSwitch::class)->findBy(["token" => $token]);
        if(!isset($switch)) {
            return $this->getViewHandler()->handle($this->view(["status" => "OK"]));
        } else {
            return $this->getViewHandler()->handle($this->view(["status" => "SUSPENDED"]));
        }
    }
}
