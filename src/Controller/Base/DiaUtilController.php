<?php


namespace CrosierSource\CrosierLibBaseBundle\Controller\Base;


use CrosierSource\CrosierLibBaseBundle\Entity\Base\DiaUtil;
use CrosierSource\CrosierLibBaseBundle\Repository\Base\DiaUtilRepository;
use CrosierSource\CrosierLibBaseBundle\Utils\DateTimeUtils\DateTimeUtils;
use Psr\Container\ContainerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

/**
 * @author Carlos Eduardo Pauluk
 */
class DiaUtilController extends AbstractController
{

    /**
     * DiaUtilController constructor.
     * @param ContainerInterface $container
     */
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    /**
     * Encontra um dia útil específico de acordo com os parâmetros.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function findDiaUtil(Request $request): JsonResponse
    {
        try {
            $dia = $request->get('dt');
            $dateTimeDia = DateTimeUtils::parseDateStr($dia);
            $prox = $request->get('prox') ? filter_var($request->get('prox'), FILTER_VALIDATE_BOOLEAN) : null;
            $comercial = $request->get('comercial') ? filter_var($request->get('comercial'), FILTER_VALIDATE_BOOLEAN) : null;
            $financeiro = $request->get('financeiro') ? filter_var($request->get('financeiro'), FILTER_VALIDATE_BOOLEAN) : null;
            /** @var DiaUtilRepository $repo */
            $repo = $this->getDoctrine()->getRepository(DiaUtil::class);
            $diaUtil = $repo->findDiaUtil($dateTimeDia, $prox, $financeiro, $comercial);// Se não achar, apenas incrementa ou decrementa
            if (!$diaUtil) {
                if ($prox === null) {
                    // não faz nada
                } else {
                    if ($prox) {
                        $dateTimeDia->add(new \DateInterval('P1D'));
                    } else {
                        $dateTimeDia->sub(new \DateInterval('P1D'));
                    }
                }
                $response = new JsonResponse(
                    [
                        'diaUtil' => $dateTimeDia->format('Y-m-d')
                    ]
                );
                return $response;
            }
            $response = new JsonResponse(
                [
                    'diaUtil' => $diaUtil->format('Y-m-d')
                ]
            );
            return $response;
        } catch (\Throwable $e) {
            return new JsonResponse(null, 500);
        }
    }


    /**
     * Incrementa um período relatórial.
     *
     * @param Request $request
     * @return null|JsonResponse
     */
    public function incPeriodo(Request $request): ?JsonResponse
    {
        try {
            $ini = $request->get('ini');
            $dtIni = DateTimeUtils::parseDateStr($ini);
            $fim = $request->get('fim');
            $dtFim = DateTimeUtils::parseDateStr($fim);
            $futuro = $request->get('futuro') ? filter_var($request->get('futuro'), FILTER_VALIDATE_BOOLEAN) : null;
            $comercial = $request->get('comercial') ? filter_var($request->get('comercial'), FILTER_VALIDATE_BOOLEAN) : null;
            $financeiro = $request->get('financeiro') ? filter_var($request->get('financeiro'), FILTER_VALIDATE_BOOLEAN) : null;
            if ($ini === $fim) {
                if (!$comercial && !$financeiro) {
                    $amanha = DateTimeUtils::addDays($dtIni, 1);
                    $periodo = [
                        'dtIni' => $amanha->format('Y-m-d'),
                        'dtFim' => $amanha->format('Y-m-d'),
                    ];
                } else {
                    /** @var DiaUtilRepository $repo */
                    $repo = $this->getDoctrine()->getRepository(DiaUtil::class);
                    /** @var \DateTime $diaUtil */
                    $diaUtil = $repo->findEnesimoDiaUtil($dtIni, $futuro ? 2 : -2, $financeiro, $comercial);
                    $periodo = [
                        'dtIni' => $diaUtil->format('Y-m-d'),
                        'dtFim' => $diaUtil->format('Y-m-d'),
                    ];
                }
            } else {
                $periodo = DateTimeUtils::iteratePeriodoRelatorial($dtIni, $dtFim, $futuro);
            }
            return new JsonResponse($periodo);
        } catch (\Exception $e) {
            return new JsonResponse(null, 500);
        }
    }

    /**
     * Encontra o próximo dia útil ordinalmente
     * @param Request $request
     * @return JsonResponse|null
     */
    public function findEnesimoDiaUtil(Request $request): ?JsonResponse
    {
        try {
            $dtIni = DateTimeUtils::parseDateStr($request->get('dtIni'));
            $ordinal = (int)$request->get('ordinal');
            $comercial = $request->get('comercial') ? filter_var($request->get('comercial'), FILTER_VALIDATE_BOOLEAN) : null;
            $financeiro = $request->get('financeiro') ? filter_var($request->get('financeiro'), FILTER_VALIDATE_BOOLEAN) : null;
            /** @var DiaUtilRepository $repo */
            $repo = $this->getDoctrine()->getRepository(DiaUtil::class);
            /** @var \DateTime $diaUtil */
            $diaUtil = $repo->findEnesimoDiaUtil($dtIni, $ordinal, $financeiro, $comercial);
            $response = new JsonResponse(
                [
                    'diaUtil' => $diaUtil->format('Y-m-d')
                ]
            );
            return $response;
        } catch (\Exception $e) {
            return new JsonResponse(null, 500);
        }
    }

}