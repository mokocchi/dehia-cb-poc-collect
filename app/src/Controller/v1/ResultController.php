<?php

namespace App\Controller\v1;

use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/result")
 */
class ResultController extends AbstractFOSRestController
{

    /**
     * 
     * @Rest\Get(name="result")
     * 
     * @return Response
     */
    public function getResults()
    {
        $switch = true;
        if($switch) {
            return $this->getViewHandler()->handle($this->view(["updated results"]));
        } else {
            sleep(120);
        }
    }
}
