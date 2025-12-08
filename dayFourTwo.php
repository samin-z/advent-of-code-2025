<?php

require_once 'inputs.php';

function countAdjacentRolls($grid, $i, $j, $rows, $cols) {
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
    
    return $adjacentCount;
}

function countTotalRemovableRolls($input) {
    $grid = [];
    $lines = explode("\n", trim($input));
    
    foreach ($lines as $line) {
        $line = trim($line);
        if (empty($line)) continue;
        $grid[] = str_split($line);
    }
    
    $rows = count($grid);
    $cols = count($grid[0]);
    $totalRemoved = 0;
    
    while (true) {
        $toRemove = [];
        
        for ($i = 0; $i < $rows; $i++) {
            for ($j = 0; $j < $cols; $j++) {
                if ($grid[$i][$j] !== '@') {
                    continue;
                }
                
                $adjacentCount = countAdjacentRolls($grid, $i, $j, $rows, $cols);
                
                if ($adjacentCount < 4) {
                    $toRemove[] = [$i, $j];
                }
            }
        }
        
        if (empty($toRemove)) {
            break;
        }
        
        foreach ($toRemove as $pos) {
            $grid[$pos[0]][$pos[1]] = '.';
            $totalRemoved++;
        }
    }
    
    return $totalRemoved;
}

echo "Total removable rolls: " . countTotalRemovableRolls($dayFourTwoInput) . "\n";


