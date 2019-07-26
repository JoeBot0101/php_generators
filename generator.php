<?php

include_once "convert_bytes.php";

function getData(string $filename) {
  if (!$fh = fopen($filename, 'r')) {
    return;
  }

  while (FALSE !== $row = fgetcsv($fh)) {
    yield $row;
  }

  fclose($fh);
}

echo "Baseline Memory Usage: " . convertBytes(memory_get_usage()) . PHP_EOL . PHP_EOL;

$filename = 'your_file_here.csv';
$i = 0;
foreach (getData($filename) as $row) {
  // Skip header row.
  if ($i === 0) {
    $i = 1;
    continue;
  }

  // Output the first three fields followed by the current memory usage.
  echo "$row[0]\t$row[1]\t$row[2]\t" . convertBytes(memory_get_usage()) . PHP_EOL;
}