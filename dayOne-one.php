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
        
        if ($direction === 'L') {
            $position = $position - $distance;
        } else {
            $position = $position + $distance;
        }
        
        $position = (($position % 100) + 100) % 100;
        
        if ($position === 0) {
            $count++;
        }
    }
    
    return $count;
}

echo "Password: " . calculatePassword($dayOneOneInput) . "\n";