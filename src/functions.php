<?php
namespace Apantle\FunPHP;

function constant($value): callable
{
    return function () use ($value) {
        return $value;
    };
}

function identity($item)
{
    return $item;
}

function head(array $items)
{
    return $items[0];
}

function compose(callable $f, callable $g): callable
{
    return function () use ($f, $g) {
        $fun_args = func_get_args();
        return $f(call_user_func_array($g, $fun_args));
    };
}


