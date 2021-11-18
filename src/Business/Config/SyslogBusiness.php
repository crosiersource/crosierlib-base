<?php

namespace CrosierSource\CrosierLibBaseBundle\Business\Config;

use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\Security\Core\Security;

/**
 * Class SyslogBusiness
 * @package CrosierSource\CrosierLibBaseBundle\Business\Config
 * @author Carlos Eduardo Pauluk
 */
class SyslogBusiness
{
    /**
     * @var EntityManagerInterface
     */
    private EntityManagerInterface $doctrine;

    private Security $security;

    private LoggerInterface $logger;

    private ?string $app = null;

    private string $component;
    
    private ?bool $echo = false;

    /**
     * SyslogBusiness constructor.
     * @param EntityManagerInterface $doctrine
     * @param Security $security
     * @param LoggerInterface $logger
     */
    public function __construct(EntityManagerInterface $doctrine, Security $security, LoggerInterface $logger)
    {
        $this->doctrine = $doctrine;
        $this->security = $security;
        $this->logger = $logger;
    }

    /**
     * @return string
     */
    public function getApp(): string
    {
        return $this->app;
    }

    /**
     * @param string $app
     * @return SyslogBusiness
     */
    public function setApp(string $app): SyslogBusiness
    {
        $this->app = $app;
        return $this;
    }

    /**
     * @return string
     */
    public function getComponent(): string
    {
        return $this->component;
    }

    /**
     * @param string $component
     * @return SyslogBusiness
     */
    public function setComponent(string $component): SyslogBusiness
    {
        $this->component = $component;
        return $this;
    }

    /**
     * @param bool|null $echo
     * @return SyslogBusiness
     */
    public function setEcho(?bool $echo): SyslogBusiness
    {
        $this->echo = $echo;
        return $this;
    }
    
    


    /**
     * @param string $app
     * @param string $component
     * @param string $action
     * @param string|null $obs
     * @param string|null $username
     * @param \DateTime|null $deleteAfter
     * @param array|null $jsonData
     */
    public function info(string $action, ?string $obs = null, ?string $app = null, ?string $component = null, ?string $username = null, ?\DateTime $deleteAfter = null, ?array $jsonData = null)
    {
        $this->save('info', $action, $obs, $app, $component, $username, $deleteAfter, $jsonData);
    }

    /**
     * @param string $app
     * @param string $component
     * @param string $action
     * @param string|null $obs
     * @param string|null $username
     * @param \DateTime|null $deleteAfter
     * @param array|null $jsonData
     */
    public function err(string $action, ?string $obs = null, ?string $app = null, ?string $component = null, ?string $username = null, ?\DateTime $deleteAfter = null, ?array $jsonData = null)
    {
        $this->save('err', $action, $obs, $app, $component, $username, $deleteAfter, $jsonData);
    }

    /**
     * @param string $app
     * @param string $component
     * @param string $action
     * @param string|null $obs
     * @param string|null $username
     * @param \DateTime|null $deleteAfter
     * @param array|null $jsonData
     */
    public function debug(string $action, ?string $obs = null, ?string $app = null, ?string $component = null, ?string $username = null, ?\DateTime $deleteAfter = null, ?array $jsonData = null)
    {
        if ($_SERVER['APP_DEBUG']) {
            $this->save('debug', $action, $obs, $app, $component, $username, $deleteAfter, $jsonData);
        }
    }

    /**
     * @param string $action
     * @param string|null $app
     * @param string|null $component
     * @param string|null $obs
     * @param string|null $username
     * @param \DateTime|null $deleteAfter
     * @param array|null $jsonData
     */
    private function save(string $tipo, string $action, ?string $obs, ?string $app, ?string $component, ?string $username, ?\DateTime $deleteAfter, ?array $jsonData): void
    {
        try {
            $app = $app ?? $this->getApp();
            $component = $component ?? $this->getComponent();
            $username = $username ?? ($this->security->getUser() ? $this->security->getUser()->getUsername() : null) ?? 'n/d';
            $this->doctrine->getConnection()->insert('cfg_syslog', [
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

