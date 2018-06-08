<?php namespace WpmPack\Test\src;

class Test
{
    public $testKey = 'wpmTestPackFiles';
    public $testDir = '';
    public $results = [];


    public function __construct()
    {
        $this->testDir = wpmContainer()->getStore('basePath') . '/../tests';
    }


    public function all()
    {
        $files = array_diff(scandir($this->testDir), array('..', '.'));
        $files = array_filter($files, function($file) {
            $fileParts = explode('.', $file);
            return end($fileParts) == 'php';
        });
        $files = array_map(function($file) {
            return substr($file, 0, -4);
        }, $files);
        wpm('store.var')->set($this->testKey, $files);

        return $this;
    }


    public function add($class)
    {
        $classes = wpm('store.var')->get($this->testKey);
        if(!is_array($classes)) $classes = [];

        if(!is_array($class)) $class = [$class];
        $classes = array_merge($class, $classes);

        wpm('store.var')->set($this->testKey, $classes);

        return $this;
    }


    public function run()
    {
        wpm('wp.action')->add('template_redirect', [&$this, 'runAction']);
    }

    public function runAction()
    {
        include(__DIR__ . '/resources/header.php');

        $classes = wpm('store.var')->get($this->testKey);

        foreach($classes as $class) {
            $this->test($class);
        }

        echo $this->processResults();

        include(__DIR__ . '/resources/footer.php');
        die();
    }


    protected function test($class)
    {
        $file = $this->testDir . '/' . $class . '.php';
        include_once($file);

        $testClass = new $class;
        $methods = array_filter(get_class_methods($testClass), function($method) {
            return substr( $method, 0, 5 ) === "test_";
        });

        foreach($methods as $method) {
            $testClass->$method();
        }

        $results = $testClass->getResults();
        if($results) {
            $this->results = array_merge($results, $this->results);
        }
    }


    public function results()
    {
        return $this->results;
    }


    public function processResults()
    {
        $counter = 1;
        $ticks = [];
        $issues = [];

        foreach($this->results as $key => $result) {
            if(!$result['pass']) {
                $tick = "<a href='#issue-$counter' style='color: red;'>X</a>";
                $issues[$counter] = $result['data'];
                $counter++;
            } else {
                $tick = '.';
            }
            $ticks[$key] = $tick;
        }

        return $this->printResults($ticks, $issues);
    }


    public function printResults($ticks, $issues) {
        $total = count($ticks);
        $fail = count($issues);
        $pass = $total - $fail;

        $text = '<div style="font-family: monospace">';

        $text.= '<p>' . implode(' ', $ticks) . '</p>';

        $color = $fail ? 'red' : 'green';
        $text.= "<p style='color: $color'>$total Test(s), $pass Passed, $fail Failed</p>";

        foreach($issues as $key => $issue) {
            $text.= "<h4 id='issue-$key'>Issue #$key</h4>";
            $text.= "<pre style='padding: 20px; background: #eee; color: #111; border: 1px solid #cecece;'>" . htmlentities(print_r($issues[$key], true)) . "</pre>";
        }

        $text.= '</div>';
        return $text;
    }
}