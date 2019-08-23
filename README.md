# fun-php
Functional Programming to enhance OOP PHP developer experience

Currently supports functions, for better examples check the
tests of the package:

### compose
applies a par of functions, first the one to the right, then
to the returned value, the function to the left:

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

### curryToUnary

Allow to partially apply any callable, returning an unary
function. Most useful in combination with `pipe` to write
point-free style algorithms.

Passing in the list of arguments to bound, the constant
`Apantle\FunPHP\_` the position of the argument expected
can be swapped. For example, you can build a Mappable collection
with array_map and the array to be mapped, and pass to this
collection a different mapper every time.

### constant

Returns function that always return same value

### identity
useful for functor tests

### head
gets the first item of an array
