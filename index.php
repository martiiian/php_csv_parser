<?php
require "Src/MultiFileParser.php";

$files = [
    [
        'name' => 'file1',
        'ext' => '.csv',
        'delimiter' => ','
    ],
    [
        'name' => 'file2',
        'ext' => '.csv',
        'delimiter' => ','
    ]
];
$parser = new \Src\MultiFileParser([
    'src_files' => $files,
    'max_count_constraint' => 4
]);
$parser->parse()
    ->write();
