<?php

namespace CrosierSource\CrosierLibBaseBundle\EntityHandler\Config;

use CrosierSource\CrosierLibBaseBundle\Entity\Config\PushMessage;
use CrosierSource\CrosierLibBaseBundle\Entity\Security\User;
use CrosierSource\CrosierLibBaseBundle\EntityHandler\EntityHandler;
use CrosierSource\CrosierLibBaseBundle\Utils\DateTimeUtils\DateTimeUtils;

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

    public function enviarMensagem(string $mensagem, array $users, ?int $minutosValidade = null): void
    {
        /** @var User $user */
        foreach ($users as $user) {
            $pushMessage = new PushMessage();
            $pushMessage->dtEnvio = new \DateTime();
            $pushMessage->mensagem = $mensagem;
            if ($minutosValidade) {
                $pushMessage->dtValidade = $pushMessage->dtEnvio->add(new \DateInterval('PT' . $minutosValidade . 'M'));
            }
            $pushMessage->userDestinatarioId = $user->getId();
            $this->save($pushMessage);
        }
    }


}