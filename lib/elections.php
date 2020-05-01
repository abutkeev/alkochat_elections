<?php
class Elections {
  private $votes;
  private $members;
  private $counter;
  private $d_matrix;
  private $p_matrix;

  private function count_d_matrix() {
	foreach ($this->members as $row) {
	  foreach ($this->members as $col) {
		if ($row == $col)
		  continue;

		foreach ($this->votes as $vote) {
		  if ($this->is_stronger($row, $col, $vote['places'])) {
			$this->d_matrix->inc($row, $col);
		  }
		}
	  }
	}
  }

  private function count_p_matrix() {
	foreach ($this->members as $i) {
	  foreach ($this->members as $j) {
		if ($i != $j) {
		  if ($this->d_matrix->get($i, $j) > $this->d_matrix->get($j,$i)) {
			$this->p_matrix->set($i, $j, $this->d_matrix->get($i, $j));
		  }
		}
	  }
	}

	foreach ($this->members as $i) {
	  foreach ($this->members as $j) {
		if ($i != $j ) {
		  foreach ($this->members as $k) {
			if ($i != $k && $j != $k) {
			  $this->p_matrix->set($j, $k, max($this->p_matrix->get($j, $k), min($this->p_matrix->get($j, $i), $this->p_matrix->get($i, $k))));
			}
		  }
		}
	  }
	}
  }

  private function is_stronger($a, $b, $vote) {
	if ($a == $b)
	  return false;

	foreach ($vote as $entry) {
	  if ($entry == $a)
		return true;
	  elseif ($entry == $b)
		return false;
	}
	return false;
  }

  private function fill_members() {
	$this->members = array();
	foreach ($this->votes as $vote) {
	  foreach ($vote['places'] as $member) {
		if (!in_array($member, $this->members)) {
		  array_push($this->members, $member);
		}
	  }
	  foreach ($vote['last'] as $member) {
		if (!in_array($member, $this->members)) {
		  array_push($this->members, $member);
		}
	  }
	}
  }

  function __construct($file) {
	if (!$fd = fopen($file, 'r'))
	  throw new Exception("Can't open $file");

	$votes = array();
	while ($data = fgetcsv($fd)) {
	  $data = array_filter($data);
	  $last = array_pop($data);
	  $vote['places'] = $data;
	  $vote['last'] = explode(',', $last);
	  array_push($votes, $vote);
	}
	$this->votes = $votes;
	$this->fill_members();
	$this->counter = new Counter($this->members);

	$this->d_matrix = new Matrix($this->members);
	$this->p_matrix = new Matrix($this->members);
  }

  function get_quorum() {
	return floor(count($this->votes) / 2 + 1);
  }

  function count_shultze() {
	$this->count_d_matrix();
	$this->count_p_matrix();

	$result = new Counter($this->members);
	foreach ($this->members as $a) {
	  foreach ($this->members as $b) {
		if ($this->p_matrix->get($a, $b) > $this->p_matrix->get($b, $a)) {
		  $result->inc($a);
		}
	  }
	}
	return $result->get_results();
  }

  function get_firsts() {
	$this->counter->zero();
	foreach ($this->votes as $vote) {
	  if (count($vote['places']) == 0)
		continue;
	  $this->counter->inc($vote['places'][0]);
	}
	return $this->counter->get_results();
  }

  function get_five() {
	$this->counter->zero();
	foreach ($this->votes as $vote) {
	  for ($i = 0; $i < min(count($vote['places']), 5); $i++) {
		$this->counter->inc($vote['places'][$i]);
	  }
	}
	return $this->counter->get_results();
  }

  function get_list_votes(array $list) {
	$votes = array();
	foreach ($this->votes as $vote) {
	  $entries = 0;
	  for ($i = 0; $i < min(count($vote['places']), 5); $i++) {
		if (in_array($vote['places'][$i], $list)) {
		  $entries ++;
		}
	  }
	  if ($entries == count($list)) {
		array_push($votes, $vote);
	  }
	}
	return $votes;
  }

  function get_votes() {
	return $this->votes;
  }

  function get_lasts() {
	$this->counter->zero();
	foreach ($this->votes as $vote) {
	  foreach ($vote['last'] as $last) {
		$this->counter->inc($last);
	  }
	}
	return $this->counter->get_results();
  }
}
