<?php

include_once "convert_bytes.php";

class CsvIterator implements Iterator {
  protected $fileHandle;
  protected $row;
  protected $rowKey;

  public function __construct($filename) {
    if (!$this->fileHandle = fopen($filename, 'r')) {
      throw new RuntimeException('What file?');
    }
  }

  public function __destruct() {
    fclose($this->fileHandle);
  }

  public function current() {
    return $this->row;
  }

  public function next() {
    if (FALSE !== $this->row) {
      $this->row = fgetcsv($this->fileHandle);
      $this->rowKey++;
    }
  }

  public function key() {
    return $this->rowKey;
  }

  public function valid() {
    return FALSE !== $this->row;
  }

  public function rewind() {
    fseek($this->fileHandle, 0);
    $this->row = fgetcsv($this->fileHandle);
    $this->rowKey = 0;
  }
}

echo "Baseline Memory Usage: " . convertBytes(memory_get_usage()) . PHP_EOL . PHP_EOL;

$filename = 'your_file_here.csv';
$iterator = new CsvIterator($filename);
$i = 0;
foreach ($iterator as $row) {
  // Skip header row.
  if ($i === 0) {
    $i = 1;
    continue;
  }

  // Output the first three fields followed by the current memory usage.
  echo "$row[0]\t$row[1]\t$row[2]\t" . convertBytes(memory_get_usage()) . PHP_EOL;
}