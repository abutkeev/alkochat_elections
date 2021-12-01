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

$header = fgetcsv($in_fd, null, ';');
foreach ($header as $k => $v) {
    $start = strpos($v, '[');
    $end = strpos($v, ']');
    $header[$k] = substr($v, $start + 1, $end - $start - 1);
}
$cnt = count($header);
$result = array();
while ($data = fgetcsv($in_fd, null, ';')) {
    $vote = array();
    foreach ($data as $index => $place) {
        if ($place == 'Капотня') {
            if (!isset($vote[$cnt - 1])) {
                $vote[$cnt - 1] = array($header[$index]);
            } else {
                array_push($vote[$cnt - 1], $header[$index]);
            }
        } else {
            $vote[intval($place) - 1] = $header[$index];
        }
    }
    ksort($vote);
    array_push($result, $vote);
}
foreach ($result as $vote) {
    if (is_array($vote[$cnt - 1])) {
        $vote[$cnt - 1] = '"' . implode(',', $vote[$cnt - 1]) . '"';
    }
    fwrite($out_fd, implode(',', $vote). "\n");
}
fclose($in_fd);
fclose($out_fd);
