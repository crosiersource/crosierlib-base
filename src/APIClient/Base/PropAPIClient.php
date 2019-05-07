<?php

namespace CrosierSource\CrosierLibBaseBundle\APIClient\Base;


use CrosierSource\CrosierLibBaseBundle\APIClient\CrosierAPIClient;
use Symfony\Component\Cache\Adapter\FilesystemAdapter;
use Symfony\Contracts\Cache\ItemInterface;

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
    public function findGrades(): array
    {

        $cache = new FilesystemAdapter();

        $rGrades = $cache->get('grades', function (ItemInterface $item) {
            $item->expiresAfter(3600);

            $grades = json_decode($this->findByNome('GRADES')['valores'], true);

            $rGrades = [];

            foreach ($grades as $grade) {
                $gradeId = $grade['gradeId'];
                $tamanhosArr = [];
                $tamanhos = $this->findTamanhosByGradeId($gradeId);
                foreach ($tamanhos as $tamanho) {
                    $tamanhosArr[] = $tamanho['tamanho'];
                }
                $tamanhosStr = str_pad($gradeId, 3, '0', STR_PAD_LEFT) . ' (' . implode('-', $tamanhosArr) . ')';
                $rGrades[$gradeId] = $tamanhosStr;
            }

            return $rGrades;
        });


        return $rGrades;
    }

    /**
     * @param int $gradeId
     * @return array|null
     */
    public function findTamanhosByGradeId(int $gradeId): ?array
    {
        $cache = new FilesystemAdapter();

        $grades = $cache->get('findTamanhosByGradeId_' . $gradeId, function (ItemInterface $item) use ($gradeId) {
            $item->expiresAfter(3600);

            $grades = json_decode($this->findByNome('GRADES')['valores'], true);

            foreach ($grades as $grade) {
                if ($grade['gradeId'] === $gradeId) {
                    return $grade['tamanhos'];
                }
            }
            return $grades;
        });
        return $grades;
    }


    /**
     * @param int $gradeId
     * @param int $posicao
     * @return array|null
     */
    public function findTamanhoByGradeIdAndPosicao(int $gradeId, int $posicao): ?array
    {

        $cache = new FilesystemAdapter();

        $tamanho = $cache->get('findTamanhoByGradeIdAndPosicao_' . $gradeId . '-' . $posicao, function (ItemInterface $item) use ($gradeId, $posicao) {
            $item->expiresAfter(3600);

            $tamanhos = $this->findTamanhosByGradeId($gradeId);
            foreach ($tamanhos as $tamanho) {
                if ($tamanho['posicao'] === $posicao) {
                    return $tamanho;
                }
            }

            return null;
        });

        return $tamanho;
    }


    /**
     *
     * @param int $gradeId
     * @return array
     */
    public function buildGradesTamanhosByPosicaoArray(int $gradeId): array
    {
        $cache = new FilesystemAdapter();

        $gradesTamanhosByPosicaoArray = $cache->get('buildGradesTamanhosByPosicaoArray_' . $gradeId, function (ItemInterface $item) use ($gradeId) {
            $item->expiresAfter(3600);

            $tamanhos = $this->findTamanhosByGradeId($gradeId);
            $gradesTamanhosByPosicaoArray = [];

            for ($i = 1; $i <= 15; $i++) {
                foreach ($tamanhos as $tamanho) {
                    $gradesTamanhosByPosicaoArray[$i] = '-';
                    if ($i === $tamanho['posicao']) {
                        $gradesTamanhosByPosicaoArray[$tamanho['posicao']] = $tamanho['tamanho'];
                        break;
                    }
                }
            }
            return $gradesTamanhosByPosicaoArray;
        });

        return $gradesTamanhosByPosicaoArray;

    }

    /**
     *
     * @param int $gradeTamanhoId
     * @return int
     */
    public function findPosicaoByGradeTamanhoId(int $gradeTamanhoId): int
    {

        $cache = new FilesystemAdapter();

        $posicao = $cache->get('findPosicaoByGradeTamanhoId' . $gradeTamanhoId, function (ItemInterface $item) use ($gradeTamanhoId) {
            $item->expiresAfter(3600);

            $grades = json_decode($this->findByNome('GRADES')['valores'], true);

            foreach ($grades as $grade) {
                $gradeId = $grade['gradeId'];
                $tamanhos = $this->findTamanhosByGradeId($gradeId);
                foreach ($tamanhos as $tamanho) {
                    if ($tamanho['id'] === $gradeTamanhoId) {
                        return $tamanho['posicao'];
                    }
                }
            }

            return -1;
        });
        return $posicao;
    }


    /**
     * Encontra uma unidade por seu id no json UNIDADES.
     *
     * @param int $unidadeId
     * @return array|null
     */
    public function findUnidadeById(int $unidadeId): ?array
    {
        $cache = new FilesystemAdapter();

        $unidade = $cache->get('findUnidadeById' . $unidadeId, function (ItemInterface $item) use ($unidadeId) {
            $item->expiresAfter(3600);

            $unidades = json_decode($this->findByNome('UNIDADES')['valores'], true);

            foreach ($unidades as $unidade) {
                if ($unidadeId === $unidade['id']) {
                    return $unidade;
                }

            }

            return null;
        });
        return $unidade;
    }


}