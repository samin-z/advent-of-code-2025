<?php

require_once 'inputs.php';

function calculatePassword($rotations) {
    $position = 50;
    $count = 0;
    
    $lines = explode("\n", trim($rotations));
    
    foreach ($lines as $line) {
        $line = trim($line);
        if (empty($line)) continue;
        
        $direction = $line[0];
        $distance = (int) substr($line, 1);
        
        $startPosition = $position;
        
        if ($direction === 'L') {
            $endPosition = $position - $distance;
        } else {
            $endPosition = $position + $distance;
        }
                
        if ($direction === 'R') {
            $minPos = $startPosition + 1;
            $maxPos = $endPosition;
        } else {
            $minPos = $endPosition;
            $maxPos = $startPosition - 1;
        }
        
        $firstZero = (int)ceil($minPos / 100) * 100;
        $lastZero = (int)floor($maxPos / 100) * 100;
        
        if ($firstZero <= $lastZero && $firstZero <= $maxPos) {
            $count += (($lastZero - $firstZero) / 100) + 1;
        }
        
        $position = (($endPosition % 100) + 100) % 100;
    }
    
    return $count;
}

echo "input password: " . calculatePassword($dayOneTwoInput) . "\n";

