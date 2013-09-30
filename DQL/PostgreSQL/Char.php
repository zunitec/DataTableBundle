<?php 

namespace Zuni\DataTableBundle\DQL\PostgreSQL;

use Doctrine\ORM\Query\AST\Functions\FunctionNode;
use Doctrine\ORM\Query\Lexer;

/**
 * CastFunction ::= "CHAR" "(" ArithmeticPrimary ")"
 */
class Char extends FunctionNode
{

    private $firstExpression = null;

    public function parse(\Doctrine\ORM\Query\Parser $parser)
    {
        $parser->match(Lexer::T_IDENTIFIER);
        $parser->match(Lexer::T_OPEN_PARENTHESIS);
        $this->firstExpression = $parser->ArithmeticPrimary();
        $parser->match(Lexer::T_CLOSE_PARENTHESIS);
    }

    public function getSql(\Doctrine\ORM\Query\SqlWalker $sqlWalker)
    {
        return 'CAST(' .$this->firstExpression->dispatch($sqlWalker) . ' AS TEXT)'; 
    }
}
