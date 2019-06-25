<?php
require "./vendor/autoload.php";

$file_names = [
    'file1',
    'file2'
];
$parser = new \Src\MultiFileParser([
    'file_names' => $file_names,
    'max_count_constraint' => 4
]);
$parser->parse()
    ->write();
