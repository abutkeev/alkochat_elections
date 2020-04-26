<?php
class Matrix {
  private $matrix;
  private $max_key_len;
  private $keys;
  
  function __construct(array $keys) {
	sort($keys);
	$this->max_key_len = 0;
	$this->keys = $keys;
	foreach ($keys as $row) {
	  $this->matrix[$row] = array();
	  foreach ($keys as $col) {
		$this->matrix[$row][$col] = 0;
	  }
	  if (strlen($row) > $this->max_key_len)
		$this->max_key_len = strlen($row);
	}
  }

  function get($row, $col) {
	if (!array_key_exists($row, $this->matrix))
	  throw new Exception("get: row $row not found");
	if (!array_key_exists($col, $this->matrix[$row]))
	  throw new Exception("get: col $col not found");

	return $this->matrix[$row][$col];
  }

  function set($row, $col, $value) {
	if (!array_key_exists($row, $this->matrix))
	  throw new Exception("set: row $row not found");
	if (!array_key_exists($col, $this->matrix[$row]))
	  throw new Exception("set: col $col not found");

	$this->matrix[$row][$col] = $value;
  }

  function inc($row, $col) {
	if (!array_key_exists($row, $this->matrix))
	  throw new Exception("row $row not found");
	if (!array_key_exists($col, $this->matrix[$row]))
	  throw new Exception("col $col not found");

	$this->matrix[$row][$col] ++;
  }

  function out($fd = STDOUT) {
	fputcsv($fd, array_merge(array(''), $this->keys));
	foreach ($this->keys as $row) {
	  fputcsv($fd, array_merge(array($row), $this->matrix[$row]));
	}
  }
}
