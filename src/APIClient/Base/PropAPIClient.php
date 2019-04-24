<?php

namespace CrosierSource\CrosierLibBaseBundle\APIClient\Base;


use CrosierSource\CrosierLibBaseBundle\APIClient\CrosierAPIClient;

/**
 * Cliente para consumir os serviços REST da PropAPI.
 *
 * @package CrosierSource\CrosierLibBaseBundle\APIClient\Base
 * @author Carlos Eduardo Pauluk
 */
class PropAPIClient extends CrosierAPIClient
{

    public static function getBaseUri(): string
    {
        return $_SERVER['CROSIERCORE_URL'] . '/api/bse/prop';
    }

    /**
     * @param string $nome
     * @return array
     */
    public function findByNome(string $nome): ?array
    {
        $contents = $this->get('/findByNome/' . $nome);
        return json_decode($contents, true);
    }

    /**
     * @return array
     */
    public function findGrades()
    {
        $grades = json_decode($this->findByNome('GRADES')['valores'], true);

        $rGrades = [];

        foreach ($grades as $grade) {
            $gradeId = $grade['gradeId'];
            $tamanhosArr = [];
            $tamanhos = $this->findTamanhosByGradeId($gradeId);
            foreach ($tamanhos as $tamanho) {
                $tamanhosArr[] = $tamanho['tamanho'];
            }
            $tamanhosStr = str_pad($gradeId,3,'0',STR_PAD_LEFT) . ' (' . implode('-', $tamanhosArr) . ')';
            $rGrades[$gradeId] = $tamanhosStr;
        }
        return $rGrades;
    }

    /**
     * @param int $gradeId
     * @return array|null
     */
    public function findTamanhosByGradeId(int $gradeId)
    {
        $grades = json_decode($this->findByNome('GRADES')['valores'], true);

        foreach ($grades as $grade) {
            if ($grade['gradeId'] === $gradeId) {
                return $grade['tamanhos'];
            }
        }
        return null;
    }

    /**
     * @param int $gradeId
     * @return array|null
     */
    public function findTamanhosByGradeIdAndOrdem(int $gradeId, int $ordem)
    {
        $tamanhos = $this->findTamanhosByGradeId($gradeId);
        foreach ($tamanhos as $tamanho) {
            if ($tamanho['ordem'] === $ordem) {
                return $tamanho;
            }
        }
        return null;
    }


}