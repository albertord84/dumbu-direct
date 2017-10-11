<?php

$followers = [
    2226878683,
    1710017587,
    4280189532,
    368856645,
    1551583738,
    3551619243,
    29007820,
    3615398552,
    5650946541,
    429284740,
    4558283480,
    4554976093,
];

$count = count($followers);
for ($i = 0; $i < $count; $i++) {
    if (in_array($followers[$i], [ 368856645,1551583738,3551619243, ])) {
        array_splice($followers, $i, 1);
        $count--;
    }
}
print_r($followers);
echo PHP_EOL;
