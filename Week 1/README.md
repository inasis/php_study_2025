# PHP 기초 가이드

## PHP 개발 환경 설정하기

웹 개발을 위해 Thread-Safe 버전인 `php-8.4.11-Win32-vs17-x64.zip` 파일을 다운로드한 후, 작업하기 편한 위치에 압축을 풀어주세요.

**설치 경로 예시:**
```
C:\php-8.4.11-Win32-vs17-x64
```

### PHP 서버 실행하기

명령 프롬프트(CMD)를 열고 다음 명령어를 입력하면 PHP 개발 서버가 시작됩니다:

```bash
C:
cd C:\php-8.4.11-Win32-vs17-x64
php -S localhost:8000
```

서버를 종료하고 싶을 때는 `Ctrl + C`를 누르면 됩니다.

### 프로젝트 폴더 구조 만들기

php-8.4.11-Win32-vs17-x64 폴더 안에 프로젝트용 폴더를 만들어주세요. 여기서는 `htdocs`라는 이름으로 만들겠습니다.

```bash
php -S localhost:8000 -t htdocs
```

이제 브라우저에서 `localhost:8000`을 입력하면 `htdocs` 폴더의 `index.php` 파일이 실행됩니다.

## PHP 문법 기초

### PHP 코드 작성하기

PHP 코드는 `<?php`로 시작해서 `?>`로 끝납니다:

```php
<?php
  echo "Hello world, Welcome to PHP!";
?>
```

**알아두면 좋은 팁:**
- HTML과 PHP를 함께 사용할 수 있습니다
- 순수 PHP 파일의 경우 마지막 `?>`는 생략하는 것이 관례입니다

### 주석 작성하기

코드에 설명을 달고 싶을 때는 주석을 사용합니다:

```php
// 이것은 한 줄 주석입니다
# 이것도 한 줄 주석이에요

/*
이렇게 여러 줄에 걸쳐
주석을 작성할 수도 있습니다
*/
```

개발자들은 주로 `//`와 `/* */`를 많이 사용합니다.

## 변수와 데이터 다루기

### 변수 선언하기

PHP에서 변수는 `$` 기호로 시작합니다. 타입을 미리 선언할 필요가 없습니다.

```php
$userName = "김민수";        // 문자열
$userAge = 28;              // 정수
$userHeight = 175.8;        // 실수
$isLoggedIn = true;         // 불리언(참/거짓)
```

**변수 확인하기:**
```php
var_dump($userName);  // 변수의 타입과 값을 자세히 보여줍니다
```

**엄격한 타입 검사 사용하기:**
```php
declare(strict_types=1);  // 파일 코드 최상단에 작성해야 합니다

$score = "85";      // 문자열
$score = 85;        // 일반적으로는 자동 변환되지만, strict_types=1 모드에서는 오류 발생
```

### 상수 정의하기

한 번 정하면 변경되지 않는 값을 상수라고 합니다:

```php
define("WEBSITE_NAME", "웹사이트 이름");
echo WEBSITE_NAME;  // "웹사이트 이름" 출력
```

**주의사항:** 상수는 실제로 실행되는 코드 부분에서만 정의됩니다:

```php
$number = 5;
if($number == 3) {
    define("MY_CONSTANT", 100);  // 이 코드는 실행되지 않음
}
echo MY_CONSTANT * 2;  // 오류 발생!
```

## 화면에 출력하기

```php
echo "화면에 글자를 보여줍니다";
```

`echo`를 사용하면 웹 페이지에 텍스트나 HTML을 출력할 수 있습니다.

## 계산하기 (연산자)

PHP로 다양한 계산을 할 수 있습니다:

```php
$a = 10;
$b = 3;

echo $a + $b;   // 13 (덧셈)
echo $a - $b;   // 7 (뺄셈)
echo $a * $b;   // 30 (곱셈)
echo $a / $b;   // 3.33... (나눗셈)
echo $a % $b;   // 1 (나머지)
echo $a ** $b;  // 1000 (거듭제곱: 10의 3제곱)
```

**비교와 논리 연산자:**
- `==` : 값이 같은지 확인
- `===` : 값과 타입이 모두 같은지 확인
- `&&` : 그리고(AND)
- `||` : 또는(OR)
- `!` : 아니다(NOT)

## 조건에 따라 다르게 실행하기

사용자의 나이에 따라 다른 메시지를 보여주는 예시입니다:

```php
$age = 20;

if ($age >= 18) {
    echo "성인입니다";
} elseif ($age >= 13) {
    echo "청소년입니다";
} else {
    echo "어린이입니다";
}
```

**간단한 조건문 (삼항 연산자):**
```php
$message = ($age >= 18) ? "성인" : "미성년자";
echo $message;
```

## 반복 작업 자동화하기

### for 반복문 - 정해진 횟수만큼 반복
```php
for ($i = 1; $i <= 5; $i++) {
    echo "현재 숫자: {$i}<br>";
}
```

### while 반복문 - 조건이 맞는 동안 계속 반복
```php
$count = 1;
while ($count <= 3) {
    echo "카운트: {$count}<br>";
    $count++;
}
```

### foreach 반복문 - 배열의 모든 항목 처리
```php
$hobbies = ["독서", "영화감상", "요리", "여행"];
foreach ($hobbies as $hobby) {
    echo "취미: {$hobby}<br>";
}
```

## 배열로 여러 데이터 관리하기

### 순서가 있는 배열 (인덱스 배열)
```php
$colors = ["빨강", "파랑", "초록", "노랑"];
echo $colors[0];  // "빨강" 출력 (첫 번째 항목)
echo $colors[2];  // "초록" 출력 (세 번째 항목)
```

### 이름으로 관리하는 배열 (연관 배열)
```php
$student = [
    "name" => "이영희",
    "age" => 22,
    "major" => "컴퓨터공학"
];

echo $student["name"];   // "이영희"
echo $student["major"];  // "컴퓨터공학"
```

**유용한 배열 함수들:**
- `count($배열)` : 배열의 길이 확인
- `array_push($배열, 새항목)` : 배열에 새 항목 추가
- `array_merge($배열1, $배열2)` : 두 배열 합치기

## 함수로 코드 재사용하기

반복되는 코드를 함수로 만들어 효율적으로 관리할 수 있습니다:

```php
function createGreeting($name = "방문자"): string {
    return "안녕하세요, {$name}님! 좋은 하루 되세요!";
}

echo createGreeting();           // "안녕하세요, 방문자님! 좋은 하루 되세요!"
echo createGreeting("홍길동");    // "안녕하세요, 홍길동님! 좋은 하루 되세요!"
```

**함수의 구성 요소:**
- `function` : 함수를 만든다는 키워드
- 매개변수에 기본값 설정 가능
- `: string` : 반환 타입 명시 (선택사항)

## 파일 나누어 관리하기

코드가 길어지면 여러 파일로 나누어 관리하는 것이 좋습니다:

```php
include 'header.php';    // 파일이 없어도 경고만 하고 계속 실행
require 'config.php';    // 파일이 없으면 오류를 내고 중단
```

**차이점:**
- `include` : 파일을 찾지 못해도 프로그램이 계속 실행됩니다
- `require` : 파일을 반드시 필요로 하며, 없으면 프로그램이 중단됩니다

## 프로젝트 구조 개선하기

실제 웹 프로젝트에서는 public 폴더와 src 폴더를 분리하는 것이 좋습니다:

**폴더 구조:**
```
htdocs/
├── public/
│   └── index.php
└── src/
    └── index.php
```

**public/index.php:**
```php
<?php
declare(strict_types=1);
require '../vendor/autoload.php';
require '../src/index.php';
```

**서버 실행:**
```bash
php -S localhost:8000 -t htdocs/public
```

이제 브라우저에서 `localhost:8000`을 입력하면 public 폴더의 index.php가 실행되고, 이 파일이 src 폴더의 실제 코드를 불러옵니다.

## Composer로 패키지 관리하기

Composer는 PHP의 패키지 관리 도구입니다. `composer init` 명령으로 생성된 설정 파일 예시입니다:

**composer.json:**
```json
{
    "name": "myname/awesome-project",
    "description": "PHP 프로젝트",
    "autoload": {
        "psr-4": {
            "MyName\\AwesomeProject\\": "src/"
        }
    },
    "require": {
        "nesbot/carbon": "^3.10"
    }
}
```

**외부 라이브러리 사용 예시:**
```php
require 'vendor/autoload.php';
use Carbon\Carbon;

echo Carbon::now()->format('Y년 m월 d일');  // 현재 날짜를 한국어 형식으로 출력
```

Carbon은 날짜와 시간을 쉽게 다룰 수 있게 해주는 라이브러리입니다. `composer require nesbot/carbon` 명령으로 설치할 수 있습니다.
