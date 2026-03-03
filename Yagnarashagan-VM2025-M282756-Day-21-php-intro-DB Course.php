<?php
// PHP basics demo: types, strings, numbers, arrays, loops, functions, classes

function hr($title = '') {
	if ($title) echo "\n\n=== $title ===\n";
}

header('Content-Type: text/plain; charset=utf-8');

hr('Strings');
$s1 = "Hello";
$s2 = 'World';
echo "Concatenation: " . $s1 . ' ' . $s2 . "\n"; // dot operator
echo "Interpolation: $s1 $s2\n"; // double-quoted interpolation
var_dump($s1);

hr('Numbers');
$int = 42;
$float = 3.14159;
var_dump($int, $float);

hr('Booleans & Null');
$b = true;
$n = null;
var_dump($b, $n);

hr('Arrays (indexed)');
$arr = [10, 20, 30];
var_dump($arr);

hr('Associative array (like JS object)');
$assoc = ['name' => 'Alice', 'age' => 27];
var_dump($assoc);

hr('Loop: for (index)');
for ($i = 0; $i < count($arr); $i++) {
	echo "arr[$i] = " . $arr[$i] . "\n";
}

hr('Loop: foreach (value) — behaves like JS for...of');
foreach ($arr as $value) {
	echo "value: $value\n";
}

hr('Loop: foreach (key => value) — behaves like JS for...in');
foreach ($assoc as $key => $value) {
	echo "$key => $value\n";
}

hr('Loop: while & do-while');
$count = 3;
while ($count > 0) {
	echo "while countdown: $count\n";
	$count--;
}

$x = 0;
do {
	echo "do-while iteration: $x\n";
	$x++;
} while ($x < 2);

hr('Array iteration with keys (explicit)');
$keys = array_keys($assoc);
foreach ($keys as $k) {
	echo "key: $k, value: " . $assoc[$k] . "\n";
}

hr('String to array (iterate characters)');
$chars = str_split('abc');
foreach ($chars as $ch) echo "char: $ch\n";

hr('Functions');
function add($a, $b) {
	return $a + $b;
}
echo "add(2,3) = " . add(2,3) . "\n";

hr('Array helpers: array_map, array_filter');
$doubled = array_map(fn($v) => $v * 2, $arr);
var_dump($doubled);
$filtered = array_filter($arr, fn($v) => $v > 15);
var_dump($filtered);

hr('Objects and classes');
class Person {
	public string $name;
	public function __construct(string $name) {
		$this->name = $name;
	}
	public function greet(): string {
		return "Hi, I'm " . $this->name;
	}
}
$p = new Person('Bob');
echo $p->greet() . "\n";

hr('Arrays of objects and foreach');
$people = [new Person('Eve'), new Person('Mallory')];
foreach ($people as $person) {
	echo $person->greet() . "\n";
}

hr('Useful functions: implode/explode, json encode/decode');
$csv = implode(',', $arr);
echo "CSV: $csv\n";
$back = explode(',', $csv);
var_dump($back);

$json = json_encode($assoc);
echo "JSON: $json\n";
var_dump(json_decode($json, true));

hr('Operators & ternary/null coalescing');
echo 'Ternary: ' . ($int > 10 ? 'big' : 'small') . "\n";
echo 'Null coalescing: ' . ($assoc['missing'] ?? 'default') . "\n";

hr('Conclusion');
echo "This file demonstrates PHP basics: strings, numbers, arrays, loops (for, foreach, while, do-while), functions, classes, and common helpers.\n";

?>