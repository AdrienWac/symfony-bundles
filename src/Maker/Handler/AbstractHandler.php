<?php declare(strict_types=1);

namespace AdrienLbt\HexagonalMakerBundle\Maker\Handler;

use AdrienLbt\HexagonalMakerBundle\Maker\Factory\ClassFile\Creator;
use Symfony\Bundle\MakerBundle\ConsoleStyle;

abstract class AbstractHandler
{
    protected ?HandlerInterface $next = null;

    public function __construct(
        protected Creator $creator,
        protected ConsoleStyle $io
    )
    {}

    public function setNext(?HandlerInterface $next): void
    {
        $this->next = $next;
    }

    public function handleRequest(mixed $request): void
    {
        if (!is_null($this->next)) {
            $this->next->handleRequest($request);
        }
    }

}