<?php

namespace CrosierSource\CrosierLibBaseBundle\Business\Config;

use CrosierSource\CrosierLibBaseBundle\Utils\StringUtils\StringUtils;
use Doctrine\Persistence\ManagerRegistry;
use InfluxDB2\Model\WritePrecision;
use Psr\Log\LoggerInterface;
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
        try {
            $component = $component ?? $this->getComponent();
            $username = $username ?? ($this->security->getUser() ? $this->security->getUser()->getUsername() : null) ?? 'n/d';

            if ($tipo === 'err' || $this->logToo || ($_SERVER['SYSLOG_LOGTOO'] ?? false)) {
                $msg = '[COMPO:' . $component . '] ';
                if ($obs) {
                    $msg .= '[OBS:' . $obs . '] ';
                }
                $msg .= '[username: ' . $username . '] ';
                $msg .= $this->uuidSess . ' - ' . $action;
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

            if ($_SERVER['INFLUXDB_URL'] ?? false) {
                $point = \InfluxDB2\Point::measurement('logs')
                    ->addTag('app', $app)
                    ->addTag('tipo', $tipo)
                    ->addTag('ip', $_SERVER['REMOTE_ADDR'])
                    ->addTag('username', $username)
                    ->addTag('component', $component)
                    ->addTag('uuid', $this->uuidSess)
                    ->addField('action', $action)
                    ->addField('obs', $obs);
                $this->getInflux()->write($point);
            }

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


    private \InfluxDB2\WriteApi $influx;

    private function getInflux(): \InfluxDB2\WriteApi
    {
        if (!isset($this->influx)) {
            $client = new \InfluxDB2\Client([
                "url" => $_SERVER['INFLUXDB_URL'],
                "token" => $_SERVER['INFLUXDB_TOKEN'],
                "bucket" => $_SERVER['INFLUXDB_BUCKET'],
                "org" => $_SERVER['INFLUXDB_ORG'],
                "precision" => WritePrecision::NS,
            ]);
            $this->influx = $client->createWriteApi();
        }
        return $this->influx;
    }


}

