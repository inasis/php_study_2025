<?php
declare(strict_types=1);
namespace Inas\sample;

use Carbon\Carbon;

const ANIMAL = ['Dog', 'Bird', 'Cat'];

function intNumber(int|float $num): int {
    return (int) $num;
}

function setAnimal(int $number): string {
    return ANIMAL[$number];
}

$a = intNumber(83.5);
$b = intNumber(120);

// PHP는 문자열을 연결하는 경우 + 가 아닌 . 으로 연결합니다.
// 줄바꿈을 위하여 HTML을 사용합니다.
echo "A의 값: " . $a . "<br>";
echo "B의 값: " . $b . "<br>";
echo "A*B: " . $a*$b . "<br>";

if($a == $b) {
    echo "A와 B의 값은 동일합니다.<br>";
} else {
    echo "A와 B의 값은 동일하지 않습니다.<br>";
}
    

$animal = setAnimal(2);
echo $animal . "<br>";

echo "현재 시간 (UTC): " . Carbon::now();