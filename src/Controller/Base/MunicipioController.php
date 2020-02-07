<?php


namespace CrosierSource\CrosierLibBaseBundle\Controller\Base;


use CrosierSource\CrosierLibBaseBundle\Utils\EntityIdUtils\EntityIdUtils;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Psr\Container\ContainerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class MunicipioController
 * @package CrosierSource\CrosierLibBaseBundle\Controller\Base
 */
class MunicipioController extends AbstractController
{

    private EntityIdUtils $entityIdUtils;

    /**
     * MunicipioController constructor.
     * @param ContainerInterface $container
     * @param EntityIdUtils $entityIdUtils
     */
    public function __construct(ContainerInterface $container, EntityIdUtils $entityIdUtils)
    {
        $this->container = $container;
        $this->entityIdUtils = $entityIdUtils;
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function findEnderecoByCEP(Request $request): JsonResponse
    {
        try {
            $cep = $request->get('cep');

            $uri = 'http://cep.republicavirtual.com.br/web_cep.php';
            $client = new Client();
            $response = $client->request('GET', $uri,
                [
                    'query' => [
                        'cep' => $cep,
                        'formato' => 'json'
                    ]
                ]
            );
            return new JsonResponse($response->getBody()->getContents());
        } catch (GuzzleException $e) {
            return new JsonResponse('GuzzleException', 500);
        } catch (\Throwable $e) {
            return new JsonResponse(null, 500);
        }
    }


}