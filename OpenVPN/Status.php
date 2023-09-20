<?php
/*
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published by
 * the Free Software Foundation; either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Lesser General Public License for more details.
 *
 */

namespace BLKTech\Network\OpenVPN;

use BLKTech\DataType\Path;

/**
 *
 * @author TheKito < blankitoracing@gmail.com >
 */

class Status
{
    private static $headersClients = array('Common Name', 'Real Address', 'Bytes Received', 'Bytes Sent', 'Connected Since');
    private static $headersRouting = array('Virtual Address', 'Common Name', 'Real Address', 'Last Ref');

    public static function getStatus()
    {
        return new Status(Path::getPathFromString('/var/log/openvpn-status.log'));
    }


    private $statusFilePath;
    private $lastRead = 0;

    private $clients = array();
    private $routes = array();
    private $lines = array();

    public function __construct(Path $statusFilePath)
    {
        $this->statusFilePath = $statusFilePath;
    }

    private function parseFile()
    {
        if(filemtime($this->statusFilePath) <= $this->lastRead) {
            return;
        }

        $this->clients = array();
        $this->routes = array();
        $this->lines = array();

        $reader = new \BLKTech\FileSystem\File\Reader($this->statusFilePath);

        $clientsSection = false;
        $routingSection = false;

        while(!$reader->eof()) {
            $line = $reader->readLine();
            $line = trim(str_replace("\n", '', str_replace("\r", '', $line)));
            $line = explode(',', $line);
            foreach($line as $index => $value) {
                $line[$index] = trim($value);
            }

            if(self::lineIsHeader(self::$headersClients, $line)) {
                $clientsSection = true;
                $routingSection = false;
            } elseif(self::lineIsHeader(self::$headersRouting, $line)) {
                $clientsSection = false;
                $routingSection = true;
            } elseif($clientsSection && count($line) == count(self::$headersClients)) {
                $this->clients[] = array_combine(self::$headersClients, $line);
            } elseif($routingSection && count($line) == count(self::$headersRouting)) {
                $this->routes[] = array_combine(self::$headersRouting, $line);
            } else {
                $this->lines[] = $line;
            }
        }

        unset($reader);



    }

    private static function lineIsHeader($headerArray, $lineArray)
    {
        return strtolower(preg_replace("/[^A-Za-z0-9]/", '', implode('', $headerArray))) == strtolower(preg_replace("/[^A-Za-z0-9]/", '', implode('', $lineArray)));
    }


}
