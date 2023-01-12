# util
Utility classes for doing various tasks.

## PeopleTime

### Summary
This class accepts two unix timestamps, and returns the human-readable, relative time for the interval between the two.

### Usage:
```php
$endTime = 1675209600;
$startTime = 1676470379;
$relativeTime = PeopleTime::calculate($endTime, $startTime);
echo $relativeTime; // "14 days"
```

$startTime is optional. If you leave do not specify it, it will use "now".
```php
$endTime = 2018748556;
$relativeTime = PeopleTime::calculate($endTime);
echo $relativeTime; // "11 years"
```

## IniUploadManager

### Summary
This class reads the php.ini file, and returns information about allowed upload sizes.

### Dependencies

Requires these classes, which are included in this package.

- hphio\util\ByteValueInterpreter\HumanReadable
- hphio\util\ByteValueInterpreter\RawInteger
- hphio\util\PhpIni (a wrapper for `ini_get()`)


## Usage:
Assuming the following ini values:
```ini
post_max_size=8k
upload_max_filesize=7M
```

This code can used as follows:
```
$manager = $container->get(IniUploadManager::class);  
echo $manager->getMaxUpload(); //7340032  
echo $manager->getMaxPost(); //8192  
echo $manager->getUploadLimit(); //8192  
```

## ByteValueInterpreter

### Summary

A series of classes for converting ini values (like '8M') into their actual integer values.

### Usage

Add dependencies to the container.
```
$container = new Container();
$container->add(RawInteger::class);
$container->add(HumanReadable::class);
```

It can interpret human readable shortcut values:
```
$value = '8M';
$obj = ByteValueFactory::getByteInterpreter($container, $value);
$obj->getBytes($value); //8388608
```

And will interpret raw integer values as well:

```
$value = '8388608';
$obj = ByteValueFactory::getByteInterpreter($container, $value);
$obj->getBytes($value); //8388608
```
