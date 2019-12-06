<?php

namespace CrosierSource\CrosierLibBaseBundle\Doctrine\Extensions\MySQL;

use Doctrine\ORM\Query\AST\Functions\FunctionNode;
use Doctrine\ORM\Query\Lexer;

/**
 * Class Date
 *
 * Função do MySQL que extrai somente a date de um datetime.
 *
 * @package CrosierSource\CrosierLibBaseBundle\Doctrine\Extensions\MySQL
 */
class Date extends FunctionNode
{

    public $dateExpression = null;

    public $patternExpression = null;

    public function parse(\Doctrine\ORM\Query\Parser $parser)
    {
        $parser->match(Lexer::T_IDENTIFIER);
        $parser->match(Lexer::T_OPEN_PARENTHESIS);
        $this->patternExpression = $parser->StringPrimary();
        $parser->match(Lexer::T_CLOSE_PARENTHESIS);
    }

    public function getSql(\Doctrine\ORM\Query\SqlWalker $sqlWalker)
    {
        return 'DATE(' .
            $this->patternExpression->dispatch($sqlWalker) .
            ')';
    }
}