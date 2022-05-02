<?php

namespace CrosierSource\CrosierLibBaseBundle\ApiPlatform\DataProvider;

use ApiPlatform\Core\DataProvider\PaginatorInterface;

final class Paginator implements \IteratorAggregate, PaginatorInterface
{
    private $iterator;
    private $firstResult;
    private $maxResults;
    private $totalItems;

    public function __construct(array $results, int $firstResult, int $maxResults)
    {
        if ($maxResults > 0) {
            $this->iterator = new \LimitIterator(new \ArrayIterator($results), $firstResult, $maxResults);
        } else {
            $this->iterator = new \EmptyIterator();
        }
        $this->firstResult = $firstResult;
        $this->maxResults = $maxResults;
        $this->totalItems = $maxResults;
    }

    /**
     * {@inheritdoc}
     */
    public function getCurrentPage(): float
    {
        if (0 >= $this->maxResults) {
            return 1.;
        }

        return floor($this->firstResult / $this->maxResults) + 1.;
    }

    public function getLastPage(): float
    {
        if (0 >= $this->maxResults) {
            return 1.;
        }

        return ceil($this->totalItems / $this->maxResults) ?: 1.;
    }

    public function getItemsPerPage(): float
    {
        return (float)$this->maxResults;
    }

    public function getTotalItems(): float
    {
        return (float)$this->totalItems;
    }

    public function count(): int
    {
        return $this->maxResults;
    }

    public function getIterator(): \Traversable
    {
        return $this->iterator;
    }
}
