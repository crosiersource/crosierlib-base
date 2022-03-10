<?php

namespace CrosierSource\CrosierLibBaseBundle\ApiPlatform\Filter;

use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\AbstractContextAwareFilter;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Util\QueryNameGeneratorInterface;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\PropertyInfo\Type;
use Symfony\Component\Serializer\NameConverter\NameConverterInterface;

/**
 * Filtro que permite fazer o LIKE com ou sem '%'.
 * @author Carlos Eduardo Pauluk
 */
class LikeFilter extends AbstractContextAwareFilter
{

    private ?string $parameterName = null;

    public function __construct(
        ManagerRegistry        $managerRegistry,
        ?RequestStack          $requestStack = null,
        LoggerInterface        $logger = null,
        array                  $properties = null,
        NameConverterInterface $nameConverter = null,
        ?string                $parameterName = null)
    {
        parent::__construct($managerRegistry, $requestStack, $logger, $properties, $nameConverter);
        $this->parameterName = $parameterName;
    }


    public function getParameterName(): ?string
    {
        return $this->parameterName;
    }

    public function setParameterName(?string $parameterName): void
    {
        $this->parameterName = $parameterName;
    }


    protected function filterProperty(
        string                      $property,
                                    $value,
        QueryBuilder                $queryBuilder,
        QueryNameGeneratorInterface $queryNameGenerator,
        string                      $resourceClass,
        string                      $operationName = null)
    {
        if ($property !== 'like') {
            return;
        }
        $campo = key($value);
        // otherwise filter is applied to order and page as well
        if (
            !$this->isPropertyEnabled($campo, $resourceClass) ||
            !$this->isPropertyMapped($campo, $resourceClass)
        ) {
            return;
        }

        $value = str_replace('*', '%', $value[$campo]);
        $parameterName = $queryNameGenerator->generateParameterName($property); // Generate a unique parameter name to avoid collisions with other filters
        $queryBuilder
            ->andWhere(sprintf('o.%s LIKE :%s', $campo, $parameterName))
            ->setParameter($parameterName, $value);
    }

    // This function is only used to hook in documentation generators (supported by Swagger and Hydra)
    public function getDescription(string $resourceClass): array
    {
        if (!$this->properties) {
            return [];
        }

        $description = [];
        foreach ($this->properties as $property => $strategy) {
            $description["$property"] = [
                'property' => $property,
                'type' => Type::BUILTIN_TYPE_STRING,
                'required' => false,
                'swagger' => [
                    'description' => 'Filtro que permite fazer o LIKE com ou sem %',
                    'name' => '',
                    'type' => '',
                ],
            ];
        }

        return $description;
    }
}
