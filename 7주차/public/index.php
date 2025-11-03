<?php
declare(strict_types=1);

require(__DIR__ . "/../vendor/autoload.php");

use Ginger\Bootstrap;
use Ginger\Infrastructure\Routing\Router;
use Ginger\Exception\ExceptionHandler;

$container = Bootstrap::initialize(); 

// 예외 핸들러 등록
$exceptionHandler = $container->get(ExceptionHandler::class);
$exceptionHandler->register();

// 라우터 디스패치
$result = $container->get(Router::class)->dispatch(
    $_SERVER['REQUEST_METHOD'],
    $_SERVER['REQUEST_URI']
);

// POST 없이 임의로 글을 생성합니다.
// use Ginger\Controller\PostController;
// $controller = $container->get(PostController::class)->create(['title' => 'Post title', 'content' => 'lorem ipsum dolor sit amet']);

// POST 없이 회원가입을 진행합니다.
/*
use Ginger\Controller\UserController;
$userController = $container->get(UserController::class);

$vars = []; // URI 파라미터는 POST에 없습니다.
$requestData = [
    'email' => 'test@example.com',
    'password' => 'SecureP@ss123',
    'name' => 'Test User'
];
try {
    $register = $userController->create($vars, $requestData);
    $result = [
        'title' => '회원가입 성공',
        'result' => $register,
    ];
} catch (Exception $e) {
    $result = [
        'title' => '회원가입 실패',
        'message' => $e->getMessage(),
    ];
}
*/

// POST 없이 로그인을 진행합니다
/*
use Ginger\Controller\AuthenticationController;
$authController = $container->get(AuthenticationController::class);

$vars = []; // URI 파라미터는 POST에 없습니다.
$requestData = [
    'email' => 'test@example.com',
    'password' => 'SecureP@ss123'
];

try {fh
    $login = $authController->login($vars, $requestData);
    $result2 = [
        'title' => '로그인 성공',
        'message' => $login
    ];
} catch (Exception $e) {
    $result2 = [
        'title' => '로그인 실패',
        'message' => $e->getMessage(),
    ];
}

$accessToken = $login['tokens']->toArray()['accessToken'] ?? null;
$accessExpiration = time() + (60 * 15); // 15분 만료 (예시)

if ($accessToken) {
    setcookie(
        'access_token',
        $accessToken,
        [
            'expires' => $accessExpiration,
            'path' => '/',
            'domain' => null,       // localhost에서는 domain 설정 제거
            'secure' => false,      // 수정: HTTP 환경이므로 false로 설정
            'httponly' => true,
            'samesite' => 'Lax',    // localhost 환경에서 테스트 용이성을 위해 Lax로 변경
        ]
    );
}

$refreshToken = $login['tokens']->toArray()['refreshToken'] ?? null;
$refreshExpiration = time() + (60 * 60 * 24 * 7); // 7일 만료 (예시)

if ($refreshToken) {
    setcookie(
        'refresh_token',
        $refreshToken,
        [
            'expires' => $refreshExpiration,
            'path' => '/token/refresh', // 재발급 엔드포인트에서만 사용
            'domain' => null,       // localhost에서는 domain 설정 제거
            'secure' => false,      // 수정: HTTP 환경이므로 false로 설정
            'httponly' => true,
            'samesite' => 'Lax',    // localhost 환경에서 테스트 용이성을 위해 Lax로 변경
        ]
    );
}
*/

// 굉장히 작고 소중한 View를 index에 직접 만들었습니다.
header('Content-Type: application/json; charset=utf-8');
header('HTTP/1.1 200 OK');

echo json_encode($result, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);

exit;