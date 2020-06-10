<?php

namespace App;

require "vendor/autoload.php";

use App\Lib\Elections;
use App\Lib\Matrix;
use App\Lib\Counter;

function print_a(array $array, $print_position = true, $print_key = true, $print_value = true) {
  if (!($print_position || $print_key || $print_value))
	return;

  $i = 1;
  foreach ($array as $key => $value) {
	if ($print_position)
	  print "$i. ";
	if ($print_key)
	  print "$key: ";
	if ($print_value)
	  print "$value";
	print "\n";
	$i ++;
  }
}
function print_v(array $votes) {
  $i = 1;
  foreach ($votes as $vote) {
	print "Бюллетень $i:\n";
	print_a($vote['places'], true, false);
	print "Капотня:\n";
	print_a($vote['last'], false, false);
	print "\n";
	$i ++;
  }
}

if ($argc < 2 || $argc > 6) {
  fwrite(STDERR, "Usage: php ". $argv[0]. " <data.csv> [<lists.csv> [<publish_bullots> [<d_matrix.csv>] [<p_matrix.csv>]]]\n");
  exit(255);
}

$e = new Elections($argv[1]);

print "Топ первых мест:\n";
print_a($e->get_firsts());

print "\nТоп Капотни:\n";
print_a($e->get_lasts());

print "\nВхождений в топ-5:\n";
print_a($e->get_five());
print "\n";

if ($argc >= 3 && $fd = fopen($argv[2], 'r')) {
  while ($list = fgetcsv($fd)) {
	if (count($list) < 2)
	  continue;

	$list_name = array_shift($list);

	$votes = $e->get_list_votes($list);
	print "$list_name в топ-5: ". count($votes). "\n";
	if (count($votes) == 0) {
	  print "\n";
	} else {
	  if ($argc >= 4) {
		if ( $argv[3] == 'true') {
		  print_v($votes);
		} elseif ($argv[3] != 'false') {
		  throw new Exception("publish_bullot must be true or false");
		}
	  } else {
		print "\n";
	  }
	}
  }
}

print "Результат по Шульце:\n";
print_a($e->count_shultze());

print "\nКворум: ". $e->get_quorum(). "\n";

if ($argc >= 4) {
  $filename = $argv[4];
  print "\nd matrix saved to: ". $filename. "\n\n";
  $e->save_d_matrix($filename);
}

if ($argc >= 5) {
  $filename = $argv[5];
  print "d matrix saved to: ". $filename. "\n";
  $e->save_p_matrix($filename);
}
