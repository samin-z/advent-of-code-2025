<?php

require_once 'inputs.php';

function countAccessibleRolls($input) {
    $grid = [];
    $lines = explode("\n", trim($input));
    
    foreach ($lines as $line) {
        $line = trim($line);
        if (empty($line)) continue;
        $grid[] = str_split($line);
    }
    
    $rows = count($grid);
    $cols = count($grid[0]);
    $accessibleCount = 0;
    
    for ($i = 0; $i < $rows; $i++) {
        for ($j = 0; $j < $cols; $j++) {
            if ($grid[$i][$j] !== '@') {
                continue;
            }
            
            $adjacentCount = 0;
            
            $directions = [
                [-1, -1], [-1, 0], [-1, 1],
                [0, -1],           [0, 1],
                [1, -1],  [1, 0],  [1, 1]
            ];
            
            foreach ($directions as $dir) {
                $ni = $i + $dir[0];
                $nj = $j + $dir[1];
                
                if ($ni >= 0 && $ni < $rows && $nj >= 0 && $nj < $cols) {
                    if ($grid[$ni][$nj] === '@') {
                        $adjacentCount++;
                    }
                }
            }
            
            if ($adjacentCount < 4) {
                $accessibleCount++;
            }
        }
    }
    
    return $accessibleCount;
}

echo "Accessible rolls: " . countAccessibleRolls($dayFourOneInput) . "\n";


