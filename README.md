# PHP Generator Functions demo

PHP Generator functions solve the problems of memory and time efficiency when iterating over data structures in a very simple way.

## Typical approach
Let's say you need to process a csv file. Here's a typical approach to doing this:
```php
function getData(string $filename) {
  if (!$fh = fopen($filename, 'r')) {
    return;
  }

  $data = [];
  while (FALSE !== $row = fgetcsv($fh)) {
    $data[] = $row;
  }

  fclose($fh);
  return $data;
}

$filename = 'my-really-big-file.csv';
$i = 0;
foreach (getData($filename) as $row) {
  // Skip header row.
  if ($i === 0) {
    $i = 1;
    continue;
  }
  $var = $row[0]
  ...
}
```
But what if this file is 2GB?

Well, for starters, the `foreach()` loop is going to take a while, because 2GB is a lot of data. However, before we even get to run the `foreach()` loop, that `getData()` function is going to take forever.

And then there's the big question...how much memory will this consume?

Answer? A LOT. WAY more than 2GB. And even if it did only take 2GB, that is too much memory for a single process to consume, especially in a webserver environment.

## Generator functions to the rescue
This is a generator function:
```php
function getData(string $filename) {
  if (!$fh = fopen($filename, 'r')) {
    return;
  }

  while (FALSE !== $row = fgetcsv($fh)) {
    yield $row;
  }

  fclose($fh);
}
```
That looks a lot like what we were doing in the `getData()` function above, save for two key differences:
1. We are not building an array.
2. The `yield` statement.

It's the `yield` statement that makes this a generator function.

## How does this work?
A generator function returns an object of the internal PHP class [Generator](https://www.php.net/manual/en/class.generator.php), which implements PHP's [Iterator](https://www.php.net/manual/en/class.iterator.php) interface.

The `foreach()` is able to iterate over that Generator object because `foreach()` understands the Iterator interface.

Generator functions are essentially a shorthand for creating a basic Iterator class, like this:
```php
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
```
## Examples
This repo includes some example files you can run against a standard csv file of your choice to demonstrate the difference generators (and iterator classes) can make.
* no_generator.php - this is typical approach outlined at the top of this page.
* generator.php - this is the generator approach
* iterator.php - this is functionally equivalent to the generator function, just more code.

You can run these files rom the command line (provided you have PHP available) from within the project root directory like this:

`php no_generator.php`

`php generator.php`

`php iterator.php`

The bigger the file you provide, the more dramatic the difference will be.

[Learn more about PHP generators](https://www.php.net/manual/en/language.generators.overview.php)