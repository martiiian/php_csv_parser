<?php

namespace Src;

class MultiFileParser implements MultiFileParserInterface
{
    protected $file_names = [];
    protected $parsed_data = [];
    protected $delimiter = ',';
    protected $enclosure = '"';
    protected $output_dir = 'result';
    protected $ext = '.csv';
    protected $count_same_id_constraint = 2;
    protected $max_count_constraint = 3;
    protected $sort_column = 2;
    protected $id_column = 0;

    /**
     * MultiFileParser constructor.
     * @param array $data
     * @throws \Exception
     */
    public function __construct(array $data)
    {
        $this->file_names = $data['file_names'];
        if (count($data['file_names']) === 0) {
            throw new \Exception('file names is empty!');
        }
        $this->count_same_id_constraint = $data['count_same_id_constraint'] ?? $this->count_same_id_constraint;
        $this->max_count_constraint = $data['max_count_constraint'] ?? $this->max_count_constraint;
        $this->sort_column = $data['sort_column'] ?? $this->sort_column;
        $this->id_column = $data['id_column'] ?? $this->id_column;
    }

    /**
     * @return $this|MultiFileParserInterface
     */
    public function parse(): MultiFileParserInterface
    {
        foreach($this->file_names as $file_name) {
            $parser = new Parser($file_name, $this->delimiter, $this->ext);
            $this->parsed_data = array_merge($this->parsed_data, $parser->parse());
        }
        return $this;
    }

    /**
     * @return array
     */
    public function getParsedData(): array
    {
        return $this->getSortedData();
    }

    /**
     * @return array
     */
    private function getSortedData(): array
    {
        usort($this->parsed_data, $this->sortArrayByKey($this->sort_column, 'DESC'));
        return $this->parsed_data;
    }

    /**
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
     * @return string
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
        $file_dir_path = realpath(
            dirname(__DIR__)
            . "/$this->output_dir/"
        );
        return file_put_contents($file_dir_path . '/' . $file_name, $prepared_csv_data)
            ? $file_name
            : '';
    }

    /**
     * @param array $same_id_constraint_data
     * @param array $row
     * @return bool
     */
    private function checkSameIdConstraint(array &$same_id_constraint_data, array $row): bool
    {
        $key_exist = array_key_exists($row[$this->id_column], $same_id_constraint_data);
        if ($key_exist && $same_id_constraint_data[$this->id_column] >= $this->count_same_id_constraint) return false;
        if (! $key_exist) {
            $same_id_constraint_data[$this->id_column] = 0;
        }
        ++$same_id_constraint_data[$this->id_column];
        return true;
    }

    /**
     * @param array $input
     * @return string
     */
    private function convertArrayToString(array $input): string
    {
        $fp = fopen('php://temp', 'r+');
        fputcsv($fp, $input, $this->delimiter, $this->enclosure);
        rewind($fp);
        $string = stream_get_contents($fp);
        fclose($fp);
        return $string;
    }

}
