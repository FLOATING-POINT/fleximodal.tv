<?php

if (strtolower(substr(PHP_OS, 0, 5)) === 'linux')
{
    $vars = array();
    $files = glob('/etc/*-release');

    foreach ($files as $file)
    {
        $lines = array_filter(array_map(function($line) {

            // split value from key
            $parts = explode('=', $line);

            // makes sure that "useless" lines are ignored (together with array_filter)
            if (count($parts) !== 2) return false;

            // remove quotes, if the value is quoted
            $parts[1] = str_replace(array('"', "'"), '', $parts[1]);
            return $parts;

        }, file($file)));

        foreach ($lines as $line)
            $vars[$line[0]] = $line[1];
    }

    print_r($vars);
}

?>