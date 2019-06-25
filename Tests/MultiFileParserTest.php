<?php

use PHPUnit\Framework\TestCase;
use \Src\Parser;

final class MultiFileParserTest extends TestCase
{
    public function getFiles()
    {
        return [
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
    }

    /**
     * @return array
     * @throws Exception
     */
    public function getParser()
    {
        $parser = new \Src\MultiFileParser([
            'src_files' => $this->getFiles()
        ]);
        return [[$parser]];
    }

    public function getResultDirPath()
    {
        return realpath(
            dirname(__DIR__)
            . "/result/"
        );
    }

    /**
     * @dataProvider getParser
     * @param $parser
     */
    public function test_write_to_csv_file($parser)
    {
        $parser->parse();
        $file_name = $parser->write();
        $this->assertTrue(strlen($file_name) > 0);
        $file_name && unlink($this->getResultDirPath() . '/' . $file_name);
    }

    /**
     * @dataProvider getParser
     * @param $parser
     */
    public function test_get_unsorted_data(\Src\MultiFileParser $parser)
    {
        $parser->parse();
        $data = $parser->getParsedData();
        $this->assertIsArray($data);
        $this->assertTrue(count($data) > 0);
    }

    public function test_max_count_rows_constraints()
    {
        $file_names = [
            'file1',
            'file2'
        ];

        $parser = new \Src\MultiFileParser([
            'src_files' => $this->getFiles(),
            'count_same_id_constraint' => 2,
            'max_count_constraint' => 4,
        ]);

        $result_file_name = $parser->parse()
            ->write();

        $file_parser = new Parser(
            $result_file_name,
            [
                'delimiter' => ',',
                'ext' => '.csv',
                'csv_src_dir_name' => 'result'
            ]
        );
        $this->assertTrue(count($file_parser->parse()) <= 4);
        unlink($this->getResultDirPath() . '/' . $result_file_name);
    }
}

