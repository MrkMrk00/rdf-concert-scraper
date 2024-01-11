<?php

namespace App\Http\Controllers;

use Illuminate\Contracts\View\Factory as ViewFactory;
use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

class Controller extends BaseController
{
    use AuthorizesRequests, ValidatesRequests;

    private ViewFactory $viewFactory;

    protected function page(string $pageName, array $data = [], array $mergeData = []): View
    {
        return $this->view()->make('pages.'.$pageName, $data, $mergeData);
    }

    protected function partial(string $partialName, array $data = [], array $mergeData = []): View
    {
        return $this->view()->make('components.partials.'.$partialName, $data, $mergeData);
    }

    protected function view(): ViewFactory
    {
        return $this->viewFactory ??= app(ViewFactory::class);
    }
}
