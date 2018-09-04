<?php

// Build Phar:
//
// $ php buildPHAR.php
// $ chmod a+x build/phpctags.phar
// $ cp build/phpctags.phar /usr/bin/PHPJumpToDefinition
//
// Then config your vimrc file.
// You can see the README.md for details.

$phar = new Phar('build/phpctags.phar', 0, 'phpctags.phar');

if (version_compare(PHP_VERSION, '5.4.0') < 0) {
    class RecursiveCallbackFilterIterator extends RecursiveFilterIterator
    {
        protected $callback;

        public function __construct(RecursiveIterator $iterator, $callback)
        {
            $this->callback = $callback;
            parent::__construct($iterator);
        }

        public function accept()
        {
            $callback = $this->callback;

            return $callback(parent::current(), parent::key(), parent::getInnerIterator());
        }

        public function getChildren()
        {
            return new self($this->getInnerIterator()->getChildren(), $this->callback);
        }
    }
}

$phar->buildFromIterator(
    new RecursiveIteratorIterator(
        new RecursiveCallbackFilterIterator(
            new RecursiveDirectoryIterator(
                getcwd(),
                FilesystemIterator::SKIP_DOTS
            ),
            function ($current) {
                $includes = [
                    'bin',
                    'bin/PHPJumpToDefinition',
                    'src',
                    'src/*',
                    'vendor',
                    'vendor/*',
                ];

                foreach ($includes as $include) {
                    if (fnmatch(getcwd().'/'.$include, $current->getPathName())) {
                        return true;
                    }
                }

                return false;
            }
        )
    ),
    getcwd()
);

$phar->setStub(
    "#!/usr/bin/env php\n".$phar->createDefaultStub('bin/PHPJumpToDefinition')
);
