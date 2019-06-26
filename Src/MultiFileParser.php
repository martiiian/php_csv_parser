<?php

namespace Src;
require_once "Parser.php";

class MultiFileParser
{
    /**
     * массив с информацией о исходных CSV файлах
     * @var array|mixed
     */
    protected $src_files = [];

    /**
     * Дирректория с которой подгружаются файлы автоматом
     * @var array|mixed
     */
    protected $src_directory = '';

    /**
     * Распарсенные и совмещенные данные
     * @var array
     */
    protected $parsed_data = [];

    /**
     *
     * @var string
     */
    protected $out_delimiter = ',';


    protected $enclosure = '"';

    /**
     * Название дирректории для выходных данных
     * @var string
     */
    protected $output_dir = 'result';

    /**
     * Расширение выходного файла
     * @var string
     */
    protected $ext = '.csv';

    /**
     * Максимальное количество объектов с уникальным id
     * @var int|mixed
     */
    protected $count_same_id_constraint = 2;

    /**
     * Максимальное количество строк в результирующем файле
     * @var int|mixed
     */
    protected $max_count_constraint = 3;

    /**
     * Индекс колонки, по которой производится сортировка
     * @var int|mixed
     */
    protected $sort_column = 2;

    /**
     * Колонка по которой делается ограничение на уникальность
     * @var int|mixed
     */
    protected $id_column = 0;

    /**
     * MultiFileParser constructor.
     * @param array $data
     * @throws \Exception
     */
    public function __construct(array $data)
    {
        if (
            isset($data['src_directory'])
            && is_string($data['src_directory'])
            && strlen($data['src_directory']) > 0
        ) {
            $this->src_directory = $data['src_directory'];
            if (! file_exists($data['src_directory'])) {
                throw new \Exception("src directory doesn't exist");
            }
            $entries =  scandir($data['src_directory']);
            foreach($entries as $entry) {
                if (mb_strpos($entry, '.csv') !== false) {
                    array_push($this->src_files, substr($entry, 0, -4));
                }
            }
            if (count($this->src_files) === 0) {
                throw new \Exception("src directory doesn't contain csv files");
            }
        } else {
            if (count($data['src_files']) === 0) {
                throw new \Exception('file names is empty!');
            }
            $this->src_files = $data['src_files'];
        }
        $this->count_same_id_constraint = $data['count_same_id_constraint'] ?? $this->count_same_id_constraint;
        $this->max_count_constraint = $data['max_count_constraint'] ?? $this->max_count_constraint;
        $this->sort_column = $data['sort_column'] ?? $this->sort_column;
        $this->id_column = $data['id_column'] ?? $this->id_column;
    }

    /**
     * @return $this
     */
    public function parse()
    {
        foreach($this->src_files as $src_file) {
            $parser = $this->src_directory
                ? new Parser($src_file, [
                    'csv_src_dir_name' => $this->src_directory
                ])
                : new Parser($src_file['name'], [
                    'delimiter' => $src_file['delimiter'],
                    'ext' => $src_file['ext'],
                    'csv_src_dir_name' => $this->src_directory
                ]);
            $this->parsed_data = array_merge($this->parsed_data, $parser->parse());
        }
        return $this;
    }

    /**
     * Возвращает распарсенные и отсортированные данные
     *
     * @return array
     */
    public function getParsedData(): array
    {
        return $this->getSortedData();
    }

    /**
     * Сортирует данные
     *
     * @return array
     */
    private function getSortedData(): array
    {
        usort($this->parsed_data, $this->sortArrayByKey($this->sort_column, 'DESC'));
        return $this->parsed_data;
    }

    /**
     * Функция сортировки данных по индексу
     *
     * @param $key
     * @param string $order
     * @return \Closure
     */
    private function sortArrayByKey($key, $order = 'ASC')
    {
        $order = ($order == 'DESC') ? -1 : 1;
        return function ($a, $b) use ($key, $order) {
            if ($a[$key] == $b[$key]) {
                return 0;
            }
            return $order * ($a[$key] < $b[$key] ? 1 : -1);
        };
    }

    /**
     * Фильтрация по ограничениям и запись данных в csv
     *
     * @return string
     * @throws \Exception
     */
    public function write(): string
    {
        $prepared_csv_data = '';
        $same_id_constraint_data = [];
        $count = 0;
        foreach ($this->getSortedData() as $row) {
            if (
                ! $this->checkSameIdConstraint($same_id_constraint_data, $row)
                || $this->max_count_constraint === $count
            ) continue;
            $prepared_csv_data .= $this->convertArrayToString($row);
            ++$count;
        }
        $file_name = md5(time()) . $this->ext;
        $file_dir_path = dirname(__DIR__) . "/$this->output_dir/";
        if (! file_exists($file_dir_path) && ! mkdir($file_dir_path)) {
            throw new \Exception('cannot create directory for save result');
        }

        return file_put_contents($file_dir_path . '/' . $file_name, $prepared_csv_data)
            ? $file_name
            : '';
    }

    /**
     * Проверяет ограничения на повторы объектов столбца id_column
     *
     * @param array $same_id_constraint_data
     * @param array $row
     * @return bool
     */
    private function checkSameIdConstraint(
        array &$same_id_constraint_data,
        array $row
    ): bool {
        $key_exist = array_key_exists($row[$this->id_column], $same_id_constraint_data);
        if (
            $key_exist
            && $same_id_constraint_data[$this->id_column] >= $this->count_same_id_constraint
        ) return false;
        if (! $key_exist) {
            $same_id_constraint_data[$this->id_column] = 0;
        }
        ++$same_id_constraint_data[$this->id_column];
        return true;
    }

    /**
     * Конвертирует массив в csv стоку нужного формата
     *
     * @param array $input
     * @return string
     */
    private function convertArrayToString(array $input): string
    {
        $fp = fopen('php://temp', 'r+');
        fputcsv($fp, $input, $this->out_delimiter, $this->enclosure);
        rewind($fp);
        $string = stream_get_contents($fp);
        fclose($fp);
        return $string;
    }

}
