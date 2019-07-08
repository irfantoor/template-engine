<?php

namespace IrfanTOOR;

use Exception;

class TemplateEngine
{
    const NAME        = "Irfan's Template Engine";
    const DESCRIPTION = "A simple and small template engine";
    const VERSION     = "0.2";

    protected $text;
    protected $data;
    
    protected $base_path;
    protected $depth;
    protected $max_depth;

    function __construct($init = [])
    {
        $defaults = [
            'max_depth' => 3,
            'base_path' => '',
        ];

        foreach ($defaults as $k => $v) {
            if (!array_key_exists($k, $init)) {
                $init[$k] = $v;
            }
        }

        $this->base_path = $init['base_path'];
        $this->max_depth = $init['max_depth'];
    }

    private function _process($text, $data = [], $depth)
    {
        $this->depth = $depth;

        $exp = [
            # comments {# ...}
            '/\{\#(.*)\}/Us'  => '',

            # php statement {@ ...}        
            '/\{\@(.*)\}/Us'  => '<' .'?php ' . "$1" . '; ?' . '>',

            # php tag {$...}
            '/\{\$(.*)\}/Us' => '<' .'?php print_r($' . "$1" . '); ?' . '>',
        ];

        foreach ($exp as $p => $r) {
            $text = preg_replace($p, $r, $text);
        }

        $this->text = $text;
        $this->data = $data;

        extract($data);
        ob_start();
        eval('?>' . $this->text);
        $text = ob_get_clean();

        if ($this->depth && preg_match('/\{[\#|\@|\$]+(.*)\}/Us', $text)) {
            return $this->_process($text, $this->data, $this->depth - 1);
        }
        
        return $text;
    }

    function processText($text, $data = [])
    {
        return $this->_process($text, $data, $this->max_depth);
    }

    function processFile($file, $data = [])
    {
        chdir($this->base_path);
        if (!is_file($file)) {
            throw new Exception("file: $file, not found", 1);
        }

        return $this->processText(file_get_contents($file), $data);
    }
}
