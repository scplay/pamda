# Pamda
A PHP function programming lib same as [Ramda.js](https://ramdajs.com/) (php version >= 7.1)

# install

```
composer require zeonwang/pamda
```

# usage

```php
use Pamda\Pamda;
        
$blankTo = Pamda::curryN(3, 'preg_replace')('/\s+/');
    
$blankToSlash = $blankTo('/');
echo $blankToSlash('a b c'); // => "a/b/c"

$blankToDash = $blankTo('-');
echo $blankToDash('a b c'); // => "a-b-c"

```
