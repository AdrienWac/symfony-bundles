<?php

declare(strict_types=1);

namespace AdrienLbt\HexagonalMakerBundle\Maker\Utils\Str;

use Symfony\Bundle\MakerBundle\Str;

final class StrMakerBundleAdapter implements StrInterface
{
    public static function getNamespace(string $fullClassName): string
    {
        return Str::getNamespace($fullClassName);
    }
}