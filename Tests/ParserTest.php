<?php

require_once "Src/Parser.php";

use PHPUnit\Framework\TestCase;
use Src\Parser;

final class ParserTest extends TestCase
{
    protected $csv_dir_name = 'Tests/data';

    public function test_init_parser_file1()
    {
        $parser = new Parser('file1', [
            'csv_src_dir_name' => $this->csv_dir_name
        ]);
        $result = $parser->parse();
        $this->assertIsArray($result);
        $this->assertCount(4, $result);
        foreach ($result as $row) {
            $this->assertCount(3, $row);
        }
    }


    public function test_convert_csv_to_array()
    {
        $parser = new Parser('file1', [
            'csv_src_dir_name' => $this->csv_dir_name
        ]);
        list($resource, $file_size) = $parser->uploadCsvFile();
        $parsed = $parser->convertCsvToArray($resource, $file_size);
        $this->assertIsArray($parsed);
        $this->assertEquals($parsed, [
            [
                1, 'name1', 100
            ],
            [
                2, 'name2', 300
            ],
            [
                2, 'name4', 200
            ],
            [
                3, 'name3', 400
            ]
        ]);
    }
}

