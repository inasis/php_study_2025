<?php

namespace Ginger\Controller;

use Ginger\Service\jwtServiceInterface;

class JwksController
{
    public function __construct(
        private JwtServiceInterface $jwtService,
    ) {}

    /**
     * GET /.well-known/jwks.json
     * JWKS 엔드포인트 (공개키 제공)
     */
    public function getPublicKeys(): void
    {
        try {
            $publicKey = $this->jwtService->getPublicKey();
            $algorithm = $this->jwtService->getAlgorithm();

            $keyDetails = openssl_pkey_get_details(openssl_pkey_get_public($publicKey));

            if (!$keyDetails || !isset($keyDetails['rsa'])) {
                throw new \Exception('Failed to parse public key');
            }

            $rsa = $keyDetails['rsa'];

            $base64url = function($data) {
                return rtrim(strtr(base64_encode($data), '+/', '-_'), '=');
            };

            $jwk = [
                'kty' => 'RSA',
                'alg' => $algorithm,
                'use' => 'sig',
                'n' => $base64url($rsa['n']),
                'e' => $base64url($rsa['e']),
                'kid' => hash('sha256', $publicKey),
            ];

            $this->sendResponse(['keys' => [$jwk]], 200);
        } catch (\Exception $e) {
            $this->sendResponse(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * JSON 응답 전송
     */
    private function sendResponse(array $data, int $statusCode = 200): void
    {
        http_response_code($statusCode);
        header('Content-Type: application/json');
        echo json_encode($data);
    }
}