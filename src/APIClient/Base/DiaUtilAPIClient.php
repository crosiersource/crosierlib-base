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

}