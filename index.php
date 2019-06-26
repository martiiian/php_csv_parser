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
    'max_count_constraint' => 10,
    'src_directory' => 'data',
    'count_same_id_constraint' => 2
]);

//$parser = new \Src\MultiFileParser([
//    'max_count_constraint' => 10,
//    'src_directory' => 'data',
//    'count_same_id_constraint' => 2
//]);
$parser->parse()
    ->write();
