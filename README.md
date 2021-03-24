# IrfanTOOR\TemplateEngine

A simple and small template engine.

# Quick start

## Installation

Installation or inclusion in your project:

```sh
$ composer require irfantoor/template-engine
```

To test the template engine:
```sh
$ vendor/bin/test
```

## Creating the Template Engine:
```php
$te = new IrfanTOOR\TemplateEngine([
    'max_depth' => 3, # defaults to 3
    'base_path'  => 'your/path/to/template/files/',
]);
```

## Processing Text
```php
$text = "{$greeting} {$user}!";
$data = [
    'greeting' => 'Hello',
    'user' => 'World',
];

echo $te->processText($text, $data);
```

## Processing File

```php
# home.php
<h1>{$title}</h1>

<ul>
{@foreach ($list as $item):}
<li>{$item}</li>
{@endforeach}
</ul>
```

```php
$data = [
    'title' => 'Fruits',
    'list' => [
        'Apple',
        'Orange',
        'Blackberry',
        'Raspberry',
    ],
];

echo $te->processFile("home.php", $data);
```

## Template

### Comments

__format: {#...}__

```php
{# its a comment!}
{#anything here including the external brackets are removed from the output}
```

### Tokens
__format: {$...}__

The tokens are replaced with the values provided by the passed data array.

```tplt
{$name['first']} {$name['last']}
{$address[0]}
{$address[1]}
tel: {$tel}
email: {$email}
```

__format: {!$...}__
The tokens are replaced with tags are replaced with value, without doing any html
special character conversion. It helps in including the html tags etc. which are
displayed as html and not as content.

### Commands
__format: {@...}__

```php
{@include 'header.php'}

{@echo date('d-m-Y')}

{@if ($list):}
    # Note: you can use the curley brackets, so use the form foreach (...): endforeach instead
    {@foreach ($list as $k => $v):}
    data provided is : {$k} | {$v}
    {@endforeach}
{@endif}



# you can define the data in the template
{@ $d = date('d-m-Y')}

# and prints ...
date : {$d}

# Note: The statement in {@ ...} tags need not to be terminated with a semicolon ';'
{@ $list = [
    'black',
    'white'
]}

dump list:
# Note: The variable to dump, might as well be an object, array, bool int or a string
{$list}
```

Note: after {@ use the commands as if you were using the php code, though only
constraint is that you can not use the loops or commands using the curly brackets.