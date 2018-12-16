# pamda
A PHP function programming lib same as Ramda.js (php version >= 7.1)

# install

```
composer require zeonwang/pamda
```

# usage

```php
use Pamda\Pamda;
        
$blankTo = Pamda::curryN(3, 'preg_replace')('/\s+/');
    
$blankToSlash = $whiteSpaceTo('/');
echo $blankToToA('a b c'); // => "a/b/c"

$blankToDash = $blankToTo('-');
echo $blankToDash('a b c'); // => "a-b-c"

```
