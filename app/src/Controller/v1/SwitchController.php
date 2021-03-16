<?php

namespace App\Controller\v1;

use App\Entity\ResourceSwitch;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/switch")
 */
class SwitchController extends AbstractFOSRestController
{

    private $logger;

    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;    
    }

    /**
     * @Rest\Post(name="post_switch")
     * 
     * @return Response
     */
    public function postSwitch()
    {
        $token = $this->getUser()->getToken();
        $em = $this->getDoctrine()->getManager();
        $switch = $em->getRepository(ResourceSwitch::class)->findBy(["token" => $token]);
        if (count($switch) === 0) {
            $switch = new ResourceSwitch();
            $switch->setToken($token);
            $em->persist($switch);
            $em->flush();
        }
        return $this->getViewHandler()->handle($this->view(["result" => [["token" => true]]]));
    }

    /**
     * @Rest\Delete(name="delete_switch")
     * 
     * @return Response
     */
    public function deleteSwitch()
    {
        $token = $this->getUser()->getToken();
        $em = $this->getDoctrine()->getManager();
        $this->logger->info($token);
        $switches = $em->getRepository(ResourceSwitch::class)->findBy(["token" => $token]);
        if (count($switches) > 0) {
            foreach ($switches as $switch) {
                $em->remove($switch);
            }
            $em->flush();
        }
        return $this->getViewHandler()->handle($this->view(["result" => [["token" => false]]]));
    }
}
