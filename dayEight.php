<?php

require_once 'inputs-second-part.php';

class UnionFind {
    private $parent = [];
    private $rank = [];
    private $size = [];
    
    public function __construct($n) {
        for ($i = 0; $i < $n; $i++) {
            $this->parent[$i] = $i;
            $this->rank[$i] = 0;
            $this->size[$i] = 1;
        }
    }
    
    public function find($x) {
        if ($this->parent[$x] != $x) {
            $this->parent[$x] = $this->find($this->parent[$x]);
        }
        return $this->parent[$x];
    }
    
    public function union($x, $y) {
        $rootX = $this->find($x);
        $rootY = $this->find($y);
        
        if ($rootX == $rootY) {
            return false; 
        }
        
        if ($this->rank[$rootX] < $this->rank[$rootY]) {
            $this->parent[$rootX] = $rootY;
            $this->size[$rootY] += $this->size[$rootX];
        } elseif ($this->rank[$rootX] > $this->rank[$rootY]) {
            $this->parent[$rootY] = $rootX;
            $this->size[$rootX] += $this->size[$rootY];
        } else {
            $this->parent[$rootY] = $rootX;
            $this->rank[$rootX]++;
            $this->size[$rootX] += $this->size[$rootY];
        }
        
        return true;
    }
    
    public function getSize($x) {
        $root = $this->find($x);
        return $this->size[$root];
    }
    
    public function getAllSizes() {
        $sizes = [];
        $seen = [];
        for ($i = 0; $i < count($this->parent); $i++) {
            $root = $this->find($i);
            if (!isset($seen[$root])) {
                $seen[$root] = true;
                $sizes[] = $this->size[$root];
            }
        }
        return $sizes;
    }
}

function calculateDistance($p1, $p2) {
    $dx = $p1[0] - $p2[0];
    $dy = $p1[1] - $p2[1];
    $dz = $p1[2] - $p2[2];

    return sqrt($dx * $dx + $dy * $dy + $dz * $dz);
}

class DistanceHeap extends SplMaxHeap {
    protected function compare($a, $b): int {
        return $a['distance'] <=> $b['distance'];
    }
}

function solveJunctionBoxesPart2($input) {
    $lines = explode("\n", trim($input));
    $boxes = [];
    
    foreach ($lines as $line) {
        $line = trim($line);
        if (empty($line)) {
            continue;
        }
        $coords = explode(',', $line);
        if (count($coords) == 3) {
            $boxes[] = [
                (int)$coords[0],
                (int)$coords[1],
                (int)$coords[2]
            ];
        }
    }
    
    $numBoxes = count($boxes);
    if ($numBoxes == 0) {
        echo "Error: No boxes found in input\n";
        return 0;
    }
    
    echo "Parsed $numBoxes boxes\n";
    
    $uf = new UnionFind($numBoxes);
    
    $heap = new DistanceHeap();
    $heapSize = min(100000, ($numBoxes * ($numBoxes - 1)) / 2);
    
    $totalPairs = 0;
    for ($i = 0; $i < $numBoxes; $i++) {
        for ($j = $i + 1; $j < $numBoxes; $j++) {
            $totalPairs++;
            $dist = calculateDistance($boxes[$i], $boxes[$j]);
            $pair = [
                'distance' => $dist,
                'box1' => $i,
                'box2' => $j
            ];
            
            if ($heap->count() < $heapSize) {
                $heap->insert($pair);
            } else {
                $top = $heap->top();
                if ($dist < $top['distance']) {
                    $heap->extract();
                    $heap->insert($pair);
                }
            }
        }
    }
    
    echo "Total pairs calculated: $totalPairs\n";
    
    $distances = [];
    while (!$heap->isEmpty()) {
        $distances[] = $heap->extract();
    }
    
    usort($distances, function($a, $b) {
        $diff = $a['distance'] <=> $b['distance'];
        if ($diff != 0) return $diff;
        $diff = $a['box1'] <=> $b['box1'];
        if ($diff != 0) return $diff;
        return $a['box2'] <=> $b['box2'];
    });
    
    echo "Processing " . count($distances) . " smallest pairs for MST\n";
    
    $connectionsMade = 0;
    $lastConnection = null;
    
    foreach ($distances as $pair) {
        $box1 = $pair['box1'];
        $box2 = $pair['box2'];
        
        if ($uf->union($box1, $box2)) {
            $connectionsMade++;
            $lastConnection = $pair;
            
            if ($connectionsMade == $numBoxes - 1) {
                break;
            }
        }
    }
    
    if ($connectionsMade < $numBoxes - 1) {
        echo "Warning: Only made $connectionsMade connections, need " . ($numBoxes - 1) . "\n";
        echo "Need to process more pairs. Increasing heap size and recalculating...\n";
        
        $heap = new DistanceHeap();
        $heapSize = ($numBoxes * ($numBoxes - 1)) / 2;
        
        for ($i = 0; $i < $numBoxes; $i++) {
            for ($j = $i + 1; $j < $numBoxes; $j++) {
                $dist = calculateDistance($boxes[$i], $boxes[$j]);
                $pair = [
                    'distance' => $dist,
                    'box1' => $i,
                    'box2' => $j
                ];
                
                if ($heap->count() < $heapSize) {
                    $heap->insert($pair);
                } else {
                    $top = $heap->top();
                    if ($dist < $top['distance']) {
                        $heap->extract();
                        $heap->insert($pair);
                    }
                }
            }
        }
        
        $distances = [];
        while (!$heap->isEmpty()) {
            $distances[] = $heap->extract();
        }
        
        usort($distances, function($a, $b) {
            $diff = $a['distance'] <=> $b['distance'];
            if ($diff != 0) return $diff;
            $diff = $a['box1'] <=> $b['box1'];
            if ($diff != 0) return $diff;
            return $a['box2'] <=> $b['box2'];
        });
        
        foreach ($distances as $pair) {
            $box1 = $pair['box1'];
            $box2 = $pair['box2'];
            
            if ($uf->union($box1, $box2)) {
                $connectionsMade++;
                $lastConnection = $pair;
                
                if ($connectionsMade == $numBoxes - 1) {
                    break;
                }
            }
        }
    }
    
    echo "Connections made: $connectionsMade (need " . ($numBoxes - 1) . " to connect all boxes)\n";
    
    if ($lastConnection === null || $connectionsMade < $numBoxes - 1) {
        echo "Error: Could not connect all boxes or no last connection found\n";
        return 0;
    }
    
    $box1 = $boxes[$lastConnection['box1']];
    $box2 = $boxes[$lastConnection['box2']];
    $x1 = $box1[0];
    $x2 = $box2[0];
    $result = $x1 * $x2;
    
    if ($numBoxes <= 20) {
        echo "Last connection: [{$box1[0]},{$box1[1]},{$box1[2]}] <-> [{$box2[0]},{$box2[1]},{$box2[2]}]\n";
        echo "X coordinates: $x1 and $x2\n";
    }
    
    return $result;
}

function solveJunctionBoxes($input, $numConnections = 1000) {
    $lines = explode("\n", trim($input));
    $boxes = [];
    
    foreach ($lines as $line) {
        $line = trim($line);
        if (empty($line)) {
            continue;
        }
        $coords = explode(',', $line);
        if (count($coords) == 3) {
            $boxes[] = [
                (int)$coords[0],
                (int)$coords[1],
                (int)$coords[2]
            ];
        }
    }
    
    $numBoxes = count($boxes);
    if ($numBoxes == 0) {
        echo "Error: No boxes found in input\n";
        return 0;
    }
    
    echo "Parsed $numBoxes boxes\n";
    
    $uf = new UnionFind($numBoxes);
    
    $heap = new DistanceHeap();
    $heapSize = $numConnections;
    
    $totalPairs = 0;
    for ($i = 0; $i < $numBoxes; $i++) {
        for ($j = $i + 1; $j < $numBoxes; $j++) {
            $totalPairs++;
            $dist = calculateDistance($boxes[$i], $boxes[$j]);
            $pair = [
                'distance' => $dist,
                'box1' => $i,
                'box2' => $j
            ];
            
            if ($heap->count() < $heapSize) {
                $heap->insert($pair);
            } else {
                $top = $heap->top();
                if ($dist < $top['distance']) {
                    $heap->extract();
                    $heap->insert($pair);
                }
            }
        }
    }
    
    echo "Total pairs calculated: $totalPairs\n";
    
    $distances = [];
    while (!$heap->isEmpty()) {
        $distances[] = $heap->extract();
    }
    
    usort($distances, function($a, $b) {
        $diff = $a['distance'] <=> $b['distance'];
        if ($diff != 0) return $diff;

        $diff = $a['box1'] <=> $b['box1'];

        if ($diff != 0) return $diff;
        return $a['box2'] <=> $b['box2'];
    });
    
    echo "Processing " . count($distances) . " smallest pairs\n";
    if ($numBoxes <= 20 && count($distances) > 0) {
        echo "Shortest distance: " . number_format($distances[0]['distance'], 2) . "\n";
        echo "Longest of kept: " . number_format($distances[count($distances)-1]['distance'], 2) . "\n";
    }
    
    $pairsProcessed = 0;
    foreach ($distances as $pair) {
        if ($pairsProcessed >= $numConnections) {
            break;
        }
        
        $box1 = $pair['box1'];
        $box2 = $pair['box2'];
        
        $alreadyConnected = ($uf->find($box1) == $uf->find($box2));
        
        $connected = $uf->union($box1, $box2);
        
        if ($numBoxes <= 20 && $pairsProcessed < 5) {
            $b1 = $boxes[$box1];
            $b2 = $boxes[$box2];
            $status = $connected ? "CONNECTED" : ($alreadyConnected ? "already connected" : "failed");
            echo "Pair " . ($pairsProcessed + 1) . ": [{$b1[0]},{$b1[1]},{$b1[2]}] <-> [{$b2[0]},{$b2[1]},{$b2[2]}] dist=" . number_format($pair['distance'], 2) . " $status\n";
        }
        
        $pairsProcessed++;
    }
    
    echo "Pairs processed: $pairsProcessed (requested: $numConnections)\n";
    
    $circuitSizes = $uf->getAllSizes();
    
    rsort($circuitSizes);
    
 
    if (count($circuitSizes) > 0) {
        echo "Total circuits: " . count($circuitSizes) . "\n";
        echo "Circuit sizes (top 10): " . implode(', ', array_slice($circuitSizes, 0, 10)) . "\n";
    }
    
    if (count($circuitSizes) >= 3) {
        $result = $circuitSizes[0] * $circuitSizes[1] * $circuitSizes[2];
        echo "Product of three largest: {$circuitSizes[0]} * {$circuitSizes[1]} * {$circuitSizes[2]} = $result\n";
        return $result;
    } else {
        $product = 1;
        foreach ($circuitSizes as $size) {
            $product *= $size;
        }
        return $product;
    }
}


$fullResult1 = solveJunctionBoxes($dayEightOneInput, 1000);
echo "\nFinal answer part 1: $fullResult1\n\n";

$fullResult2 = solveJunctionBoxesPart2($dayEightTwoInput);
echo "\nFinal answer part 2: $fullResult2\n";


