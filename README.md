# IrfanTOOR\TemplateEngine

A simple and small template engine.

# Quick start

## Installation

Incstallation or inclusion in your project:
```sh
$ composer require irfantoor/template-engine
```

to test:
in the root folder:
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
    'tgreeting' => 'Hello',
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

```tplt
{# its a comment}
{#anything here including the external brackets are removed from the output}
```

### Tokens
__format: {$...}__

the tokens are replaced with the data provided by through the passed data array.

```tplt
{$name['first']} {$name['last']}
{$address[0]}
{$address[1]}
tel: {$tel}
email: {$email}
```

### Commands
__format: {@...}__

```tplt
{@include 'header.php'}

{@echo date('d-m-Y')}

{@if ($list):}
    {@foreach ($list as $k => $v):}
    data provided is : {$k} | {$v}
    {@endforeach}
{@endif}

define: {@$d=date('d-m-Y')}
prints: {$d}

{@$list = [
    'black',
    'white'
]}

dump list: 
{$list}

```

 Note: after {@ use the commands as if you were using the php code, though only constraint is that
 you can not use the loops or commands using the curly brackets.
