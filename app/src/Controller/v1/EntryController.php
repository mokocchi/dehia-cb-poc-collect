<?php

namespace App\Controller\v1;

use App\Entity\ResourceSwitch;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/entry")
 */
class EntryController extends AbstractFOSRestController
{

    /**
     * 
     * @Rest\Get(name="entries")
     * 
     * @return Response
     */
    public function getResults()
    {
        $token = $this->getUser()->getToken();
        $em = $this->getDoctrine()->getManager();
        $switch = $em->getRepository(ResourceSwitch::class)->findBy(["token" => $token]);

        if(count($switch) === 0) {
            return $this->getViewHandler()->handle($this->view(["results" => ["updated", "results"]]));
        } else {
            sleep(120);
        }
    }
}
