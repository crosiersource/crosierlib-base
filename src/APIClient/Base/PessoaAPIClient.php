<?php

namespace CrosierSource\CrosierLibBaseBundle\APIClient\Base;


use CrosierSource\CrosierLibBaseBundle\APIClient\CrosierEntityIdAPIClient;
use Symfony\Component\Cache\Adapter\FilesystemAdapter;
use Symfony\Contracts\Cache\ItemInterface;

/**
 * Cliente para consumir os serviços REST da PessoaAPI.
 *
 * @package CrosierSource\CrosierLibBaseBundle\APIClient\Base
 * @author Carlos Eduardo Pauluk
 */
class PessoaAPIClient extends CrosierEntityIdAPIClient
{

    public function getBaseURI(): string
    {
        return $_SERVER['CROSIERCORE_URL'] . '/api/bse/pessoa';
    }

    public function findByCategEStr(string $categ, string $str = null, $limit = null)
    {

        $cache = new FilesystemAdapter();

        $r = $cache->get('findByCategEStr_' . $categ . $str . $limit, function (ItemInterface $item) use ($categ, $str, $limit) {
            $item->expiresAfter(3600);

            return $this->get('/findByCategEStr', ['categ' => $categ, 'str' => $str, 'limit' => $limit]);
        });

        return json_decode($r, true);
    }


}