## PHP 설치
웹 페이지를 만들기 위하여 Thread-Safe 버전인 php-8.4.11-Win32-vs17-x64.zip 을 사용합니다.
접근이 편리한 위치에 압축 해제합니다.

예) C:\php-8.4.11-Win32-vs17-x64

위와 동일한 위치에 압축해제한 경우 아래의 명령어를 CMD에 입력하면 PHP 서버가 실행됩니다.
```shell
C:
cd C:\php-8.4.11-Win32-vs17-x64
php -S localhost:8000
```

Ctrl + C로 PHP 서버를 종료할 수 있습니다.

php-8.4.11-Win32-vs17-x64 폴더에 프로젝트명으로 폴더를 생성합니다.
이번은 htdocs로 폴더를 만들었습니다.

아래의 명령어를 CMD에 입력하면 PHP 서버가 실행됩니다.
```shell
php -S localhost:8000 -t htdocs
```
localhost:8000 을 Chrome에 입력하면 htdocs 폴더의 index.php가 실행됩니다.


## PHP 시작 태그
```php
<?php
  echo "Hello, PHP!";
```
- `<?php ` 와 `?>` 사이에 PHP 코드를 작성합니다.
- HTML과 함께 사용 가능합니다.
- PHP 코드만으로 작성하는 경우 `<?php ` 로 시작하고, 마지막 닫는 괄호를 작성하지 않습니다.

## PHP 주석
```php
// 한 줄 주석
# 한 줄 주석
/*
여러 줄
주석
*/
```
주석은 보통 //와 /* */ 를 대중적으로 사용합니다.

## 변수와 데이터 타입
```php
$name = "John";       // 문자열
$age = 25;            // 정수
$height = 175.5;      // 실수
$isStudent = true;    // 불리언
```

- `$` 기호로 시작합니다
- 타입 선언 없이 사용 가능합니다
- PHP 헬퍼 함수 `var_dump($변수)`로 타입과 값을 확인할 수 있습니다.

- declare(strict_types=1); 을 최상단에 사용하면 자동 형변환을 금지하게 됩니다.
```php
declare(strict_types=1);

$variable = "Doe";    // 문자열
 // 일반적인 경우 아래는 "25"로 변환되지만 declare(strict_types=1); 으로 오류 발생
$variable = 25;      
```

## 상수
```php
define("SITE_NAME", "My site");
echo SITE_NAME;
```
- `define()` 으로 상수 선언
- 한 번 정의하면 변경이 불가능합니다
- 코드를 실행하는 로직에 존재하는 경우에만 정의됩니다.

```php
$number = 5;

if($number == 3) {
    define("DEFINED_NUMBER", 3);
}

echo DEFINED_NUMBER * 5; // 오류, if 문 안의 코드는 실행되지 않았기 때문입니다.
```


## 출력
```php
echo "안녕하세요";
```
- `echo`로 문자열을 출력합니다.

---

## 연산자
```php
$a = 10;
$b = 3;

echo $a + $b; // 덧셈
echo $a % $b; // 나머지
echo $a ** $b; // 거듭제곱
```
- 산술, 비교(`==`, `===`), 논리(`&&`, `||`, `!`) 연산자 또한 지원합니다

---

## 조건문
```php
if ($age >= 18) {
    echo "성인";
} elseif ($age >= 13) {
    echo "청소년";
} else {
    echo "어린이";
}
```
- `if`, `elseif`, `else` 사용
- 삼항 연산자:  
  ```php
  $result = ($age >= 18) ? "성인" : "미성년자";
  ```

---

## 반복문
```php
for ($i = 0; $i < 5; $i++) {
    echo $i;
}

$counter = 0;
while ($counter < 3) {
    echo $counter;
    $counter++;
}

$fruits = ["사과", "바나나", "포도"];
foreach ($fruits as $fruit) {
    echo $fruit;
}
```
- `for`, `while`, `foreach` 를 지원합니다.
- `foreach`는 배열에 최적화되어 있습니다

---

## 배열
```php
// 인덱스 배열
$colors = ["빨강", "파랑", "초록"];
echo $colors[0];

// 연관 배열
$user = ["name" => "Amy", "age" => 21];
echo $user["name"];
```
- 배열 함수: `count()`, `array_push()`, `array_merge()`

---

## 함수
```php
function greet($name = "김짱구"): string {
    return "안녕하세요, {$name}님!";
}

echo greet(); // 안녕하세요, 김짱구님!
echo greet("이철수");  // 안녕하세요, 이철수님!
```
- `function` 키워드로 정의
- 매개변수 기본값을 정의할 수 있습니다


## include와 require
```php
include 'header.php';
require 'config.php';
```
- `include`: 파일 불러오기, 파일이 없으면 경고하고 계속 실행합니다.
- `require`: 파일 불러오기. 파일이 없으면 오류를 내고 종료합니다


## public과 src 분리
```php
<?php
declare(strict_types=1);
require '../vendor/autoload.php';

require '../src/index.php';
```

```shell
php -S localhost:8000 -t htdocs/public
```
localhost:8000 을 Chrome에 입력하면 htdocs/public 폴더의 index.php가 실행됩니다.
index.php는 src 폴더의 index.php 파일을 불러오게 됩니다.


## Composer init로 생성된 composer.json 파일
```json
{
    "name": "inas/sample", // "사용자명/프로젝트명"으로 만들어집니다.
    "autoload": {
        "psr-4": {
            "Inas\\sample\\": "src/"  // src를 프로젝트 폴더로 정합니다.
        }
    }, // require 부분은 일반적으로 composer가 직접 작성합니다.
    "require": {
        "nesbot/carbon": "^3.10"
    }
}
```


## Composer Autoload 예시
```php
require 'vendor/autoload.php';

use Carbon\Carbon;

echo Carbon::now();
```
- `composer require nesbot/carbon` 설치 후 사용 가능
- `autoload.php`파일으로 라이브러리 자동 로드
