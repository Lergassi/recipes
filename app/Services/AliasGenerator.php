<?php

namespace App\Services;

use Behat\Transliterator\Transliterator;

class AliasGenerator
{
    private string $separator = '_';
    private int $numberStringLength = 2;
    private string $numberPadString = '0';

    private DataManager $dataManager;

    public function __construct(DataManager $dataManager)
    {
        $this->dataManager = $dataManager;
    }

    public function generate(string $string, int $number = null): string
    {
        if ($number !== null) {
            $string .= $this->separator . str_pad($number, $this->numberStringLength, $this->numberPadString, STR_PAD_LEFT);
        }

        return Transliterator::transliterate($string, $this->separator);
    }

//    public function generateByRecordsCount(string $string, string $table): string
//    {
//        $count = $this->dataManager->count($table, 'alias', $string) + 1;
//
//        return $this->generate($string, $count);
//    }
}