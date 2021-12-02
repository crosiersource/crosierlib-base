<?php

namespace CrosierSource\CrosierLibBaseBundle\Controller\Config\API;

use CrosierSource\CrosierLibBaseBundle\Entity\Config\PushMessage;
use CrosierSource\CrosierLibBaseBundle\Repository\Config\PushMessageRepository;
use CrosierSource\CrosierLibBaseBundle\Utils\EntityIdUtils\EntityIdUtils;
use Psr\Container\ContainerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @author Carlos Eduardo Pauluk
 */
class PushMessageController extends AbstractController
{
    
    private LoggerInterface $logger;

    public function __construct(ContainerInterface $container, LoggerInterface $logger)
    {
        $this->container = $container;
        $this->logger = $logger;
    }


    /**
     * @Route("/api/cfg/pushMessage/getNewMessages", name="api_cfg_pushMessage_getNewMessages")
     */
    public function getNewMessages(Request $request): ?JsonResponse
    {
        $this->logger->debug('/cfg/pushMessage/getNewMessages');
        $this->logger->debug('CRSRSESSCK: ' . $request->cookies->get('CRSRSESSCK_CROSIERCORE'));

        try {
            /** @var PushMessageRepository $pushMessageRepo */
            $pushMessageRepo = $this->getDoctrine()->getRepository(PushMessage::class);
            $pushMessages = $pushMessageRepo->findByFiltersSimpl(
                [
                    ['dtNotif', 'IS_NULL'],
                    ['userDestinatarioId', 'EQ', $this->getUser()->getId()]
                ]
            );
            $r = [];
            /** @var PushMessage $pushMessage */
            foreach ($pushMessages as $pushMessage) {
                $pushMessage->setDtNotif(new \DateTime());
                $r[] = EntityIdUtils::serialize($pushMessage);
                $this->entityHandler->save($pushMessage);
            }
            return new JsonResponse($r);
        } catch (\Exception $e) {
            return new JsonResponse('');
        }
    }

}