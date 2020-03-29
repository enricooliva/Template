<?php
/**
 * uniurb/unidem package configuration file. 
 */
return [

    /**
     * Datepicker configuration:
     */
    'date_format'        => 'd-m-Y',
    'date_format_jquery' => 'dd-mm-yyyy',
    'time_format'        => 'H:i:s',
    'time_format_jquery' => 'HH:mm:ss',

    'datetime_format' => 'd-m-Y H:i:s',
    'timezone' => 'Europe/Rome',

    'date_format_contratto' => 'd/m/Y',

    /**
     * Quickadmin settings
     */
    // Default route
    'route'              => '',  
    
    'client_url' => env('CLIENT_URL', ''),   

    // Default role to access users
    //'defaultRole'        => ''
     
    'unitaSuperAdmin' => ['005400'],
    'unitaAdmin' => explode(',',env('UFF_ADMIN','005199')),    

    'administrator_email' =>  explode(',',env('ADMINISTRATOR_EMAIL', 'enrico.oliva@uniurb.it')),   
         
];