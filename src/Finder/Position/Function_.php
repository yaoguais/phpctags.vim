<?php

namespace PhpCTags\Finder\Position;

class Function_ extends BaseFinder implements Finder
{
    public $root;
    public $namespace;
    public $name;

    protected $logger;

    const CLASS_PATTERN = '/class\s+[A-Za-z0-9_]+(\s+extends\s+[A-Za-z0-9_\\\\]+)?(\s+implements\s+[A-Za-z0-9_, \\\\]+)?\s*\{/s';

    public function __construct()
    {
        $this->logger = \PhpCTags\Logger::getInstance();
    }

    public function validate()
    {
        if (! $this->name) {
            $this->throwException('name is invalid');
        }
    }

    public function find()
    {
        $this->validate();

        $fullName = $this->namespace ? $this->namespace.'\\'.$this->name : $this->name;
        try {
            $refFunc = new \ReflectionFunction($fullName);
        } catch (\Exception $e) {
        }
        if (isset($refFunc) && $refFunc->isInternal()) {
            $this->throwException("keyword is an internal function: $fullName");
        }

        $command = sprintf(
            'ag --nogroup --nocolor -G "\.php" "^[ \t]*function\s+%s\s*[\s(]" %s',
            $this->name,
            $this->root
        );
        $output = exec($command, $outputs, $code);

        if (0 !== $code && 1 !== $code) {
            $this->throwException("execute ag search failed:[$code] $output");
        }
        if (0 == count($outputs)) {
            $this->throwException('symbol not found');
        }

        // global function always start with "function" and without space or table.
        // line start with space " " that should be put to last.
        rsort($outputs);

        $this->logger->debug('ag found positions: '.implode("\n", $outputs));

        $positions = [];
        foreach ($outputs as $output) {
            list($file, $line, $raw) = explode(':', $output);

            try {
                $positions[] = $this->parse($file, $line, $raw);
            } catch (\Exception $e) {
                $this->logger->debug($e->getMessage().' '.$e->getTraceAsString());
            }
        }

        if (0 == count($positions)) {
            $this->throwException('no available symbol not found');
        }

        $this->logger->debug('positions found: '.json_encode($positions));

        return $positions[0];
    }

    public function parse($file, $line, $raw)
    {
        if (! is_readable($file)) {
            $this->throwException("file is not readable: $file");
        }

        $content = file_get_contents($file);
        $nsParser = new \PhpCTags\Parser\Namespace_();
        $namespaces = $nsParser->parseToken(token_get_all($content));

        if ($this->namespace) {
            $found = false;
            for ($i = 0, $l = count($namespaces); $i < $l; ++$i) {
                if ($namespaces[$i][0] == $this->namespace) {
                    $found = true;

                    if ($line < $namespaces[$i][1]) {
                        $this->throwException("function found at wrong line: $file");
                    }
                    if ($i < $l - 1 && $line >= $namespaces[$i + 1][1]) {
                        $namespace = $namespaces[$i + 1][0];
                        $this->throwException("function found in wrong namespace {$namespace}: $file");
                    }
                }
            }
            if (! $found) {
                $this->throwException("namespace {$this->namespace} not found: $file");
            }
        } elseif (count($namespaces) > 0) {
            $this->throwException("namespace should't be exists: $file");
        }

        // class definition should't be exists.

        if (preg_match(self::CLASS_PATTERN, $content)) {
            $this->throwException("class should't be found: $file");
        }

        return new \PhpCTags\Position($file, $line, stripos($raw, $this->name) + 1);
    }
}
