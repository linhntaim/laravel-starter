<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\ApiResponseTrait;
use App\Utils\Helper;
use Laminas\Diactoros\Response as Psr7Response;
use Laravel\Passport\Exceptions\OAuthServerException;
use Laravel\Passport\Http\Controllers\AccessTokenController;
use Laravel\Passport\TokenRepository;
use Lcobucci\JWT\Parser as JwtParser;
use League\OAuth2\Server\AuthorizationServer;
use League\OAuth2\Server\Exception\OAuthServerException as LeagueException;
use Psr\Http\Message\ServerRequestInterface;

class LoginController extends AccessTokenController
{
    use ApiResponseTrait;

    public function __construct(AuthorizationServer $server, TokenRepository $tokens, JwtParser $jwt)
    {
        parent::__construct($server, $tokens, $jwt);

        $this->inlineMiddleware();
    }

    public function issueToken(ServerRequestInterface $request)
    {
        $parsedBody = $request->getParsedBody();
        if (isset($parsedBody['client_id'])) {
            if (!Helper::isUnsignedInteger($parsedBody['client_id'])) {
                return $this->throwException(LeagueException::invalidClient($request));
            }
        }
        return parent::issueToken($request);
    }

    protected function withErrorHandling($callback)
    {
        try {
            return $callback();
        } catch (LeagueException $e) {
            return $this->throwException($e);
        }
    }

    protected function throwException(LeagueException $e)
    {
        $e->setPayload(static::failPayload(null, $e, 401));
        throw new OAuthServerException(
            $e,
            $this->convertResponse($e->generateHttpResponse(new Psr7Response))
        );
    }
}
