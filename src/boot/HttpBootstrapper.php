<?php

namespace Bermuda\App\Boot;

use Bermuda\HTTP\Responder;
use Bermuda\App\AppInterface;
use Bermuda\String\Json;
use Bermuda\String\Stringable;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\UriInterface;

final class HttpBootstrapper implements BootstrapperInterface
{
    /**
     * @inerhitDoc
     */
    public function boot(AppInterface $app): void
    {
        $app->registerCallback('respond', static function(int $code = 200, $content = null) use ($app): ResponseInterface {

            if (!is_string($content) && !$content instanceof Stringable) {
                $content = Json::encode($content);
            }

            return $app->get(Responder::class)->respond($code, $content);
        });
        
        $app->registerCallback('redirect', static function(string|UriInterface $location, bool $permanent = false) use ($app): ResponseInterface {
            return $app->get(Responder::class)->redirect($location, $permanent);
        });
    }
}
