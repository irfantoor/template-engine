<?php

namespace IrfanTOOR;

use Exception;
/**
 * Template Engine parsing a template, which might include the following tags
 * {$tag} -- prints the value of tag in the template e.g.
 *           <h1>{$title}</h1>
 *           <script>{$script}</script>
 * {!$tag} -- prints the value of tag in the template with speicial char
 *            conversion, specially for printing query or posted data e.g.
 *            <pre>{!$_GET['q']}</pre>
 * {@...} -- use to execute a php commmand e.g. {@echo date('d-m-Y')}
 *           {@foreach ($cats as $cat):}
 *           <li>{$cat}</li>
 *           {@endforeach}
 * {#...} -- define a comment e.g. {# this is a comment}
 * {@extends("main.php")} -- extends from the template main.php and replace the
 *           tag {$section} in the main.php by the contents of current template
 */
class TemplateEngine
{
    const NAME        = "Irfan's Template Engine";
    const DESCRIPTION = "A simple and small template engine";
    const VERSION     = "0.3";

    /** @var string -- root dir/path of the views */
    protected $root;

    /** @var int -- max depth for the iteration */
    protected $max_depth;

    # regex ...
    protected static $exp = [
        # template extends another template: e.g. {@extends("main.php")}
        '/\{\@extends\("(.*)"\)\}/Us' => '',

        # comments: e.g {# this is a comment}
        '/\{\#(.*)\}/Us'  => '',

        # any php statement: e.g
        # {@echo date("y m d")}
        # {@foreach ():} ... {@endforeach}
        '/\{\@(.*)\}/Us'  => '<' .'?php ' . "$1" . '; ?' . '>',

        # php tag : e.g {$title}  <h1>{$title}</h1> ...
        '/\{\$(.*)\}/Us' => '<' .'?php print_r($' . "$1  ?? ''" . '); ?' . '>',

        # php tag : e.g {!$url}  <div>{!$url}</div> ...
        # htmlspecialchars converion - for insecure data
        '/\{\!\$(.*)\}/Us' => '<' .'?php print_r(htmlspecialchars($' . "$1  ?? ''" . ')); ?' . '>',
    ];

    /**
     * TE Constructor
     *
     * @param array $options Init options:
     *                            base_path: provide the base path of views
     *                            max_depth: max depth of iteration
     */
    public function __construct(array $options = [])
    {
        $this->root      = $options['base_path'] ?? '';
        $this->max_depth = $options['max_depth'] ?? 3;
    }

    /**
     * processes the text
     * The tags {$tag}, {# comment ...}, {@phpcommand}, {@extends("main.php")}
     * are processed
     * e.g of {@phpcommand} are: {@include "header.php"}, {@print_r($array)},
     * {@foreach($item as $k => $v):} {$k} {@endforeach} ...
     * {@extends("main.php")} extends from main.php and replaces {$section} in
     * main.php with the contents of parsed result of the current template.
     *
     * @param string $contents Text to be parsed
     * @param array  $data     Associative array of key=>value, to be used
     * @return string
     */
    public function processText(string $contents, array $data = [], ?int $depth = null): string
    {
        $depth = $depth ?? $this->max_depth;

        # check if the contents contains {@extends(".*")}
        preg_match('/\{\@extends\("(.*)"\)\}/Us', $contents, $m);

        foreach (self::$exp as $p => $r) {
            $contents = preg_replace($p, $r, $contents);
        }

        $fp = function() use ($contents, $data) {
            extract($data);
            eval('?' . '>' . $contents);
        };

        try {
            ob_start();
            $fp();
            $contents = ob_get_clean();

            if ($m) {
                $data['section'] = $contents;
                $contents = $this->processFile($m[1], $data);
            }

            $depth--;

            if ($depth && preg_match('/\{[\#|\@|\$]+(.*)\}/Us', $contents)) {
                return $this->processText($contents, $data, $depth);
            }
        } catch(Throwable $e) {
            ob_clean();
            throw $e;
        }

        return $contents;
    }

    /**
     * Processes a template file
     *
     * @param string $tplt Template file
     * @param array  $data Data containing associative data for the variable
     * @return string
     */
    public function processFile(string $tplt, array $data = []): string
    {
        chdir($this->root);

        if (!file_exists($tplt))
            throw new Exception("File: " . str_replace(ROOT, "", $this->root) . $tplt . ", not found");

        return $this->processText(file_get_contents($tplt), $data);
    }
}
