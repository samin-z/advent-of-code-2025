<?php

function isInvalid($number) {
    $str = (string) $number;
    $length = strlen($str);

    if ($str[0] === '0') {
        return false;
    }

    
    for ($substringLength = 1; $substringLength <= $length / 2; $substringLength++) {
        if ($length % $substringLength !== 0) {
            continue;
        }

        $pattern = substr($str, 0, $substringLength);
        
        $repetitions = $length / $substringLength;
        
        $repeated = str_repeat($pattern, $repetitions);
        
        if ($str === $repeated) {
            return true;
        }
    }

    return false;
}

function findInvalidIds($input) {
    $ranges = explode(',', $input);
    $invalidIds = [];

    foreach ($ranges as $range) {
        $range = trim($range);

        if (empty($range)) {
            continue;
        }

        list($start, $end) = explode('-', $range);
        $start = (int) $start;
        $end = (int) $end;

        for ($i = $start; $i <= $end; $i++) {
            if (isInvalid($i)) {
                $invalidIds[] = $i;
            }
        }
    }

    return $invalidIds;
}

function calculateInvalidIdsSum($input) {
    $invalidIds = findInvalidIds($input);
    return array_sum($invalidIds);
}

$exampleNumbers = "2200670-2267527,265-409,38866-50720,7697424-7724736,33303664-33374980,687053-834889,953123-983345,3691832-3890175,26544-37124,7219840722-7219900143,7575626241-7575840141,1-18,1995-2479,101904-163230,97916763-98009247,52011-79060,31-49,4578-6831,3310890-3365637,414256-608125,552-1005,16995-24728,6985-10895,878311-912296,59-93,9978301-10012088,17302200-17437063,1786628373-1786840083,6955834840-6955903320,983351-1034902,842824238-842861540,14027173-14217812";

var_dump(calculateInvalidIdsSum($exampleNumbers));