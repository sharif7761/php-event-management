<?php

namespace middleware;
abstract class BaseMiddleware
{
    abstract public function handle($next);
}