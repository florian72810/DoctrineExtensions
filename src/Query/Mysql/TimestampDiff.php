<?php

namespace DoctrineExtensions\Query\Mysql;

use Doctrine\ORM\Query\AST\Functions\FunctionNode;
use Doctrine\ORM\Query\Lexer;

/**
 * @author Przemek Sobstel <przemek@sobstel.org>
 * @author Florian Guillaumin <florian.guillaumin@gmail.com>
 */
class TimestampDiff extends FunctionNode
{
    public $firstDatetimeExpression = null;

    public $secondDatetimeExpression = null;

    public $unit = null;

    protected static $allowedUnits = [
        'MICROSECOND',
        'SECOND',
        'MINUTE',
        'HOUR',
        'DAY',
        'WEEK',
        'MONTH',
        'QUARTER',
        'YEAR',
    ];

    public function parse(\Doctrine\ORM\Query\Parser $parser)
    {
        $parser->match(Lexer::T_IDENTIFIER);
        $parser->match(Lexer::T_OPEN_PARENTHESIS);
        $this->unit = $parser->StringPrimary();
        $parser->match(Lexer::T_COMMA);
        $this->firstDatetimeExpression = $parser->ArithmeticPrimary();
        $parser->match(Lexer::T_COMMA);
        $this->secondDatetimeExpression = $parser->ArithmeticPrimary();
        $parser->match(Lexer::T_CLOSE_PARENTHESIS);
    }

    public function getSql(\Doctrine\ORM\Query\SqlWalker $sql_walker)
    {
        $unit = strtoupper(is_string($this->unit) ? $this->unit : $this->unit->value);

        if (!in_array($unit, self::$allowedUnits)) {
            throw QueryException::semanticalError('TIMESTAMPDIFF() does not support unit "' . $unit . '".');
        }

        return sprintf(
            'TIMESTAMPDIFF(%s, %s, %s)',
            $unit,
            $this->firstDatetimeExpression->dispatch($sql_walker),
            $this->secondDatetimeExpression->dispatch($sql_walker)
      );
    }
}
