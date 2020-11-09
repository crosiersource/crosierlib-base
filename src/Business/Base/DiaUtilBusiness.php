<?php


namespace CrosierSource\CrosierLibBaseBundle\Business\Base;


use CrosierSource\CrosierLibBaseBundle\Exception\ViewException;
use CrosierSource\CrosierLibBaseBundle\Utils\DateTimeUtils\DateTimeUtils;
use Doctrine\DBAL\Connection;

/**
 * @package CrosierSource\CrosierLibBaseBundle\Business\Base
 */
class DiaUtilBusiness
{

    private Connection $conn;

    public function __construct(Connection $conn)
    {
        $this->conn = $conn;
    }

    /**
     * @param \DateTime $maxDia
     * @throws ViewException
     */
    public function gerarOuCorrigirDiasUteis(\DateTime $maxDia)
    {
        try {
            $rsDiasUteisConfig = $this->conn
                ->fetchAssociative('SELECT valor FROM cfg_app_config WHERE chave = \'bse_dia_util.json\' AND app_uuid = :coreAppUUID',
                    ['coreAppUUID' => '175bd6d3-6c29-438a-9520-47fcee653cc5']);

            $diasUteisConfig = json_decode($rsDiasUteisConfig['valor'], true);

            $agora = (new \DateTime())->format('Y-m-d H:i:s');
            $rsDiasUteisNaBase = $this->conn->fetchAllAssociative('SELECT * FROM bse_diautil WHERE date(dia) BETWEEN :dtIni AND :dtFim',
                [
                    'dtIni' => (new \DateTime())->format('Y-m-d'),
                    'dtFim' => $maxDia->format('Y-m-d')
                ]);
            $diasUteisNaBase = [];
            foreach ($rsDiasUteisNaBase as $diaUtilNaBase) {
                $dti = DateTimeUtils::parseDateStr($diaUtilNaBase['dia'])->format('Ymd');
                $diasUteisNaBase[$dti] = $diaUtilNaBase;
            }

            $datesList = DateTimeUtils::getDatesList(new \DateTime(), $maxDia);
            foreach ($datesList as $date) {
                $dti = $date->format('Ymd');

                $comercial = 1;
                $financeiro = 1;

                if (isset($diasUteisConfig['feriados']['fixos'][$date->format('md')])) {
                    $descricao = $diasUteisConfig['feriados']['fixos'][$date->format('md')];
                    $comercial = 0;
                    $financeiro = 0;
                } elseif (isset($diasUteisConfig['feriados']['moveis'][$date->format('Ymd')])) {
                    $descricao = $diasUteisConfig['feriados']['moveis'][$date->format('Ymd')];
                    $comercial = 0;
                    $financeiro = 0;
                } else {
                    $descricao = mb_strtoupper(DateTimeUtils::getDiaDaSemana($date));
                    $diaSemana = (int)$date->format('w');

                    // domingo
                    if ($diaSemana === 0) {
                        $comercial = 0;
                        $financeiro = 0;
                    }
                    // sábado
                    if ($diaSemana === 6) {
                        $financeiro = 0;
                        if (!$diasUteisConfig['sabados_comercial']) {
                            $comercial = 0;
                        }
                    }
                }

                $diaUtil = [
                    'dia' => $date->format('Y-m-d'),
                    'descricao' => $descricao,
                    'comercial' => $comercial,
                    'financeiro' => $financeiro,
                    'municipio_id' => null,
                    'inserted' => $agora,
                    'updated' => $agora,
                    'estabelecimento_id' => 1,
                    'user_inserted_id' => 1,
                    'user_updated_id' => 1,
                    'version' => 0
                ];

                if (isset($diasUteisNaBase[$dti])) {
                    $this->conn->update('bse_diautil', $diaUtil, ['id' => $diasUteisNaBase[$dti]['id']]);
                } else {
                    $this->conn->insert('bse_diautil', $diaUtil);
                }
            }

        } catch (\Throwable $e) {
            throw new ViewException('Ocorreu um erro ao gerar os dias úteis');
        }

    }

}