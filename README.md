# util
Utility classes for doing various tasks.

## Helpers
These are utility wrappers of system functions, which should be used to perform system operations because they can easily be mocked in tests.

### ShellExec
Use this to run command line utilities. It is a wrapper for `shell_exec()`. It is a class so that it can be mocked in tests.
```php
$shell = new ShellExec();
$cwd = sys_get_temp_dir();
$shell->exec("echo 'test' && echo 'error test' >&2", $cwd);
```

Running the example above will print *test* to stdout, and *error test* to stderr. You can retrieve the output of both streams by calling the `getStdout()` and `getStderr()` methods.

This is very useful in preventing output from going to the screen with command line utils that dump logging information to `stderr`.

### Tempdir
Use this to get a temporary directory.

```php
$tempdir = new Tempdir();
$path = $tempdir->create();
```

## Tempfile
Use this to get a temporary file.

```php
$tempfile = new Tempfile();
$path = $tempfile->create();
```

## PDF
### Ghostscript

This class is used to downgrade / convert files from a newer version of PDF version 1.4 so that FPDi, FPDF, and other libraries can use it. It uses Ghostscript to do the conversion.

Usage:
```php
$container = new Container();
$container->add(ShellExec::class);
$container->add(VersionParser::class);
$container->add(GhostScript::class)->addArgument($container);

$ghostscript = $container->get(GhostScript::class);
$pathToPdfFile = '/path/to/file.pdf';
$outputfile = '/path/to/output.pdf';
$ghostscript->downgrade($pathToPdfFile, $outputfile);
```

### PDF\VersionParser
This class read a PDF file, and parses out the version of the PDF for that file. The method: `getVersion()` returns a string, like "1.4" or "1.7". 
Example usage:
```php
$parser = new VersionParser();
$version = $parser->getVersion($pathToPdfFile);
```

It follows the specification for PDF files for the header described in ISO 32000-1:2008 for PDF v1.7. The latest standard (v2.0) is described in ISO 32000-2:2020. 

This class was developed using the following information obtained from [PDF (Portable Document Format) Family](https://www.loc.gov/preservation/digital/formats/fdd/fdd000030.shtml#:~:text=Adobe%20has%20a%20number%20of,1.7):

    Self-identification of chronological versions of PDF: Identification of chronological versions of PDF can be given
    in two places in a PDF file. All PDF files should have a version identified in the header with the 5 characters
    %PDF– followed by a version number of the form 1.N, where N is a digit between 0 and 7 or a version number of 2.0.
    For example, PDF 1.7 would be identified as %PDF–1.7. However, beginning with PDF 1.4, a conforming PDF writer may
    use the Version entry in the document Catalog to override the version specified in the header. The location of the
    Catalog within the file is indicated in the Root entry of the file trailer/footer. This override feature was
    introduced to facilitate the incremental updating of a PDF by simply adding to the end of the file. As a result,
    it is necessary to locate the Catalog within the file to get the correct version number. Unless the PDF is
    "linearized," in which case the Catalog is up front, this will require reading the trailer and then using the
    reference there to locate the Catalog, which will typically be compressed. This has practical implications because
    format identification tools, including DROID, typically look for particular characters at the beginning of a file
    (i.e., in the header), to permit identification with minimal effort. DROID can look for characters at the end of the
    file, but is not able to follow an indirect reference or decompress file contents. When the version number is not
    the same in the header and the Catalog, there is potential for format identification errors.


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
