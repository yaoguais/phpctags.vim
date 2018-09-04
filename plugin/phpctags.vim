fun! PHPJumpToDefinition(word)
    let l:word = a:word
    for l:i in ["\\$", "\\-", "\\\""]
        let l:word = substitute(l:word, l:i , "\\" . l:i, "g")
    endfor
    
    " external command use for jumping
    " returns string start with 'error' when error hanppend,
    " and returns 'file line position' when success.
    let l:phpJumpToDefinitionCommand = "PHPJumpToDefinition"
    if executable(l:phpJumpToDefinitionCommand) != 1
        echoerr l:phpJumpToDefinitionCommand . " not found in this system"
        return
    endif

    let l:args = ["--file", expand('%:p'), "--line", line("."), "--column", col("."), "--keyword", '"' . l:word . '"']
    let l:command = l:phpJumpToDefinitionCommand . ' ' . join(l:args, ' ')
    let l:result = system(l:command)
    if l:result =~? '^error'
        echo l:result
    else
        let l:result = substitute(l:result, '\n\+$', '', '')
        let l:result = split(l:result, " ")
        let l:ecommand = "normal :edit +call\\ setpos('.',[0," . l:result[1] . "," . l:result[2] . ",0]) " . l:result[0] . " \<CR>"
        execute l:ecommand
    endif
endfun
autocmd FileType php nnoremap <C-]> :call PHPJumpToDefinition(expand('<cfile>'))<CR>
