# fun-php
Functional Programming to enhance OOP PHP developer experience

[![Build Status](https://travis-ci.org/apantle/fun-php.svg?branch=master)](https://travis-ci.org/apantle/fun-php) [![Maintainability](https://api.codeclimate.com/v1/badges/5367d092ea73ae674743/maintainability)](https://codeclimate.com/github/apantle/fun-php/maintainability) [![Test Coverage](https://api.codeclimate.com/v1/badges/5367d092ea73ae674743/test_coverage)](https://codeclimate.com/github/apantle/fun-php/test_coverage)

Here you have the supported functions with a minimal example, there are more examples in the tests of the package:

### compose
applies a par of functions, first the one to the right, then
to the returned value applies the function to the left:

Example:
```php
$stringSquaredRoot = compose('strval', 'sqrt');

var_dump('4' === $stringSquaredRoot(16)); // bool(true) 
```

### pipe

Returns a function that receives any number of callables,
first one is passed an arbitrary number of arguments,
then it passes the returned value to next one in the pipe,
and so on. Next functions must be unary (only accept one argument).

Example with a simplified functional controller:
```php

$getFoldersController = pipe(
    'validateHasUser',
    'validatePermissionOfUser',
    'getRootFolders'
);

function validateHasUser($request = null)
{
    if (!array_key_exists($request, 'user')) {
        throw new \LogicException('User parameter not received');
    }
    return $request;
}

function validatePermissionOfUser($request)
{
    if (intval($request['user']) !== 1) {
        throw new \DomainException('Only root user has permissions on this route');
    }
    return $request;
}

function getRootFolders($request) {
    return [ 'folder-' . $request['user'] ];
}

// $user key absent of request:
$getFoldersController(['path' => 'whatever']); // throws LogicException

// $user key present, non root user
$getFoldersController(['user' => 20]); // throws DomainException

// $user is root
$getFoldersController(['user' => 1]); // returns [ 'folder-1' ];

```

### curryToUnary
Allow to partially apply any callable, returning an unary
function. Most useful in combination with `pipe` to write
point-free style algorithms.

Passing in the list of arguments to bound, the constant
`Apantle\FunPHP\_` the position of the argument expected
can be swapped. For example, you can build a Mappable collection
with array_map and the array to be mapped, and pass to this
collection a different mapper every time.

Examples:
```php

$simpleArrayReverse = curryToUnary('array_reverse');

var_dump([4,3,2,1] === $simpleArrayReverse([1,2,3,4]); // bool(true)

// custom position of argument to receive:
$map = curryToUnary('array_map', \Apantle\FunPHP\_, [1, 2, 3, 4]);

$result = $map(function ($num) { return $num * 10; });

var_dump([10, 20, 30, 40] === $result); // bool(true)

```

### constant

Returns a function that always return the value passed, useful for
placeholders or composition with constant values but to avoid
hardcoding values:

```php
$request = [
    'store_id' => filter_input(
        INPUT_POST,
        'store',
        VALIDATE_FILTER_INT
    ) 
];

$scope = constant($request['store_id']);
$scope() // always return the value received in $_POST['store']

```

### identity
useful for functor tests, always returns the passed value

### head
gets the first item of an array

```php
var_dump(1 === head[1,2,3,4]); // bool(true)
```

### unfold
Returns a function that takes a single value, applies an array
of transformations to it, and returns a an associative array with
the same keys as the array of specs, with the value mapped by those
functions.

Useful to take a single input and pass it to several services,
then collecting the output in a single hashmap. A perfect dual
to apantle/hashmapper.

Example:
```php

$input = 4;

var_dump(json_encode(unfold([
    'sqrt' => 'sqrt',
    'factorial' => 'gmp_fact'
])($input)));
// string(25) "{"sqrt":2,"factorial":24}"

```

If the function is unary, it passes only the input value.

In order to composer more complex algorithms easily, the mappers can receive
a second argument, that apantle/hashmapper passes to every
mapper automatically, being that the whole associative array being mapped.

Apart from the spec of transformations, it takes an optional
argument that allows you to pass values through the mappers,
it is recommended this argument to be an object to be passed by
reference.

See the test sources for [more examples](https://github.com/apantle/fun-php/blob/566bfe539bb193028c5bad4c6687a6f3a1b1e82c/tests/FunctionsTest.php#L50-L70).
