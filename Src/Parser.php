<?php

namespace Src;

class Parser implements ParserInterface
{
    private $filename = '';
    private $path_to_csv_dir = 'data';
    private $ext = '.csv';
    private $delimiter = ',';

    /**
     * Parser constructor.
     *
     * @param string $filename
     * @param string $delimiter
     * @param string $ext
     * @param string $path_to_csv_dir
     */
    public function __construct(
        string $filename,
        string $delimiter = ',',
        string $ext = '.csv',
        string $path_to_csv_dir = 'data'
    ) {
        $this->filename = $filename;
        $this->delimiter = $delimiter;
        $this->ext = $ext;
        $this->path_to_csv_dir = $path_to_csv_dir;
    }

    /**
     * Запускает процесс парсинга
     *
     * @return array
     */
    function parse(): array
    {
        list($resource, $file_size) = $this->uploadCsvFile();
        return $this->convertCsvToArray($resource, $file_size);
    }


    /**
     * Конвертирует csv в массив
     *
     * @param $resource
     * @param int $file_size
     * @return array
     */
    public function convertCsvToArray($resource, int $file_size): array
    {
        $data = [];
        if (! $resource) return [];
        while ($row = fgetcsv(
            $resource,
            $file_size,
            $this->delimiter
        )) {
            $data[] = $this->formatParsedRow($row, 'classic');
        }
        return $data;
    }

    /**
     * Форматирует распарсеную строчку
     * в соответствии с указанным форматом
     *
     * @param array $row
     * @param string $format_name
     * @return array
     */
    private function formatParsedRow(array $row, string $format_name): array
    {
        switch ($format_name) {
            case 'classic':
                return [
                    (int) $row[0],
                    (string) trim($row[1]),
                    (int) $row[2]
                ];
                break;
            // add other formats...
            default:
                [];
        }
    }

    /**
     * Считывает CSV файл возращая размер и ресурс файла
     *
     * @return array
     */
    public function uploadCsvFile()
    {
        try {
            ini_set('auto_detect_line_endings', true);

            $file_path = realpath(
                dirname(__DIR__)
                . "/$this->path_to_csv_dir/"
                . $this->filename
                . $this->ext
            );

            if (! file_exists($file_path) || ! is_readable($file_path)) {
                throw new \Exception('file not readed');
            }

            return [
                fopen($file_path, 'r'),
                filesize($file_path)
            ];
        } catch (\Exception $e) {
            return [false, false];
        }

    }


}
