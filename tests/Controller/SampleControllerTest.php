<?php

namespace Test\Essential\Core\Controller;

use Essential\Core\Controller\Controller;

class SampleControllerTest extends Controller
{
    public function __construct(array $middleware)
    {
        foreach ($middleware as $item) {
            $this->middleware($item);
        }
    }

    public function testGet(string $id)
    {
        return $this->get($id);
    }
}