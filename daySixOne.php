<?php

require_once 'inputs-second-part.php';

function solveWorksheet($input) {
    $lines = explode("\n", trim($input));
    
    $operatorRow = null;
    $numberRows = [];
    
    foreach ($lines as $line) {
        if (empty(trim($line))) continue;
        
        if (preg_match('/^[\s\*\+]+$/', $line)) {
            $operatorRow = $line;
        } else {
            $numberRows[] = $line;
        }
    }
    
    if (null ===$operatorRow) {
        return 0;
    }
    
    $maxLength = max(array_map('strlen', array_merge($numberRows, [$operatorRow])));
    
    $columnIsSeparator = [];
    for ($col = 0; $col < $maxLength; $col++) {
        $isSeparator = true;
        foreach ($numberRows as $row) {
            if ($col < strlen($row) && $row[$col] !== ' ') {
                $isSeparator = false;
                break;
            }
        }
        $columnIsSeparator[$col] = $isSeparator;
    }
    
    $problems = [];
    $problemStart = null;
    
    for ($col = 0; $col < $maxLength; $col++) {
        if ($columnIsSeparator[$col]) {
            if ($problemStart !== null) {
                $problems[] = ['start' => $problemStart, 'end' => $col - 1];
                $problemStart = null;
            }
        } else {
            if (null === $problemStart) {
                $problemStart = $col;
            }
        }
    }
    
    if ($problemStart !== null) {
        $problems[] = ['start' => $problemStart, 'end' => $maxLength - 1];
    }
    
    $grandTotal = 0;
    
    foreach ($problems as $problem) {
        $numbers = [];
        $operator = null;
        
        foreach ($numberRows as $row) {
            $substring = substr($row, $problem['start'], $problem['end'] - $problem['start'] + 1);
            preg_match_all('/\d+/', $substring, $matches);
            foreach ($matches[0] as $numStr) {
                $numbers[] = (int)$numStr;
            }
        }
        
        for ($col = $problem['start']; $col <= $problem['end']; $col++) {
            if ($col < strlen($operatorRow)) {
                $char = trim($operatorRow[$col]);
                if ('*' === $char || '+' === $char) {
                    $operator = $char;
                    break;
                }
            }
        }
        
        if (empty($numbers) || null === $operator) {
            continue;
        }
        
        if ('*' ===$operator) {
            $result = array_product($numbers);
        } else {
            $result = array_sum($numbers);
        }
        
        $grandTotal += $result;
    }
    
    return $grandTotal;
}

echo "Grand total: " . solveWorksheet($daySixOneInput) . "\n";


