<?php declare(strict_types=1);

namespace AdrienLbt\HexagonalMakerBundle\Maker\Handler;

interface HandlerInterface
{
    public function setNext(HandlerInterface $handler): void;

    public function handleRequest(mixed $request): void;
}