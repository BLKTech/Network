<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace BLKTech\Network\OpenVPN;

/**
 * Description of Status
 *
 */

class Status 
{
    private $statusFilePath;
    
    public function __construct($statusFilePath = '/var/log/openvpn-status.log') 
    {
        $this->statusFilePath = $statusFilePath;
    }

    
}
