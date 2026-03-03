<?php
// TNSkills - PHP basics examples (plain PHP, no HTML)
// Save as tnskills_asgardia.php and run with: php -S localhost:8000
header('Content-Type: text/plain; charset=utf-8');

function hr($title){
    echo "\n=== $title ===\n";
}

// 1) Functions
hr('Functions');
function add($a, $b){ return $a + $b; }
function mul($a, $b){ return $a * $b; }
function factorial($n){ if($n <= 1) return 1; $res=1; for($i=2;$i<=$n;$i++) $res *= $i; return $res; }
function greet($name = 'Guest'){ $n = trim($name); return $n === '' ? 'Hello, Guest!' : "Hello, $n!"; }

echo "add(5,7) = " . add(5,7) . "\n";
echo "mul(4,6) = " . mul(4,6) . "\n";
echo "factorial(5) = " . factorial(5) . "\n";
echo greet('Ibrahim') . "\n";

// 2) Conditional statements
hr('Conditionals');
$mark = 72;
if($mark >= 90) echo "Grade: A+\n";
elseif($mark >= 75) echo "Grade: A\n";
elseif($mark >= 60) echo "Grade: B\n";
else echo "Grade: C or below\n";

$day = 3;
switch($day){
  case 1: echo "Monday\n"; break;
  case 2: echo "Tuesday\n"; break;
  case 3: echo "Wednesday\n"; break;
  default: echo "Other day\n"; break;
}

// 3) Operators
hr('Operators');
$a=15; $b=4;
echo "Arithmetic: $a + $b = " . ($a+$b) . "\n";
echo "Modulus: $a % $b = " . ($a % $b) . "\n";
$c = 5; $c += 3; echo "Assignment (+=): 5 += 3 -> $c\n";
echo "Comparison (==): " . ($a == $b ? 'true' : 'false') . "\n";
echo "Strict comparison (=== '5' === 5): " . ('5' === 5 ? 'true' : 'false') . "\n";
echo "Logical (true && false): " . ((true && false) ? 'true' : 'false') . "\n";

// 4) var_dump and printf
hr('var_dump and printf');
$arr = [1,2,3];
echo "Using var_dump on array:\n";
var_dump($arr);

$person = (object)['name'=>'Alice','age'=>27];
echo "Using var_dump on object:\n";
var_dump($person);

printf("Formatted: %s has %d points\n", 'Bob', 85);

// 5) Join (implode) and explode
hr('Join (implode) and explode');
$words = ['php','is','fun'];
$joined = implode(' ', $words); // join
echo "Joined: $joined\n";
$split = explode(' ', $joined);
echo "Exploded back: "; var_dump($split);

// 6) String functions
hr('String functions');
$s = "  Hello PHP World  ";
echo "Original: '" . $s . "'\n";
echo "trim => '" . trim($s) . "'\n";
echo "strlen => " . strlen($s) . "\n";
echo "strtoupper => " . strtoupper($s) . "\n";
echo "strtolower => " . strtolower($s) . "\n";
echo "strpos('PHP') in string => ";
$pos = strpos($s, 'PHP');
echo ($pos === false ? 'not found' : $pos) . "\n";
echo "substr(2,5) => " . substr($s, 2, 5) . "\n";
echo "str_replace 'PHP' -> 'Web' => " . str_replace('PHP','Web',$s) . "\n";

// 7) Arrays and loops
hr('Arrays & loops');
$nums = [1,2,3,4,5];
echo "For loop outputs:\n";
for($i=0;$i<count($nums);$i++) echo "index $i => {$nums[$i]}\n";
echo "Foreach outputs:\n";
foreach($nums as $n) echo "$n\n";

// 8) Superglobals example (GET)
hr('Superglobals (GET example)');
echo "Access query params via \\$_GET. Example: visit tnskills_asgardia.php?name=Sam&age=20\n";
if(isset($_GET['name'])){
  echo "Hello, " . $_GET['name'] . "!\n";
}

// 9) Notes
hr('Notes');
echo "This file is pure PHP and prints examples to the browser (text/plain) or terminal.\n";
echo "Topics included: functions, conditionals, operators, var_dump, printf, implode/explode (join), string functions, arrays, loops, superglobals.\n";

?>