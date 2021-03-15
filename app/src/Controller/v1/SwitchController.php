<?php

namespace App\Controller\v1;

use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/switch")
 */
class SwitchController extends AbstractFOSRestController
{

    /**
     * 
     * @Rest\Get(name="switch")
     * 
     * @return Response
     */
    public function getSwitches()
    {
        return $this->getViewHandler()->handle($this->view(["data" => [1, 2, 3]]));
    }

    /**
     * @Rest\Post(name="post_switch")
     * 
     * @return Response
     */
    public function postSwitch()
    {
        return $this->getViewHandler()->handle($this->view(["data" => [1, 2, 3]]));
    }
}
