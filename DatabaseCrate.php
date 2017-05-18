<?php

/**
 * Class DatabaseCrate
 * prepravni trida pro prenos vice databazi v ramci jedne instance
 *
 *
 * # prepravni instance pro pouzivani 2 a vice instanci stejne databazove vrstvy
 * #- \DatabaseCrate(@dibi.connection, @dibi2.connection)
 * #- \DatabaseCrate(@dibi.connection)
 *
 *
 * ### DatabaseCrate
 *
 * prepravka databazovych spojeni pro projekty s vice pripojenima do ruznych DB
 *
 *
 * @author  geniv
 * @package NetteWeb
 */
class DatabaseCrate
{
    public $database1, $database2;


    /**
     * defaultni konstruktor
     * @param Dibi\Connection $database1
     * @param Dibi\Connection $database2
     */
    function __construct(\Dibi\Connection $database1, \Dibi\Connection $database2)
    {
        $this->database1 = $database1;
        $this->database2 = $database2;
    }
}
