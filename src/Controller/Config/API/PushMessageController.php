<?php

namespace CrosierSource\CrosierLibBaseBundle\Controller\Config\API;

use CrosierSource\CrosierLibBaseBundle\Entity\Config\PushMessage;
use CrosierSource\CrosierLibBaseBundle\EntityHandler\Config\PushMessageEntityHandler;
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
    
    private PushMessageEntityHandler $entityHandler;

    public function __construct(ContainerInterface $container, 
                                LoggerInterface $logger,
                                PushMessageEntityHandler $entityHandler
    )
    {
        $this->container = $container;
        $this->logger = $logger;
        $this->entityHandler = $entityHandler;
    }


    /**
     * @Route("/api/cfg/pushMessage/getNewMessages", name="api_cfg_pushMessage_getNewMessages")
     */
    public function getNewMessages(Request $request): ?JsonResponse
    {
        try {
            $sql = 'SELECT id FROM cfg_pushmessage WHERE 
                        user_destinatario_id = :userId AND 
                        (dt_validade IS NULL OR dt_validade >= :agora) AND 
                        dt_notif IS NULL ORDER BY dt_envio';
            
            $rsMsgs = $this->getDoctrine()->getConnection()->fetchAllAssociative($sql, [
                'userId' => $this->getUser()->getId(),
                'agora' => (new \DateTime())->format('Y-m-d H:i:s'),
            ]);
            
            /** @var PushMessageRepository $pushMessageRepo */
            $pushMessageRepo = $this->getDoctrine()->getRepository(PushMessage::class);
            
            $r = [];
            /** @var PushMessage $pushMessage */
            foreach ($rsMsgs as $rMsg) {
                $pushMessage = $pushMessageRepo->find($rMsg['id']);
                $pushMessage->dtNotif = new \DateTime();
                $r[] = EntityIdUtils::serialize($pushMessage);
                $this->entityHandler->save($pushMessage);
            }
            return new JsonResponse($r);
        } catch (\Exception $e) {
            return new JsonResponse('ERRO - getNewMessages');
        }
    }

}