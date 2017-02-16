# Translatte

This package aims to provide an interpretation layer to data.

## Usage

There is no limit as per when or for what to use it. The only requisite
is that your classes implements the `carlosV2\Translatte\TranslationInterface`.

For example, you can perform language translations:

```php
class HelloWorldTranslation implements TranslationInterface
{
    public function matches($message)
    {
        return $message === 'Hello World';
    }
    
    public function translate($message)
    {
        return 'Hola Mundo';
    }
}
```

Or to translate file paths into their contents:

```php
class FileTranslation implements TranslationInterface
{
    public function matches($message)
    {
        return file_exists($message);
    }
    
    public function translate($message)
    {
        return file_get_contents($message);
    }
}
```

If you want to perform those translations, you will need to instantiate
a translator and feed him with the translations:

```php
$translator = new Translator();

$translator->addTranslation(new HelloWorldTranslation());
$translator->addTranslation(new FileTranslation());

echo $translator->translate('Hello World'); // Hola Mundo
echo $translator->translate('/etc/passwd'); // The contents of your file
```

### SQL to RAW

Bundled within the library there is a piece of code to help translating SQLs.
Be aware it is very simple and it does not understand SQL but rather it works
by pattern matching them.

In order to implement those translations, you need to extend `carlosV2\Translatte\SqlToRaw\SqlToRawTranslation`.
Despite this class only enforces the implementation of `getSqlPattern` method, you
are also required to implement the `process` method.

For example, imagine we have some data indexed by a certain value and, for
whatever reason, we have an SQL looking for this data. Using this package,
we can grab the value requested in the SQL and use it to select the correct
data. Take a look:

```php
class LoadRowSql extends SqlToRawTranslation
{
    private $data;

    public function __construct(array $data)
    {
        $this->data = $data;
    }

    protected function getSqlPattern()
    {
        return 'SELECT {*} FROM my_table WHERE field = {field}';
    }
    
    protected function process($field)
    {
        return $this->data[$field];
    }
}
```

The match will be performed according to the following rules:

- Any character will match itself
- Placeholders will match an unlimited number or characters

The placeholders are composed by `{` + some name + `}` and there are
two different placeholders:

- Discard placeholders: It contains an asterisk and it tells the library
  that we don't care whatever is in there.
- Named placeholders: It contain a valid argument name and it tells the
  library to extract whatever is in there and provide it as value for the
  same argument name in the `process` method.


## Install

Open a command console, enter your project directory and execute the
following command to download the latest stable version of this project:

```bash
$ composer require carlosv2/translatte
```

This command requires you to have Composer installed globally, as explained
in the [installation chapter](https://getcomposer.org/doc/00-intro.md)
of the Composer documentation.
