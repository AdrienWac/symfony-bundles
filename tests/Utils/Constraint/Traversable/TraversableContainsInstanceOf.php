<?php declare(strict_types=1);

namespace AdrienLbt\HexagonalMakerBundle\Tests\Utils\Constraint\Traversable;

use PHPUnit\Framework\Constraint\Constraint;
use PHPUnit\Framework\Constraint\IsInstanceOf;

final class TraversableContainsInstanceOf extends Constraint
{   
    private array $constraints = [];

    public function __construct(private array $types)
    {
        foreach ($types as $type) {
            $this->constraints[] = new IsInstanceOf($type);
        }
    }

    public function toString(): string
    {
        $typeList = implode(', ', array_map(function ($type) {
            return is_string($type) ? $type : get_class($type);
        }, $this->types));
        
        return 'contains only instances of: ' . $typeList;
    }

    /**
     * We go through each $other element, checking that the current element 
     * verifies an instance constraint.
     * If no constraint is passed, this means that the current element does not have 
     * an authorized instance. The evaluation is a failure.
     * 
     * @todo Edit failure message with name of element not passing constraint
     */
    public function evaluate($other, string $description = '', bool $returnResult = false): ?bool
    {
        $success = true;
        
        foreach ($other as $item) {
            $instanceFound = false;
            
            foreach ($this->constraints as $constraint) {
                if ($constraint->evaluate($item, '', true)) {
                    $instanceFound = true;
                    break;
                }
            }
            
            if (!$instanceFound) {
                $success = false;
                break;
            }
        }

        if ($returnResult) {
            return $success;
        }

        if (!$success) {
            $this->fail($other, $description);
        }

        return null;
    }
}