<?php
spl_autoload_extensions(".php");
spl_autoload_register(function($class) {
    $filePath = __DIR__ . "/" . str_replace("\\", "/", $class) . ".php";
    if (file_exists($filePath)) {
        require_once($filePath);
    }    

});
require_once 'vendor/autoload.php';
$commands = include "Commands/registry.php";

// 第2引数は実行するコマンド

$inputCommand = $argv[1];


// PHPでそれらをインスタンス化できるすべてのコマンドクラス名を通過します。
foreach ($commands as $commandClass) {
    // fwrite(STDOUT, $commandClass);
    // registry.phpにあるクラス
    $alias = $commandClass::getAlias();

    if($inputCommand === $alias){

        if(in_array('--help',$argv)){
            fwrite(STDOUT, $commandClass::getHelp());
            exit(0);
        }
        else{
            $command = new $commandClass();
            $result = $command->execute();
            exit($result);
        }
    }
}

fwrite(STDOUT,"Failed to run any commands\n");