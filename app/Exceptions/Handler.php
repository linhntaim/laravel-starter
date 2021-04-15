<?php

/**
 * Base - Any modification needs to be approved, except the space inside the block of TODO
 */

namespace App\Exceptions;

use App\Http\Controllers\ApiController;
use App\Http\Middleware\Web\ViewShare;
use App\Utils\ConfigHelper;
use App\Utils\Database\Transaction\TransactionManager;
use App\Utils\Theme\ThemeFacade;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Http\Exceptions\ThrottleRequestsException;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\ViewErrorBag;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Throwable;

class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that are not reported.
     *
     * @var array
     */
    protected $dontReport = [
        //
    ];

    /**
     * A list of the inputs that are never flashed for validation exceptions.
     *
     * @var array
     */
    protected $dontFlash = [
//        'current_password',
//        'password',
//        'password_confirmation',
    ];

    /**
     * Register the exception handling callbacks for the application.
     *
     * @return void
     */
    public function register()
    {
        $this->reportable(function (Throwable $e) {
            //
        });
    }

    public function render($request, Throwable $e)
    {
        TransactionManager::getInstance()->stop();

        return parent::render($request, $e);
    }

    protected function prepareException(Throwable $e)
    {
        // common
        $exceptionClasses = [
            NotFoundHttpException::class,
            ThrottleRequestsException::class,
        ];
        foreach ($exceptionClasses as $exceptionClass) {
            if ($e instanceof $exceptionClass) {
                return AppException::from($e);
            }
        }
        // specific
        if ($e instanceof MethodNotAllowedHttpException) {
            return AppException::from($e, [], [
                'trans_options' => [
                    'replace' => [
                        'method' => request()->getMethod(),
                        'allow' => $e->getHeaders()['Allow'],
                    ],
                ],
            ]);
        }
        return parent::prepareException($e);
    }

    protected function renderHttpException(HttpExceptionInterface $e)
    {
        $this->renderViewShare();

        $errorView = "errors.{$e->getStatusCode()}";
        if (ThemeFacade::viewExists($errorView)) {
            return response()->view(
                ThemeFacade::viewPath($errorView),
                [
                    'errors' => new ViewErrorBag,
                    'exception' => $e,
                ],
                ConfigHelper::getWebResponseStatus($e->getStatusCode()),
                $e->getHeaders()
            );
        }

        $this->registerErrorViewPaths();

        if (view()->exists($view = $this->getHttpExceptionView($e))) {
            return response()->view(
                $view,
                [
                    'errors' => new ViewErrorBag,
                    'exception' => $e,
                ],
                ConfigHelper::getWebResponseStatus($e->getStatusCode()),
                $e->getHeaders()
            );
        }

        return $this->convertExceptionToResponse($e);
    }

    protected function renderViewShare()
    {
        (new ViewShare())->share(request());
    }

    protected function prepareJsonResponse($request, Throwable $e)
    {
        return new JsonResponse(
            $this->convertExceptionToArray($e),
            ConfigHelper::getApiResponseStatus($this->isHttpException($e) ? $e->getStatusCode() : 500),
            array_merge($this->isHttpException($e) ? $e->getHeaders() : [], ConfigHelper::getApiResponseHeaders()),
            JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES
        );
    }

    protected function convertExceptionToArray(Throwable $e)
    {
        return ApiController::failPayload(null, $e, $this->isHttpException($e) ? $e->getStatusCode() : 500);
    }

    protected function unauthenticated($request, AuthenticationException $exception)
    {
        return $request->expectsJson()
            ? response()->json(
                ApiController::failPayload(null, $exception, 401),
                ConfigHelper::getApiResponseStatus(401),
                ConfigHelper::getApiResponseHeaders(),
                JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES
            )
            : redirect()->guest($exception->redirectTo() ?? route('login'));
    }

    protected function invalidJson($request, ValidationException $exception)
    {
        return response()->json(
            ApiController::failPayload(null, $exception, $exception->status),
            ConfigHelper::getApiResponseStatus($exception->status),
            ConfigHelper::getApiResponseHeaders(),
            JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES
        );
    }
}
