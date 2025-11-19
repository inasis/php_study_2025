<?php
declare(strict_types=1);

namespace Hazelnut\Presentation\Web\Controller;

use Hazelnut\Application\UseCase\Post\PublishPostUseCase;
use Hazelnut\Application\UseCase\Post\ViewPostUseCase;
use Hazelnut\Application\UseCase\Post\UpdatePostUseCase;
use Hazelnut\Application\UseCase\Post\RemovePostUseCase;
use Hazelnut\Application\DTO\Post\PublishPostCommand;
use Hazelnut\Application\DTO\Post\UpdatePostCommand;

/**
 * 게시물 관련 요청을 처리하는 컨트롤러 (프레젠테이션 계층)
 * Router가 파싱한 순수 배열 데이터($params, $requestData)를 사용하여 Application Layer와 통신합니다.
 */
class PostController
{
    public function __construct(
        private readonly PublishPostUseCase $publishPostUseCase,
        private readonly ViewPostUseCase $viewPostUseCase,
        private readonly UpdatePostUseCase $updatePostUseCase,
        private readonly RemovePostUseCase $removePostUseCase
    ) {}

    /**
     * 새 게시물 생성
     *
     * @param array $params 라우트 파라미터 및 미들웨어 결과
     * @param array $requestData Router::getRequestData에서 JSON 파싱된 HTTP 요청 본문 데이터
     * @return array 웹 응답 렌더링을 위한 데이터 배열
     */
    public function publishPost(array $params): ?object
    {
        // 미들웨어 결과값에서 유효한 AuthResultDTO을 조회합니다.
        $authResultDTO = $params['middleware'];

        if (!$authResultDTO) {
            return null; // 401 Unauthorized
        }

        // Application Command DTO로 변환
        $command = new PublishPostCommand(
            title: $params['requestData']['title'],
            content: $params['requestData']['content'],
            authorId: $authResultDTO->id
        );

        // Application publishPost Use Case 호출
        $postResultDTO = $this->publishPostUseCase->publishPost($command);
        
        // DTO를 반환합니다.
        return $postResultDTO;
    }

    /**
     * 특정 게시물 조회
     *
     * @param array $params 라우트 파라미터
     * @param array $requestData 빈 배열
     * @return array 웹 응답 렌더링을 위한 데이터 배열
     * @throws PostNotFoundException
     */
    public function viewPost(array $params): object
    {
        $postId = (int)($params['id'] ?? 0); // 라우트 파라미터에서 게시물 ID 추출

        // Application viewPost Use Case 호출
        $postResultDTO = $this->viewPostUseCase->viewPost($postId);

        // DTO를 반환합니다.
        return $postResultDTO;
    }

    /**
     * 특정 게시물 수정
     *
     * @param array $params 라우트 파라미터 및 미들웨어 결과
     * @param array $requestData HTTP 요청 본문 데이터
     * @return array 웹 응답 렌더링을 위한 데이터 배열
     * @throws PostNotFoundException, InvalidUpdateException
     */
    public function update(array $params): ?object
    {
        // 미들웨어 결과값에서 유효한 AuthResultDTO을 조회합니다.
        $authResultDTO = $params['middleware'];

        if (!$authResultDTO) {
            return null; // 401 Unauthorized
        }

        if ($authResultDTO->email !== $params['email']) {
            return null; // 403 Forbidden
        }

        $postId = (int)($params['id'] ?? 0); // 라우트 파라미터에서 ID 추출

        // Application Command 생성
        $updatePostCommand = new UpdatePostCommand(
            id: $postId,
            title: $params['requestData']['title'] ?? null,
            content: $params['requestData']['content'] ?? null
        );

        // Application Use Case 호출
        /** @var PostpostResultDTO $postResultDTO */
        $postResultDTO = $this->updatePostUseCase->updatePost($updatePostCommand);

        // DTO를 반환합니다.
        return $postResultDTO;
    }

    /**
     * 특정 게시물 삭제
     *
     * @param array $params 라우트 파라미터 및 미들웨어 결과
     * @param array $requestData 빈 배열
     * @return array 웹 응답 렌더링을 위한 데이터 배열 (성공 시 보통 빈 배열이나 성공 메시지)
     * @throws PostNotFoundException
     */
    public function removePost(array $params, array $requestData): bool
    {
        // 미들웨어 결과값에서 유효한 AuthResultDTO을 조회합니다.
        $authResultDTO = $params['middleware'];

        if (!$authResultDTO) {
            return false; // 401 Unauthorized
        }

        if ($authResultDTO->email !== $params['email']) {
            return false; // 403 Forbidden
        }

        $postId = (int)($params['id'] ?? 0); // 라우트 파라미터에서 ID 추출

        // Application removePost Use Case 호출
        $this->removePostUseCase->removePost($postId);
        
        // 라우터가 204 No Content로 매핑 가능
        return true;
    }
}