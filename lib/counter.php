<?php

namespace App\Lib;

class Counter {
  private $results;

  function __construct(array $keys) {
	foreach ($keys as $key) {
	  $this->results[$key] = 0;
	}
  }

  function zero() {
	foreach (array_keys($this->results) as $key) {
	  $this->results[$key] = 0;
	}
  }

  function get_results($sort = true) {
	if ($sort) {
	  arsort($this->results);
	}
	return $this->results;
  }

  function inc($key) {
	if (array_key_exists($key, $this->results)) {
	  $this->results[$key] ++;
	} else {
	  throw new Exception("$key not found");
	}
  }
}
