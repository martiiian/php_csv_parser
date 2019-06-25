<?php


namespace Src;


interface MultiFileParserInterface
{
    /**
     * MultiFileParserInterface constructor.
     *
     * @param array $data
     */
    public function __construct(array $data);

    function parse(): MultiFileParserInterface;

    function getParsedData(): array;

    function write(): string;
}
