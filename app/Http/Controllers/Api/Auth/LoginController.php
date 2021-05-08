<?php

/**
 * Base - Any modification needs to be approved, except the space inside the block of TODO
 */

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\ApiResponseTrait;
use App\ModelRepositories\OAuthImpersonateRepository;
use App\Utils\ConfigHelper;
use App\Vendors\Illuminate\Support\Str;
use Illuminate\Http\Response;
use Laravel\Passport\Exceptions\OAuthServerException;
use Laravel\Passport\Http\Controllers\AccessTokenController;
use Laravel\Passport\TokenRepository;
use Lcobucci\JWT\Parser as JwtParser;
use League\OAuth2\Server\AuthorizationServer;
use League\OAuth2\Server\Exception\OAuthServerException as LeagueException;
use Nyholm\Psr7\Response as Psr7Response;
use Psr\Http\Message\ServerRequestInterface;

abstract class LoginController extends AccessTokenController
{
    use ApiResponseTrait;

    public function __construct(AuthorizationServer $server, TokenRepository $tokens, JwtParser $jwt)
    {
        parent::__construct($server, $tokens, $jwt);

        $this->inlineMiddleware();
    }

    /**
     * @param Response $response
     * @param string|null $impersonateToken
     * @return Response
     * @throws
     */
    private function impersonate($response, $impersonateToken)
    {
        if (!is_null($impersonateToken) && ConfigHelper::get('impersonated_by_admin')) {
            $parsedToken = $this->jwt->parse(
                json_decode($response->getContent(), true)['_data']['access_token']
            );
            $accessTokenId = null;
            if (method_exists($parsedToken, 'getClaim')) { // support lcobucci/jwt@3.x
                $accessTokenId = $parsedToken->getClaim('jti');
            }
            elseif (method_exists($parsedToken, 'claims')) { // support lcobucci/jwt@4.x
                $accessTokenId = $parsedToken->claims()->get('jti');;
            }
            $oAuthImpersonateRepository = new OAuthImpersonateRepository();
            $oAuthImpersonateRepository->pinModel()->getByImpersonateToken($impersonateToken);
            $oAuthImpersonateRepository->updateWithAttributes([
                'access_token_id' => $accessTokenId,
            ]);
        }
        return $response;
    }

    public function issueToken(ServerRequestInterface $request)
    {
        $parsedBody = $request->getParsedBody();
        if (isset($parsedBody['client_id'])) {
            if (!Str::isUnsignedInteger($parsedBody['client_id'])) {
                $this->throwException(LeagueException::invalidClient($request));
            }
        }
        return $this->impersonate(
            parent::issueToken($request),
            $parsedBody['impersonate_token'] ?? null
        );
    }

    protected function withErrorHandling($callback)
    {
        try {
            return $callback();
        }
        catch (LeagueException $e) {
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

    public function convertResponse($psrResponse)
    {
        return parent::convertResponse($psrResponse)
            ->setStatusCode(ConfigHelper::getApiResponseStatus($psrResponse->getStatusCode()));
    }
}
