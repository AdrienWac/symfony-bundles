<?php
namespace AdrienLbt\HexagonalMakerBundle\Maker\Factory;

interface CreatorInterface 
{
    public function generate(): void;

    public function addOperation(): void;

    public function writeChanges(): void;
}