<?php


namespace Src;


interface ParserInterface
{

    /**
     * ParserInterface constructor.
     * @param string $filename
     * @param string $delimiter
     * @param string $ext
     * @param string $path_to_csv_dir
     */
    function __construct(
        string $filename,
        string $delimiter = ',',
        string $ext = '',
        string $path_to_csv_dir = 'data'
    );

    function parse(): array;
}
