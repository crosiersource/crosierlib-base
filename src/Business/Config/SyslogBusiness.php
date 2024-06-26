<?php

namespace CrosierSource\CrosierLibBaseBundle\Business\Config;

use CrosierSource\CrosierLibBaseBundle\Utils\StringUtils\StringUtils;
use Doctrine\Persistence\ManagerRegistry;
use Psr\Log\LoggerInterface;
use Symfony\Component\DependencyInjection\Attribute\Autoconfigure;
use Symfony\Component\Security\Core\Security;

class SyslogBusiness
{

    private ManagerRegistry $doctrine;

    private Security $security;

    private LoggerInterface $logger;

    private ?string $app = null;

    private ?string $component = null;

    private ?bool $echo = false;

    private ?bool $logToo = false;

    // para marcar todas as chamadas dentro de uma mesma "sessão"
    public ?string $uuidSess = null;


    public function __construct(
        ManagerRegistry $doctrine,
        Security        $security,
        LoggerInterface $logger)
    {
        $this->doctrine = $doctrine;
        $this->security = $security;
        $this->logger = $logger;
        $this->uuidSess = StringUtils::guidv4();
    }

    public function getApp(): string
    {
        return $this->app ?? 'n/d';
    }

    public function setApp(string $app): SyslogBusiness
    {
        $this->app = $app;
        return $this;
    }

    public function getComponent(): string
    {
        return $this->component ?? 'n/d';
    }

    public function setComponent(string $component): SyslogBusiness
    {
        $this->component = $component;
        return $this;
    }

    public function setEcho(?bool $echo): SyslogBusiness
    {
        $this->echo = $echo;
        return $this;
    }

    public function setLogToo(?bool $logToo): SyslogBusiness
    {
        $this->logToo = $logToo;
        return $this;
    }


    public function info(
        string     $action,
        ?string    $obs = null,
        ?string    $app = null,
        ?string    $component = null,
        ?string    $username = null,
        ?\DateTime $deleteAfter = null,
        ?array     $jsonData = null): void
    {
        $this->save('info', $action, $obs, $app, $component, $username, $deleteAfter, $jsonData);
    }


    public function err(
        string     $action,
        ?string    $obs = null,
        ?string    $app = null,
        ?string    $component = null,
        ?string    $username = null,
        ?\DateTime $deleteAfter = null,
        ?array     $jsonData = null
    ): void
    {
        $this->save('err', $action, $obs, $app, $component, $username, $deleteAfter, $jsonData);
    }

    public function error(
        string     $action,
        ?string    $obs = null,
        ?string    $app = null,
        ?string    $component = null,
        ?string    $username = null,
        ?\DateTime $deleteAfter = null,
        ?array     $jsonData = null): void
    {
        $this->err($action, $obs, $app, $component, $username, $deleteAfter, $jsonData);
    }


    public function debug(
        string     $action,
        ?string    $obs = null,
        ?string    $app = null,
        ?string    $component = null,
        ?string    $username = null,
        ?\DateTime $deleteAfter = null,
        ?array     $jsonData = null): void
    {
        if ($_SERVER['APP_DEBUG'] ?? false) {
            $this->save('debug', $action, $obs, $app, $component, $username, $deleteAfter, $jsonData);
        }
    }

    private function save(
        string     $tipo,
        string     $action,
        ?string    $obs = null,
        ?string    $app = null,
        ?string    $component = null,
        ?string    $username = null,
        ?\DateTime $deleteAfter = null,
        ?array     $jsonData = null): void
    {
        if ($_SERVER['SYSLOG_DESABILITADO'] ?? false) {
            return;
        }
        try {
            if ($tipo === 'err' || $this->logToo || ($_SERVER['SYSLOG_LOGTOO'] ?? false)) {
                $msg = $this->uuidSess . ' - ' . $action;
                $msg .= '[' . $component . '] ';
                $msg .= '[username: ' . $username . '] ';
                switch ($tipo) {
                    case 'info':
                        $this->logger->info($msg);
                        break;
                    case 'err':
                        $this->logger->error($msg);
                        break;
                    case 'debug':
                        $this->logger->debug($msg);
                        break;
                }
            }
            $app = $app ?? $this->getApp();
            $component = $component ?? $this->getComponent();
            $username = $username ?? ($this->security->getUser() ? $this->security->getUser()->getUsername() : null) ?? 'n/d';
            $this->doctrine->getManager('logs')->getConnection()->insert('cfg_syslog', [
                'uuid_sess' => $this->uuidSess,
                'tipo' => $tipo,
                'app' => $app,
                'component' => $component,
                'act' => $action,
                'obs' => $obs,
                'username' => $username,
                'moment' => (new \DateTime())->format('Y-m-d H:i:s'),
                'delete_after' => $deleteAfter ? $deleteAfter->format('Y-m-d H:i:s') : null,
                'json_data' => $jsonData ? json_encode($jsonData) : null
            ]);
            if ($this->echo) {
                echo $tipo . ": " . $action . PHP_EOL;
                if ($obs) {
                    echo $obs . PHP_EOL . PHP_EOL;
                }
            }
        } catch (\Throwable $e) {
            $this->logger->error('erro ao gravar em cfg_syslog');
            $this->logger->error($e->getMessage());
        }
    }


}

