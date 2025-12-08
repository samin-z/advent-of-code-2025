<?php

require_once 'inputs.php';

function findMaxJoltage($bank) {
    $maxJoltage = 0;
    $digits = str_split($bank);
    $length = count($digits);
    
    for ($i = 0; $i < $length - 1; $i++) {
        $firstDigit = (int)$digits[$i];
        
        $maxSecondDigit = 0;
        for ($j = $i + 1; $j < $length; $j++) {
            $secondDigit = (int)$digits[$j];
            if ($secondDigit > $maxSecondDigit) {
                $maxSecondDigit = $secondDigit;
            }
        }
        
        $joltage = $firstDigit * 10 + $maxSecondDigit;
        if ($joltage > $maxJoltage) {
            $maxJoltage = $joltage;
        }
    }
    
    return $maxJoltage;
}

function calculateTotalJoltage($input) {
    $banks = explode("\n", trim($input));
    $total = 0;
    
    foreach ($banks as $bank) {
        $bank = trim($bank);
        if (empty($bank)) continue;
        
        $maxJoltage = findMaxJoltage($bank);
        $total += $maxJoltage;
    }
    
    return $total;
}

echo "Total joltage: " . calculateTotalJoltage($dayThreeInput) . "\n";

