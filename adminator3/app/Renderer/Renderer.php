<?php

namespace App\Renderer;

use Psr\Http\Message\ResponseInterface;

final class Renderer
{
    public function __construct(
        private \Smarty $smarty,
    ) {
    }

    public function template(
        ResponseInterface $response,
        string $template,
        array $assignData = [],
    ): ResponseInterface {
        foreach ($assignData as $name => $value) {
            $this->smarty->assign($name, $value);
        }

        $content = $this->smarty->fetch($template);

        $response->getBody()->write($content);

        return $response;
    }
}
