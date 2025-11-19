<?php
declare(strict_types=1);

require(__DIR__ . "/../vendor/autoload.php");

use Hazelnut\Infrastructure\Commence\Bootstrap;
use Hazelnut\Infrastructure\Routing\Router;

use ExpoOne\Engine;

$container = Bootstrap::initialize();

// 라우터 디스패치
/** @var Router $router */
try {
    $router = $container->get(Router::class);
    $result = $router->dispatch(
        $_SERVER['REQUEST_METHOD'] ?? 'GET',
        $_SERVER['REQUEST_URI'] ?? '/'
    );
} catch (\Throwable $e) {
    // 라우팅 또는 컨트롤러 실행 중 발생한 예외 처리 (JSON 응답으로 전환)
    $pos = strpos($e->getTrace()[0]['file'], "src/");
    if ($pos !== false) {
        $path = substr($e->getTrace()[0]['file'], $pos);
    }
    $result  = [
        'status' => 'Route has an unexpected error occurred during dispatch in '.$path,
        'message' => $e->getMessage(),
        // 개발 환경이 아닐 경우 trace 출력은 생략합니다.
        'trace' => defined('APP_ENV') && APP_ENV !== 'production' ? $e->getTrace() : null,
    ];
}
/*
// POST 없이 회원가입을 진행합니다.
use Hazelnut\Presentation\Web\Controller\UserController;
$userController = $container->get(UserController::class);

$vars = []; // URI 파라미터는 POST에 없습니다.
$requestData = [
    'email' => 'inas@mail.com',
    'password' => 'SecureP@ss123',
    'name' => '인야'
];
try {
    $register = $userController->registerUser($vars, $requestData);
    $result = [
        'title' => '회원가입 성공',
        'result' => $register,
    ];
} catch (Exception $e) {
    $result = [
        'title' => '회원가입 실패',
        'message' => $e->getMessage(),
        'trace' => $e->getTrace()
    ];
}

// POST 없이 로그인을 진행합니다
use Hazelnut\Presentation\Web\Controller\AuthenticationController;
$authController = $container->get(AuthenticationController::class);

$vars = []; // URI 파라미터는 POST에 없습니다.
$requestData = [
    'email' => 'inas@mail.com',
    'password' => 'SecureP@ss123'
];
$login = [];
*/
/*
try {
    $authResultDTO = $authController->login($vars, $requestData);
    $result = [
        'title' => '로그인 성공',
        'message' => $authResultDTO
    ];
} catch (Exception $e) {
    $result = [
        'title' => '로그인 실패',
        'message' => $e->getMessage(),
        'trace' => $e->getTrace()
    ];

    header('Content-Type: application/json; charset=utf-8');
    header('HTTP/1.1 200 OK');

    echo json_encode($result, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
    exit;
}

$accessToken = $authResultDTO->tokens->accessToken ?? null;
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

$refreshToken = $authResultDTO->tokens->refreshToken;
$refreshExpiration = time() + (60 * 60 * 24 * 7); // 7일 만료 (예시)

if ($refreshToken) {
    setcookie(
        'refresh_token',
        $refreshToken,
        [
            'expires' => $refreshExpiration,
            'path' => '/', // 재발급 엔드포인트에서만 사용
            'domain' => null,       // localhost에서는 domain 설정 제거
            'secure' => false,      // 수정: HTTP 환경이므로 false로 설정
            'httponly' => true,
            'samesite' => 'Lax',    // localhost 환경에서 테스트 용이성을 위해 Lax로 변경
        ]
    );
}
*/
/*
$params['middleware']= $authResultDTO;
// POST 없이 임의로 글을 생성합니다.
use Hazelnut\Presentation\Web\Controller\PostController;
$controller = $container->get(PostController::class)->publishPost($params,['title' => 'Post title', 'content' => 'lorem ipsum dolor sit amet']);
*/

if (is_array($result) && isset($result['view']) && is_string($result['view'][0])) {
    /** @var Engine $engine */
    try {
        $engine = $container->get(Engine::class);
        $viewer = $result['view'][0];
        
        // 템플릿 렌더링 실행
        $htmlOutput = $engine->render(
            $viewer, 
            $result ?? [] // 데이터가 없을 경우 빈 배열 전달
        );

        // HTML 응답 헤더 설정 및 출력
        header('Content-Type: text/html; charset=utf-8');
        header('HTTP/1.1 200 OK');
        echo $htmlOutput;
        
        exit;
    } catch (\Throwable $e) {
        // 템플릿 렌더링 중 발생한 예외 처리 (JSON 응답으로 전환)
        $result  = [
            'status' => 'template_error',
            'message' => 'File: '.$e->getFile().' Template rendering failed.',
            'error_details' => $e->getMessage() .' in line '.$e->getLine(),
            'view_attempted' => $result['view'],
        ];
    }
}

if(is_array($result)) {
    header('Content-Type: application/json; charset=utf-8');
    header('HTTP/1.1 200 OK');

    echo json_encode($result, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
    exit;
}
echo $result;
exit;