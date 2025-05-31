<?php

declare(strict_types=1);

namespace AdrienLbt\HexagonalMakerBundle\Maker\Utils\Str;

interface StrInterface
{
    public static function getNamespace(string $fullClassName): string;
}