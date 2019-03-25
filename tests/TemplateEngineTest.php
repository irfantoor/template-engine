<?php

use IrfanTOOR\TemplateEngine;
use IrfanTOOR\Test;

class TemplateEngineTest extends Test
{
    protected $te = null;

    function getTemplateEngine()
    {
        if (!$this->te) {
            $this->te = new TemplateEngine([
                'base_path' => __DIR__ .'/',
            ]);
        }

        return $this->te;
    }

    function testInstance()
    {
        $te = $this->getTemplateEngine();
        $this->assertInstanceOf(IrfanTOOR\TemplateEngine::class, $te);
    }

    function testProcessText()
    {
        $te = $this->getTemplateEngine();

        $text = 'Hello {$name}, I\'m {$host}';
        $data = [
            'name' => 'World!',
            'host' => 'your DJ'
        ];

        $rendered = $te->processText($text, $data);

        $this->assertEquals("Hello World!, I'm your DJ", $rendered);
    }

    function testProcessFile()
    {
        $te = $this->getTemplateEngine();

        $text = 'Hello {$name}, I\'m {$host}';
        $file = 'test.tplt';

        file_put_contents(__DIR__ . '/' . $file, $text);

        $data = [
            'name' => 'World!',
            'host' => 'your DJ'
        ];

        $rendered = $te->processFile($file, $data);
        $this->assertEquals("Hello World!, I'm your DJ", $rendered);
        unlink($file);
    }

    # {# ...}
    function testCommentToken()
    {
        $te = $this->getTemplateEngine();
        $data = [
            'token' => 'something',
        ];

        $tests = [
            '' => '{# its a comment}',
            't11' => 't{# first comment"}1{# second comment}1',
            't21-something' => 't21-{$token}',
            't22' => 't2{#$something}2',
        ];

        foreach ($tests as $k => $v) {
            $this->assertEquals($k, $te->processText($v, $data));
        }        
    }

    # {@foreach ($name as )}
    function testToken()
    {
        $te = $this->getTemplateEngine();

        $text = '{$greeting}';
        $data = ['greeting' => 'Hello World!'];
        $expected = 'Hello World!';
        $this->assertEquals($expected, $te->processText($text, $data));
    }

    # {@ ...}
    function testCommandToken()
    {
        $te = $this->getTemplateEngine();

        $text = '{@echo date("Y-m-d")}';
        $expected = date('Y-m-d');
        $rendered = $te->processText($text);

        $this->assertEquals($expected, $rendered);
    }

    function testTextDataOverride()
    {
        $te = $this->getTemplateEngine();

        $text = '{$greeting}';
        $data = ['greeting' => 'Hello World!'];
        $this->assertEquals('Hello World!', $te->processText($text, $data));

        $text = '{$text}{$check}';
        $data['text'] = '[TEXT]';
        $data['check'] = '[CHECK]';
        $this->assertEquals('[TEXT][CHECK]', $te->processText($text, $data));

        $text = '{$text}{$data}';
        $data['data'] = '[DATA]';
        $this->assertEquals('[TEXT][DATA]', $te->processText($text, $data));

        $text = '{$text}';
        $data = [
            'greeting' => 'Hello World!',
            'text' => '{$greeting}',
        ];
        $this->assertEquals('Hello World!', $te->processText($text, $data));

        $text = '{$text}{$data}';
        $data = [
            'text' => '{$data}',
            'data' => '{$text}',
            'depth' => 1000,
            'max_depth' => 1000,
        ];
        $this->assertEquals('{$text}{$data}', $te->processText($text, $data));
    }
}
