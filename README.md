# phpctags

An enhanced definition and declaration jumper written 
in pure PHP for vim-php. When we work on a huge project, 
which would has hundreds of the same tags, and it is hard 
to choose the correct tag.

And I wrote the jumper that named "phpctags", but which 
doesn't generate tags file. It replace the shortcut key 
"<C-]>" with executing an external php script, which returns 
the jump position for the definition or declaration of the 
keyword that you hit.

# Usage

```
$ bin/PHPJumpToDefinition --help

  -f, --file=FILE              file that needs to be parsed
  -l, --line=LINE              line number where the keyword appear
  -c, --column=COLUMN          column where the keyword appear
  -k, --keyword=KEYWORD        keyword that needs to be analysed
  -r, --root=ROOT              project root for finding functions and others
  -a, --autoload=AUTOLOAD      user project autoload files, default 'vendor/autoload.php'
  -h, --help                   print the help info and exit
  -v, --version                print the version and exit

returns '$jump_file $jump_line $jump_position' when success, 
and returns a string start with 'Error' then following detail 
information when failed.
```

# Install

Install for popular vim package managers:

* [Vim 8 packages](http://vimhelp.appspot.com/repeat.txt.html#packages)
  * `git clone https://github.com/yaoguais/phpctags.vim.git ~/.vim/pack/plugins/start/phpctags.vim`
* [Pathogen](https://github.com/tpope/vim-pathogen)
  * `git clone https://github.com/yaoguais/phpctags.vim.git ~/.vim/bundle/phpctags.vim`
* [vim-plug](https://github.com/junegunn/vim-plug)
  * `Plug 'yaoguais/phpctags.vim', { 'do': ':GoUpdateBinaries' }`
* [Vundle](https://github.com/VundleVim/Vundle.vim)
  * `Plugin 'yaoguais/phpctags.vim'`


Install the\_silver\_searcher for quick searching:

    $ brew install the_silver_searcher
      or
    $ apt-get install silversearcher-ag

Finally add the bin dir into the system $PATH variable:

    $ git clone https://github.com/yaoguais/phpctags.vim.git
    $ cd phpctags.vim && composer install --no-dev
    $ export PATH=$(pwd)/bin:$PATH


# Road Map

[Road Map](./ROADMAP.md)

# Enjoy it
 
