<?php
namespace Apantle\FunPHP;
const _ = 'curryUnaryArg';

/**
 * Creates a function that always returns the $value given
 * @param $value
 * @return callable
 */
function constant($value): callable
{
    return function () use ($value) {
        return $value;
    };
}

/**
 * Retuns the same passed element
 * @param $item
 * @return mixed
 */
function identity($item)
{
    return $item;
}

/**
 * Returns the first item in the array
 * @param array $items
 * @return mixed
 */
function head(array $items)
{
    return $items[0];
}

/**
 * Returns a function where $g result is passed to $f function
 * @param callable $f
 * @param callable $g
 * @return callable
 */
function compose($f, $g)
{
    return function () use ($f, $g) {
        $fun_args = func_get_args();
        return $f(call_user_func_array($g, $fun_args));
    };
}

/**
 * La primera función puede admitir varios argumentos, las restantes son unary
 * @param callable[] ...$functions array de funciones
 * @return callable
 */
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

/**
 * @param callable $callableFirst función de soporte a ser llamada
 * @param mixed ...$fun_args argumentos adicionales
 * @return callable
 */
function unary($callableFirst, ...$fun_args): callable
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

/**
 * @deprecated change to unary simply
 */
function curryToUnary($callableFirst, ...$fun_args): callable
{
    $unaryArgs = array_reduce($fun_args, function ($acc, $arg) {
        array_push($acc, $arg);
        return $acc;
    }, [$callableFirst]);
    return call_user_func_array(__NAMESPACE__ . '\unary', $unaryArgs);
}

/**
 * @param array $specs array de claves y funciones que construirán el objetivo
 * @param object|null $tameme
 * @return callable
 */
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

/**
 * @param callable $callable
 * @return bool
 * @throws \ReflectionException
 * @todo implementar memoization para solo indagar una vez si el callable es unary
 */
function isUnaryFn($callable)
{
    if (!is_callable($callable)) {
        return false;
    }
    $reflector = new \ReflectionFunction($callable);
    return boolval($reflector->getNumberOfParameters() === 1);
}