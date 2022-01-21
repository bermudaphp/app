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
        $app->registerCallback('render', static function(string $template, array $vars = []) use ($app) {
            if ($app->has('renderer')) {
                $content = $app->renderer->render($template, $vars);
            } elseif ($app->has('Bermuda\Templater\RendererInterface')){
                $content = $app->get('Bermuda\Templater\RendererInterface')->render($template, $vars);
            } else {
                if ($vars !== []) {
                    var_export($vars);
                }
                ob_start();
                require $template;
                $content = ob_get_clean();
            }

            if ($app->has('responder')) {
                return $app->responder->respond(200, $content);
            } elseif ($app->has(Responder::class)) {
                return $app->get(Responder::class)->respond(200, $content);
            } else {
                return Responder::fromContainer($app)->respond(200, $content);
            }
        });
    }
}
