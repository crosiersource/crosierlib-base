<?php

namespace CrosierSource\CrosierLibBaseBundle\APIClient\Base;


use CrosierSource\CrosierLibBaseBundle\APIClient\CrosierAPIClient;

class DiaUtilAPIClient extends CrosierAPIClient
{

    /**
     * @param int $id
     * @return string
     */
    public function incPeriodo(\DateTime $ini, \DateTime $fim, bool $futuro)
    {
        $params = [
            'ini' => $ini->format('Y-m-d'),
            'fim' => $fim->format('Y-m-d'),
            'futuro' => $futuro
        ];
        return $this->get('/api/bse/diaUtil/incPeriodo/', $params);
    }

    public function findProximoDiaUtilFinanceiro(\DateTime $dt) {
        $params = [
            'dt' => $dt->format('Y-m-d'),
        ];
        $r = $this->get('/api/bse/diaUtil/findProximoDiaUtilFinanceiro/', $params);
        $dt = json_decode($r);
        return $dt;
    }

    public function findDiasUteisFinanceirosByMesAno(\DateTime $mesano) {
        $params = [
            'mesano' => $mesano->format('Y-m'),
        ];
        $r = json_decode($this->get('/api/bse/diaUtil/findDiasUteisFinanceirosByMesAno/', $params));
        return $r;
    }

}