<?php


namespace CrosierSource\CrosierLibBaseBundle\Controller\Base;


use CrosierSource\CrosierLibBaseBundle\Entity\Base\DiaUtil;
use CrosierSource\CrosierLibBaseBundle\Entity\Base\Pessoa;
use CrosierSource\CrosierLibBaseBundle\Repository\Base\DiaUtilRepository;
use CrosierSource\CrosierLibBaseBundle\Repository\Base\PessoaRepository;
use CrosierSource\CrosierLibBaseBundle\Utils\DateTimeUtils\DateTimeUtils;
use CrosierSource\CrosierLibBaseBundle\Utils\EntityIdUtils\EntityIdUtils;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class PessoaController extends AbstractController
{

    /**
     * @var EntityIdUtils
     */
    private $entityIdUtils;

    public function __construct(EntityIdUtils $entityIdUtils)
    {
        $this->entityIdUtils = $entityIdUtils;
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function findById(Request $request): JsonResponse
    {
        try {
            $id = $request->get('id');
            /** @var PessoaRepository $repoPessoa */
            $repoPessoa = $this->getDoctrine()->getRepository(Pessoa::class);
            /** @var Pessoa $pessoa */
            $pessoa = $repoPessoa->find($id);
            if ($pessoa) {
                return new JsonResponse(['pessoa' => $this->entityIdUtils->serialize($pessoa)]);
            }
            return new JsonResponse(null);
        } catch (\Throwable $e) {
            return new JsonResponse(null, 500);
        }
    }

    /**
     * @param Request $request
     * @return JsonResponse|null
     */
    public function findByStr(Request $request): ?JsonResponse
    {
        $str = $request->get('term');
        $categ = $request->get('categ') ?? null;
        $maxResults = $request->get('maxResults') ?? 30;
        /** @var PessoaRepository $repoPessoa */
        $repoPessoa = $this->getDoctrine()->getRepository(Pessoa::class);
        if ($categ) {
            $pessoas = $repoPessoa->findPessoaByStrECateg($str, $categ, (int)$maxResults);
        } else {
            $pessoas = $repoPessoa->findPessoaByStr($str, (int)$maxResults);
        }
        if ($pessoas) {
            return new JsonResponse(['results' => $this->entityIdUtils->serializeAll($pessoas)]);
        }
        return null;
    }


}