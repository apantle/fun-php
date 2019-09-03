<?php
namespace Apantle\FunPHP;
const _ = 'curryUnaryArg';

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

function pipe(...$functions): callable
{
    return function() use($functions) {
        $fun_args = func_get_args();
        $entryFunction = array_shift($functions);
        return array_reduce(
          $functions,
          function($prev, $current_fun) {
              return call_user_func($current_fun, $prev);
          },
          call_user_func_array($entryFunction, $fun_args)
        );
    };
}

function curryToUnary($callableFirst, ...$fun_args): callable
{
    if(in_array(_, $fun_args)) {

        return function ($uniqueArg) use ($callableFirst, $fun_args) {
            $callArgs = array_map(function ($arg) use ($uniqueArg) {
                return ($arg === _) ? $uniqueArg : $arg;
            }, $fun_args);

            return call_user_func_array($callableFirst, $callArgs);
        };
    }

    return function ($uniqueArgOnRight) use ($callableFirst, $fun_args) {
        array_push($fun_args, $uniqueArgOnRight);
        return call_user_func_array( $callableFirst, $fun_args );
    };
}

function unfold($specs, $tameme = null): callable
{
    return function ($input, $optional = null) use ($specs, $tameme) {
        $mecapal = [];

        foreach ($specs as $target => $mapper) {
            $mecapal[$target] = isUnaryFn($mapper)
                ? call_user_func($mapper, $input)
                : call_user_func($mapper, $input, $optional, $tameme)
            ;
        }

        return $mecapal;
    };
}

function isUnaryFn(callable $callable)
{
    $reflector = new \ReflectionFunction($callable);
    return boolval($reflector->getNumberOfParameters() === 1);
}
