<?php

namespace PhpCTags\Finder\Position;

class Const_ extends BaseFinder implements Finder
{
    public $root;
    public $namespace;
    public $name;

    protected $logger;

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

        $positions = [];
        try {
            $positions = $this->findFromFunctionDefine();
        } catch (\Exception $e) {
            $this->logger->debug('positions found from function define: '.$e->getMessage());
        }

        try {
            if (0 == count($positions)) {
                $positions = $this->findFromKeywordConst();
            }
        } catch (\Exception $e) {
            $this->logger->debug('positions found from function define: '.$e->getMessage());
        }

        if (0 == count($positions)) {
            $this->throwException('no available symbol not found');
        }

        $this->logger->debug('positions found: '.json_encode($positions));

        return $positions[0];
    }

    public function findFromFunctionDefine()
    {
        $command = sprintf(
            'ag --nogroup --nocolor -G "\.php" "define\s*\(\s*(\'|\")[A-Za-z0-9_\\\\\\\\]*%s\s*(\'|\")" %s',
            $this->name,
            $this->root
        );

        $output = exec($command, $outputs, $code);

        if (0 !== $code && 1 !== $code) {
            $this->throwException("execute ag search failed:[$code] $output");
        }
        if (0 == count($outputs)) {
            $this->throwException('symbol not found, by command '.$command);
        }

        $this->logger->debug('ag found positions: '.implode("\n", $outputs));

        $namePattern = sprintf('/define\s*\(\s*(\'|")%s\s*(\'|")/', $this->name);
        $keyword = $this->namespace ? $this->namespace.'\\'.$this->name : $this->name;
        $keyword = preg_replace('/(\\\\+)/', '\\\\\\\\{1,2}', $keyword);
        $nsPattern = sprintf('/define\s*\(\s*(\'|")%s\s*(\'|")/', $keyword);

        $nsPs = $ps = [];
        foreach ($outputs as $output) {
            list($file, $line, $raw) = explode(':', $output);

            if ($this->namespace && preg_match($nsPattern, $raw, $m)) {
                $column = strpos($raw, $this->name, strpos($raw, $m[0])) + 1;
                $position = new \PhpCTags\Position($file, $line, $column);
                $nsPs[] = $position;
                continue;
            }
            if (preg_match($namePattern, $raw, $m)) {
                $column = strpos($raw, $this->name, strpos($raw, $m[0])) + 1;
                $position = new \PhpCTags\Position($file, $line, $column);
                $ps[] = $position;
            }
        }

        if (count($ps) > 0) {
            // sort the positions, and let position that has shorter file length first.
            usort($ps, function ($a, $b) {
                return strlen($a->file) > strlen($b->file);
            });
        }

        return array_merge($nsPs, $ps);
    }

    public function findFromKeywordConst()
    {
        $command = sprintf(
            'ag --nogroup --nocolor -G "\.php" "const\s+%s\s+=" %s',
            $this->name,
            $this->root
        );

        $output = exec($command, $outputs, $code);

        if (0 !== $code && 1 !== $code) {
            $this->throwException("execute ag search failed:[$code] $output");
        }
        if (0 == count($outputs)) {
            $this->throwException('symbol not found, by command '.$command);
        }

        $this->logger->debug('ag found positions: '.implode("\n", $outputs));

        $positions = [];
        foreach ($outputs as $output) {
            list($file, $line, $raw) = explode(':', $output);

            try {
                $positions[] = $this->findPosition($file, $line, $raw);
            } catch (\Exception $e) {
                $this->logger->debug('find position exception '.$e->getMessage());
            }
        }

        return $positions;
    }

    public function findPosition($file, $line, $raw)
    {
        $content = file_get_contents($file);
        $tokens = \PhpCTags\Pool\Token::getInstance()->fromContent($content);

        $tokenParser = new \PhpCTags\Parser\Token();
        $ranges = $tokenParser->parseRange($tokens, [T_CLASS]);
        foreach ($ranges as $range) {
            if ($range[3] <= $line && $line <= $range[4]) {
                $this->throwException('const defined in class');
            }
        }

        $nsFinder = new \PhpCTags\Finder\Namespace_();
        $namespace = $nsFinder->find($content, $line);

        if ($namespace && $this->namespace != $namespace[0]) {
            $this->throwException("const namespace {$this->namespace} mismatch {$namespace[0]}");
        }

        return new \PhpCTags\Position($file, $line, strpos($raw, $this->name) + 1);
    }
}
