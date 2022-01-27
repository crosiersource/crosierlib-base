<?php

namespace CrosierSource\CrosierLibBaseBundle\ApiPlatform\Filter;

use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\AbstractContextAwareFilter;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Util\QueryNameGeneratorInterface;
use Doctrine\ORM\QueryBuilder;
use Symfony\Component\PropertyInfo\Type;

/**
 * Implementação de filtro "NOT LIKE".
 * @author Carlos Eduardo Pauluk
 */
final class NotLikeFilter extends AbstractContextAwareFilter
{
    
    protected function filterProperty(string $property, $value, QueryBuilder $queryBuilder, QueryNameGeneratorInterface $queryNameGenerator, string $resourceClass, string $operationName = null)
    {
        if ($property === 'notLike') {
            foreach ($value as $campo => $valor) {
                // otherwise filter is applied to order and page as well
                if (!$this->isPropertyEnabled($campo, $resourceClass) || !$this->isPropertyMapped($campo, $resourceClass)) {
                    return;
                }
            }
        } else {
            return;
        }

        $rootAlias = $queryBuilder->getRootAliases()[0];
        foreach ($value as $campo => $valor) {
            // otherwise filter is applied to order and page as well
            $parameterName = $queryNameGenerator->generateParameterName($campo); // Generate a unique parameter name to avoid collisions with other filters
            $queryBuilder
                ->andWhere(sprintf($rootAlias . '.%s NOT LIKE :%s', $campo, $parameterName))
                ->setParameter($parameterName, $valor);
        }
        
        
    }

    // This function is only used to hook in documentation generators (supported by Swagger and Hydra)
    public function getDescription(string $resourceClass): array
    {
        if (!$this->properties) {
            return [];
        }

        $description = [];
        foreach ($this->properties as $property => $strategy) {
            $description["not_like_$property"] = [
                'property' => $property,
                'type' => Type::BUILTIN_TYPE_STRING,
                'required' => false,
                'swagger' => [
                    'description' => 'Filter NOT LIKE',
                    'name' => 'Custom name to use in the Swagger documentation',
                    'type' => 'Will appear below the name in the Swagger documentation',
                ],
            ];
        }

        return $description;
    }
}
