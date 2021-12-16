<?php

namespace CrosierSource\CrosierLibBaseBundle\EntityHandler\Config;

use CrosierSource\CrosierLibBaseBundle\Entity\Config\PushMessage;
use CrosierSource\CrosierLibBaseBundle\Entity\Security\User;
use CrosierSource\CrosierLibBaseBundle\EntityHandler\EntityHandler;
use CrosierSource\CrosierLibBaseBundle\Repository\Security\UserRepository;
use Doctrine\DBAL\Connection;

/**
 * Class PushMessageEntityHandler
 * @package App\EntityHandler\Config
 *
 * @author Carlos Eduardo Pauluk
 */
class PushMessageEntityHandler extends EntityHandler
{

    public function getEntityClass()
    {
        return PushMessage::class;
    }

    /**
     * @param PushMessage $pushMessage
     * @return mixed|void
     */
    public function beforeSave($pushMessage)
    {
        if (!$pushMessage->getId()) {
            if (!$pushMessage->dtEnvio) {
                $pushMessage->dtEnvio = new \DateTime();
            }
        }
    }

    public function enviarMensagem(string $mensagem, ?array $users = [], ?string $url = null, ?int $minutosValidade = 10080): void
    {
        /** @var User $user */
        foreach ($users as $user) {
            $pushMessage = new PushMessage();
            $pushMessage->dtEnvio = new \DateTime();
            $pushMessage->mensagem = $mensagem;
            $pushMessage->url = $url;
            if ($minutosValidade) {
                $dtValidade = (clone $pushMessage->dtEnvio)->add(new \DateInterval('PT' . $minutosValidade . 'M'));
                $pushMessage->dtValidade = $dtValidade;
            }
            $pushMessage->userDestinatarioId = $user->getId();
            $this->save($pushMessage);
        }
    }


    public function enviarMensagemUsersIds(string $mensagem, ?array $usersIds = [], ?string $url = null, ?int $minutosValidade = 10080): void
    {
        /** @var UserRepository $repoUser */
        $repoUser = $this->getDoctrine()->getRepository(User::class);

        $users = [];
        /** @var User $user */
        foreach ($usersIds as $userId) {
            $users[] = $repoUser->find($userId);
        }

        $this->enviarMensagem($mensagem, $users, $url, $minutosValidade);
    }


    public function enviarMensagemParaLista(string $mensagem, string $chaveLista, ?string $url = null, ?int $minutosValidade = 10080): void
    {
        /** @var Connection $conn */
        $conn = $this->getDoctrine()->getConnection();
        $rs = $conn->fetchAssociative(
            'SELECT valor FROM cfg_app_config WHERE chave = :chave AND app_uuid = :appUUID',
            ['chave' => 'listas_push.json', 'appUUID' => ($_SERVER['CROSIERCORE_UUID'] ?? '')]);

        $rsListas = json_decode($rs['valor'] ?? '[]', true);

        /** @var UserRepository $repoUser */
        $repoUser = $this->getDoctrine()->getRepository(User::class);

        $users = [];
        foreach ($rsListas as $rLista) {
            if ($rLista['chave'] === $chaveLista) {
                foreach ($rLista['usuariosAssinantes'] as $username) {
                    $users[] = $repoUser->findOneByUsername($username);
                }
                break;
            }
        }
        $this->enviarMensagem($mensagem, $users, $url, $minutosValidade);
    }


}