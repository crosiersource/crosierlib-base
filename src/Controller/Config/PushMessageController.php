<?php

namespace CrosierSource\CrosierLibBaseBundle\Controller\Config;

use CrosierSource\CrosierLibBaseBundle\Entity\Config\PushMessage;
use CrosierSource\CrosierLibBaseBundle\EntityHandler\Config\PushMessageEntityHandler;
use CrosierSource\CrosierLibBaseBundle\Repository\Config\PushMessageRepository;
use CrosierSource\CrosierLibBaseBundle\Utils\APIUtils\CrosierApiResponse;
use CrosierSource\CrosierLibBaseBundle\Utils\EntityIdUtils\EntityIdUtils;
use Doctrine\DBAL\Connection;
use Psr\Container\ContainerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Security;

/**
 * @author Carlos Eduardo Pauluk
 */
class PushMessageController extends AbstractController
{
    private Security $security;
    
    private PushMessageEntityHandler $entityHandler;

    public function __construct(
        ContainerInterface $container,
        Security           $security,
        PushMessageEntityHandler $entityHandler
    )
    {
        $this->container = $container;
        $this->security = $security;
        $this->entityHandler = $entityHandler;
    }

    public function getNewMessages(Request $request): ?JsonResponse
    {
        //$this->logger->debug('/cfg/pushMessage/getNewMessages');
        //$this->logger->debug('CRSRSESSCK: ' . $request->cookies->get('CRSRSESSCK_CROSIERCORE'));

        try {
            /** @var PushMessageRepository $pushMessageRepo */
            $pushMessageRepo = $this->getDoctrine()->getRepository(PushMessage::class);
            $pushMessages = $pushMessageRepo->findByFiltersSimpl(
                [
                    ['dtNotif', 'IS_NULL'],
                    ['userDestinatarioId', 'EQ', $this->security->getUser()->getId()]
                ]
            );
            $r = [];
            /** @var PushMessage $pushMessage */
            foreach ($pushMessages as $pushMessage) {
                $pushMessage->dtNotif = new \DateTime();
                $r[] = EntityIdUtils::serialize($pushMessage);
                $eh->save($pushMessage);
            }
            return new JsonResponse($r);
        } catch (\Exception $e) {
            return new JsonResponse('');
        }
    }


    public function getListasPush(): JsonResponse
    {
        try {
            /** @var Connection $conn */
            $conn = $this->getDoctrine()->getConnection();
            $rs = $conn->fetchAssociative(
                'SELECT valor FROM cfg_app_config WHERE chave = :chave AND app_uuid = :appUUID',
                ['chave' => 'listas_push.json', 'appUUID' => $_SERVER['CROSIERAPP_UUID']]);

            $rsListas = json_decode($rs['valor'], true);

            foreach ($rsListas as $rLista) {
                $lista = $rLista;
                $lista['assinada'] = (in_array($this->security->getUser()->getUsername(), $rLista['usuariosAssinantes'], true));
                unset($lista['usuariosAssinantes']);
                $listas[] = $lista;
            }

            return CrosierApiResponse::success($listas);
        } catch (\Throwable $e) {
            return CrosierApiResponse::error($e);
        }
    }

    public function assinarListaPush(Request $request): JsonResponse
    {
        try {
            $assinaturas = json_decode($request->getContent(), true)['assinaturas'];

            /** @var Connection $conn */
            $conn = $this->getDoctrine()->getConnection();
            $rs = $conn->fetchAssociative(
                'SELECT id, valor FROM cfg_app_config WHERE chave = :chave AND app_uuid = :appUUID',
                ['chave' => 'listas_push.json', 'appUUID' => $_SERVER['CROSIERAPP_UUID']]);

            $appConfig = json_decode($rs['valor'], true);

            foreach ($appConfig as $k => $ac) {
                $achouChave = false;
                foreach ($assinaturas as $chave) {
                    if ($ac['chave'] === $chave) {
                        $achouChave = true;
                        if (!in_array($this->security->getUser()->getUsername(), $ac['usuariosAssinantes'], true)) {
                            $appConfig[$k]['usuariosAssinantes'][] = $this->security->getUser()->getUsername();
                        }
                    }
                }
                if (!$achouChave && in_array($this->security->getUser()->getUsername(), $ac['usuariosAssinantes'], true)) {
                    foreach ($ac['usuariosAssinantes'] as $kk => $v) {
                        if ($v === $this->security->getUser()->getUsername()) {
                            unset($appConfig[$k]['usuariosAssinantes'][$kk]);
                        }
                    }
                }
            }

            $conn->update('cfg_app_config', ['valor' => json_encode($appConfig)], ['id' => $rs['id']]);

            return CrosierApiResponse::success();
        } catch (\Throwable $e) {
            return CrosierApiResponse::error($e);
        }
    }


}