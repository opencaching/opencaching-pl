<?php

$cookie = new cookie();

class cookie
{

    var $changed = false;
    var $values = array();

    function cookie()
    {
        global $opt;

        if (isset($_COOKIE[$opt['cookie']['name'] . 'data'])) {
            //get the cookievars-array
            // returns false in strict mode, if not valid base64 input
            $decoded = base64_decode($_COOKIE[$opt['cookie']['name'] . 'data'], true);

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
        global $opt;

        if (isset($_COOKIE[$opt['cookie']['name'] . 'data']))
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
        global $opt;

        if ($this->changed == true) {
            if (count($this->values) == 0){
                setcookie(
                    $opt['cookie']['name'] . 'data', false,
                    time() + 31536000, $opt['cookie']['path'],
                    $opt['cookie']['domain'], 0);
            } else {
                setcookie(
                    $opt['cookie']['name'] . 'data',
                    base64_encode(json_encode($this->values)),
                    time() + 31536000, $opt['cookie']['path'],
                    $opt['cookie']['domain'], 0);
            }
        }
    }

    function debug()
    {
        print_r($this->values);
        exit;
    }

}


