<?php

namespace Core;

use Core\Http\Request;
use Core\Http\Response;

class Controller
{
    protected Request $request;

    protected function __construct(Request $request)
    {
        $this->request = $request;
    }
    public function render(string $view, array $data = []): Response
    {
        $viewFile = VIEW_PATH . "/$view.php";

        if (!file_exists($viewFile)) {
            throw new \RuntimeException("View file '$viewFile' not found.");
        }

        // Create isolated variable scope
        $render = function ($__file__, $__data__) {
            extract($__data__);
            ob_start();
            include $__file__;
            return ob_get_clean();
        };

        $response = new Response();
        return $response->body($render($viewFile, $data));
    }

    public function render_with_layout(
        string $view,
        array $data = [],
        string $layout = 'layouts/layout'
    ): Response {
        $response = $this->render($view, $data);

        // Merge content into layout data
        $layoutData = array_merge($data, ['content' => $response->get_body()]);
        $layoutFile = VIEW_PATH . "/$layout.php";

        if (!file_exists($layoutFile)) {
            throw new \RuntimeException("Layout file '$layoutFile' not found.");
        }

        return $this->render($layout, $layoutData);
    }
}
