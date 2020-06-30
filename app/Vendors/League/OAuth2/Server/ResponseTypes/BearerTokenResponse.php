<?php

namespace App\Vendors\League\OAuth2\Server\ResponseTypes;

use App\Http\Controllers\ApiController;
use League\OAuth2\Server\Entities\RefreshTokenEntityInterface;
use League\OAuth2\Server\ResponseTypes\BearerTokenResponse as BaseBearerTokenResponse;
use LogicException;
use Psr\Http\Message\ResponseInterface;

class BearerTokenResponse extends BaseBearerTokenResponse
{
    public function generateHttpResponse(ResponseInterface $response)
    {
        $expireDateTime = $this->accessToken->getExpiryDateTime()->getTimestamp();

        $responseParams = [
            'token_type' => 'Bearer',
            'expires_in' => $expireDateTime - \time(),
            'access_token' => (string)$this->accessToken,
        ];

        if ($this->refreshToken instanceof RefreshTokenEntityInterface) {
            $refreshTokenPayload = json_encode([
                'client_id' => $this->accessToken->getClient()->getIdentifier(),
                'refresh_token_id' => $this->refreshToken->getIdentifier(),
                'access_token_id' => $this->accessToken->getIdentifier(),
                'scopes' => $this->accessToken->getScopes(),
                'user_id' => $this->accessToken->getUserIdentifier(),
                'expire_time' => $this->refreshToken->getExpiryDateTime()->getTimestamp(),
            ]);

            if ($refreshTokenPayload === false) {
                throw new LogicException('Error encountered JSON encoding the refresh token payload');
            }

            $responseParams['refresh_token'] = $this->encrypt($refreshTokenPayload);
        }

        $responseParams = json_encode(ApiController::successPayload(array_merge($this->getExtraParams($this->accessToken), $responseParams)));

        if ($responseParams === false) {
            throw new LogicException('Error encountered JSON encoding response parameters');
        }

        $response = $response
            ->withStatus(200)
            ->withHeader('pragma', 'no-cache')
            ->withHeader('cache-control', 'no-store')
            ->withHeader('content-type', 'application/json; charset=UTF-8');

        $response->getBody()->write($responseParams);

        return $response;
    }
}
