<?php
declare(strict_types=1);

require '../vendor/autoload.php';
require_once __DIR__ . '/../src/Models/User.php';
?>
<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="UTF-8">
    <title>User 클래스 데모</title>
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 p-6">
    <div class="max-w-3xl mx-auto space-y-6">
        <h1 class="text-2xl font-bold">User 클래스 데모</h1>

        <div class="bg-white shadow rounded-lg p-4">
            <h2 class="text-lg font-semibold mb-2">User 객체 생성</h2>
            <?php
            $user = new User("김지연", "jykim@sample.com", "password");
            $user->setId(1);
            ?>
            이 부분은 HTML에 보이지 않습니다.
        </div>

        <div class="bg-white shadow rounded-lg p-4">
            <h2 class="text-lg font-semibold mb-2">Getter를 사용하여 불러오기</h2>
            <p><strong>사용자명:</strong> <?= $user->getUsername(); ?></p>
            <p><strong>이메일:</strong> <?= $user->getEmail(); ?></p>
            <p><strong>생성일:</strong> <?= $user->getCreatedAt()->format('Y-m-d H:i:s'); ?></p>
            <p><strong>상태:</strong> <?= $user->isActive() ? "활성" : "비활성"; ?></p>
        </div>

        <div class="bg-white shadow rounded-lg p-4">
            <h2 class="text-lg font-semibold mb-2">비밀번호를 검증합니다</h2>
            <?php if ($user->verifyPassword("password")): ?>
                <p class="text-green-600 font-medium">비밀번호 인증 성공</p>
            <?php else: ?>
                <p class="text-red-600 font-medium">비밀번호 인증 실패</p>
            <?php endif; ?>
        </div>

        <div class="bg-white shadow rounded-lg p-4">
            <h2 class="text-lg font-semibold mb-2">JSON으로 출력합니다</h2>
            <pre class="bg-gray-50 p-2 rounded"><?= $user->toJson(); ?></pre>
        </div>
    </div>
</body>
</html>
