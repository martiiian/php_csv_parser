<?php


namespace Src;


interface ParserInterface
{

    /**
     * ParserInterface constructor.
     * @param string $filename
     * @param array $options
     */
    function __construct(
        string $filename,
        array $options = []
    );

    function parse(): array;
}
