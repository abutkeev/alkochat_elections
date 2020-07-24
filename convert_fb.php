<?php

if ($argc != 3) {
    fwrite(STDERR, "Usage: php " . $argv[0] . " <in.csv> <out.csv>\n");
    exit(255);
}

if (!$in_fd = fopen($argv[1], 'r')) {
    throw new Exception("Can't open in file");
}

if (!$out_fd = fopen($argv[2], 'w')) {
    throw new Exception("Can't open out file");
}

$header = fgetcsv($in_fd);
array_shift($header);
array_shift($header);
$result = array();
while ($data = fgetcsv($in_fd)) {
    array_shift($data);
    array_shift($data);
    $vote = array();
    foreach ($data as $index => $place) {
        if ($place == 'Последнее место') {
            $vote[5] = $header[$index];
        } else {
            $vote[intval($place) - 1] = $header[$index];
        }
    }
    ksort($vote);
    array_push($result, $vote);
}
foreach ($result as $vote) {
    fwrite($out_fd, implode(',', $vote). "\n");
}
fclose($in_fd);
fclose($out_fd);
