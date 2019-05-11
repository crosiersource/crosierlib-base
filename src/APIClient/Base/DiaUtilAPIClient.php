<?php

namespace CrosierSource\CrosierLibBaseBundle\APIClient\Base;


use CrosierSource\CrosierLibBaseBundle\APIClient\CrosierAPIClient;
use CrosierSource\CrosierLibBaseBundle\Exception\ViewException;
use CrosierSource\CrosierLibBaseBundle\Utils\DateTimeUtils\DateTimeUtils;

/**
 * Cliente para consumir os serviços REST da DiaUtilAPI.
 *
 * @package CrosierSource\CrosierLibBaseBundle\APIClient\Base
 * @author Carlos Eduardo Pauluk
 */
class DiaUtilAPIClient extends CrosierAPIClient
{

    public function getBaseURI(): string
    {
        return $_SERVER['CROSIERCORE_URL'] . '/api/bse/diaUtil';
    }

    /**
     * @param \DateTime $ini
     * @param \DateTime $fim
     * @param bool $futuro
     * @return array
     */
    public function incPeriodo(\DateTime $ini, \DateTime $fim, bool $futuro): array
    {
        $params = [
            'ini' => $ini->format('Y-m-d'),
            'fim' => $fim->format('Y-m-d'),
            'futuro' => $futuro
        ];
        $contents = $this->get('/incPeriodo/', $params, true);
        return json_decode($contents, true);
    }

    /**
     * @param \DateTime $dt
     * @param bool $prox
     * @param bool $financeiro
     * @param bool $comercial
     * @return \DateTime|mixed
     * @throws ViewException
     */
    public function findDiaUtil(\DateTime $dt, bool $prox = true, ?bool $financeiro = null, ?bool $comercial = null): ?\DateTime
    {
        $params =
            [
                'dt' => $dt->format('Y-m-d'),
                'prox' => $prox,
                'financeiro' => $financeiro,
                'comercial' => $comercial,
            ];
        $r = json_decode($this->get('/findDiaUtil/', $params), true);

        if (!isset($r['diaUtil'])) {
            $this->handleErrorResponse($r);
        }
        $diaUtil = $r['diaUtil'];
        return \DateTime::createFromFormat('Y-m-d', $diaUtil);
    }

    /**
     * @param \DateTime $dtIni
     * @param int $ordinal
     * @param bool $financeiro
     * @param bool $comercial
     * @return \DateTime|mixed
     * @throws ViewException
     */
    public function findEnesimoDiaUtil(\DateTime $dtIni, int $ordinal, ?bool $financeiro = null, ?bool $comercial = null): ?\DateTime
    {
        $params =
            [
                'dtIni' => $dtIni->format('Y-m-d'),
                'ordinal' => $ordinal,
                'financeiro' => $financeiro,
                'comercial' => $comercial,
            ];
        $r = json_decode($this->get('/findEnesimoDiaUtil/', $params), true);

        if (!isset($r['diaUtil'])) {
            $this->handleErrorResponse($r);
        }
        $diaUtil = $r['diaUtil'];
        return DateTimeUtils::parseDateStr($diaUtil);
    }


}