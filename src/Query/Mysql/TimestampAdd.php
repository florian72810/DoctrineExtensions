<?php

namespace DoctrineExtensions\Query\Mysql;

use Doctrine\ORM\Query\AST\Functions\FunctionNode;
use Doctrine\ORM\Query\Lexer;

/**
 * @author Alessandro Tagliapietra <tagliapietra.alessandro@gmail.com>
 * @author Florian Guillaumin <florian.guillaumin@gmail.com>
 */
class TimestampAdd extends TimestampDiff
{
    public function getSql(\Doctrine\ORM\Query\SqlWalker $sql_walker)
    {
        $unit = strtoupper(is_string($this->unit) ? $this->unit : $this->unit->value);

        if (!in_array($unit, self::$allowedUnits)) {
            throw QueryException::semanticalError('TIMESTAMPADD() does not support unit "' . $unit . '".');
        }
        
        return sprintf(
            'TIMESTAMPADD(%s, %s, %s)',
            $unit,
            $this->firstDatetimeExpression->dispatch($sql_walker),
            $this->secondDatetimeExpression->dispatch($sql_walker)
      );
    }
}
