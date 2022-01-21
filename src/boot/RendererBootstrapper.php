<?php

namespace Bermuda\App\Boot;

use Bermuda\HTTP\Responder;
use Bermuda\App\AppInterface;

final class RendererBootstrapper implements BootstrapperInterface
{
    /**
     * @param AppInterface $app
     * @return void
     */
    public function boot(AppInterface $app): void
    {
        $app->registerCallback('render', static function(string $template, array $vars = []) use ($app): string {
            
            static $renderer = null;
            static $responder = null;

            if ($renderer === null) {
                if ($app->has('renderer')) {
                    $renderer = $app->renderer->render(...);
                } elseif ($app->has('Bermuda\Templater\RendererInterface')){
                    $renderer = $app->get('Bermuda\Templater\RendererInterface')->render(...);
                } else {
                    $renderer = static function(string $template, array $vars = []): string {
                        if ($vars !== []) {
                            var_export($vars);
                        }
                        ob_start();
                        require $template;
                        return ob_get_clean();
                    };
                }
            }

            if ($responder === null) {
                if ($app->has('responder')) {
                    $responder = $app->responder;
                } elseif ($app->has(Responder::class)) {
                    $responder = $app->get(Responder::class);
                } else {
                    $responder = Responder::fromContainer($app);
                }
            }

            return $responder->respond(200, $renderer($template, $vars));
        });
    }
}
