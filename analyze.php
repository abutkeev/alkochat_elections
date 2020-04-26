<?php
require('lib/matrix.php');
require('lib/counter.php');
require('lib/elections.php');

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

$pletnev_list = array(
  'Леонид Петричук',
  'Ирина',
  'Мартин Левушканс',
  'Алексей Буткеев'
);

$jewish_list = array(
  'Илья Гуревич',
  'Мария Немцова',
  'Виктор Петрунин',
  'Ирина Шехтер',
  'Илья Эйдлин'
);

if ($argc !=2) {
  fwrite(STDERR, "Usage: php ", $argv[0]. " <data.cvs>\n");
  exit(255);
}

$e = new Elections($argv[1]);

print "Топ первых мест:\n";
print_a($e->get_firsts());

print "\nТоп Капотни:\n";
print_a($e->get_lasts());

print "\nВхождений в топ-5:\n";
print_a($e->get_five());

$votes = $e->get_list_votes($pletnev_list);
print "\nСписок Плетнёва в топ-5: ". count($votes). "\n";
print_v($votes);

$votes = $e->get_list_votes($jewish_list);
print "Еврейский список в топ-5: ". count($votes). "\n";
print_v($votes);


print "Результат по Шульце:\n";
print_a($e->count_shultze());

print "\nКворум: ". $e->get_quorum(). "\n";
