<?php

$cookie = new cookie();

class cookie
{

    var $changed = false;
    var $values = array();

    function cookie()
    {
        global $config;

        if (isset($_COOKIE[$config['cookie']['name'] . 'data'])) {
            //get the cookievars-array
            // returns false in strict mode, if not valid base64 input
            $decoded = base64_decode($_COOKIE[$config['cookie']['name'] . 'data'], true);

            if ($decoded !== false) {
                $this->values = @json_decode($decoded, true, 2);

                if (!is_array($this->values))
                    $this->values = array();
            } else
                $this->values = array();
        }
    }

    function set($name, $value)
    {
        if (!isset($this->values[$name]) || $this->values[$name] != $value) {
            $this->values[$name] = $value;
            $this->changed = true;
        }
    }

    function get($name)
    {
        return isset($this->values[$name]) ? $this->values[$name] : '';
    }

    function is_set($name)
    {
        return isset($this->values[$name]);
    }

    function is_set_cookie()
    {
        global $config;

        if (isset($_COOKIE[$config['cookie']['name'] . 'data']))
            return true;
        else
            return false;
    }

    function un_set($name)
    {
        if (isset($this->values[$name])) {
            unset($this->values[$name]);
            $this->changed = true;
        }
    }

    function header()
    {
        global $config;

        if ($this->changed == true) {

            // Config setting for mobile cookies is missing. Use the main page
            // setting and hack 'm.' into the domain.

            $domain = $config['cookie']['domain'];
            if (preg_match('/^(.+?)?open(.+?)$/', $domain, $matches)) {
                if ($matches[1] == 'www.') {
                    $domain = 'm.open'.$matches[2];
                } else {
                    $domain = $matches[1].'m.open'.$matches[2];
                }
            }

            if (count($this->values) == 0) {
                setcookie(
                    $config['cookie']['name'] . 'data', false,
                    time() + 31536000, $config['cookie']['path'],
                    $domain, 0);
            } else {
                setcookie(
                    $config['cookie']['name'] . 'data',
                    base64_encode(json_encode($this->values)),
                    time() + 31536000, $config['cookie']['path'],
                    $domain, 0);
            }
        }
    }

    function debug()
    {
        print_r($this->values);
        exit;
    }

}


