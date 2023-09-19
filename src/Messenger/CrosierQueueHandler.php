<?php

namespace CrosierSource\CrosierLibBaseBundle\Messenger;

use CrosierSource\CrosierLibBaseBundle\Business\Config\SyslogBusiness;
use CrosierSource\CrosierLibBaseBundle\Entity\Config\AppConfig;
use CrosierSource\CrosierLibBaseBundle\Repository\Config\AppConfigRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Messenger\MessageBusInterface;

/**
 * @author Carlos Eduardo Pauluk
 */
class CrosierQueueHandler
{

    private EntityManagerInterface $doctrine;

    private MessageBusInterface $bus;

    private SyslogBusiness $logger;

    public function __construct(
        EntityManagerInterface $doctrine,
        MessageBusInterface    $bus,
        SyslogBusiness         $logger
    )
    {
        $this->doctrine = $doctrine;
        $this->bus = $bus;
        $this->logger = $logger->setApp('core')->setComponent(self::class);
    }

    public function post(string $queue, $content): void
    {
        if ($this->hasQueueConsumers($queue)) {
            $jsonEncoded = json_encode($content);
            $this->logger->debug('CrosierQueue: Enviando para a fila ' . $queue, $jsonEncoded);
            $this->postToQueue($queue, $jsonEncoded);
        }
    }

    private function hasQueueConsumers(string $queue): bool
    {
        $chave = 'crosier_queue_consumer';
        /** @var AppConfigRepository $repoAppConfig */
        $repoAppConfig = $this->doctrine->getRepository(AppConfig::class);
        return (bool)($repoAppConfig->findAllByFiltersSimpl([['chave', 'EQ', $chave]]));
    }

    private function postToQueue(string $queue, string $content): void
    {
        $this->bus->dispatch(new CrosierQueueMessage($queue, $content));
    }

}
