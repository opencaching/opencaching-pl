<?php
/*
  sql("SELECT id FROM &tmpdb.table WHERE a=&1 AND &tmpdb.b='&2'", 12345, 'abc');

  returns: recordset or false
*/
function sql($sql)
{
  global $rootpath;
  global $sql_debug, $sql_warntime;
  global $sql_replacements;
  global $dblink, $sqlcommands;

  $args = func_get_args();
  unset($args[0]);

  $sqlpos = 0;
  $filtered_sql = '';

  // $sql von vorne bis hinten durchlaufen und alle &x ersetzen
  $nextarg = strpos($sql, '&');
  while ($nextarg !== false)
  {
    $nextchar = substr($sql, $nextarg + 1, 1);

    if (is_numeric($nextchar))
    {
      $arglength = 0;
      $arg = '';

      // nästes Zeichen das keine Zahl ist herausfinden
      while (preg_match('/^[0-9]{1}/', $nextchar) == 1)
      {
        $arg .= $nextchar;

        $arglength++;
        $nextchar = substr($sql, $nextarg + $arglength + 1, 1);
      }

      // ok ... ersetzen
      $filtered_sql .= substr($sql, $sqlpos, $nextarg - $sqlpos);
      $sqlpos = $nextarg + $arglength;

      if (isset($args[$arg]))
      {
        if (is_numeric($args[$arg]))
          $filtered_sql .= $args[$arg];
        else
        {
          if ((substr($sql, $sqlpos - $arglength - 1, 1) == '\'') && (substr($sql, $sqlpos + 1, 1) == '\''))
            $filtered_sql .= addslashes($args[$arg]);
          else if ((substr($sql, $sqlpos - $arglength - 1, 1) == '`') && (substr($sql, $sqlpos + 1, 1) == '`'))
            $filtered_sql .= addslashes($args[$arg]);
          else
            sql_error();
        }
      }
      else
        sql_error();

      $sqlpos++;
    }
    else
    {
      $arglength = 0;
      $arg = '';

      // nästes Zeichen das kein Buchstabe/Zahl ist herausfinden
      while (preg_match('/^[a-zA-Z0-9]{1}/', $nextchar) == 1)
      {
        $arg .= $nextchar;

        $arglength++;
        $nextchar = substr($sql, $nextarg + $arglength + 1, 1);
      }

      // ok ... ersetzen
      $filtered_sql .= substr($sql, $sqlpos, $nextarg - $sqlpos);

      if (isset($sql_replacements[$arg]))
      {
        $filtered_sql .= $sql_replacements[$arg];
      }
      else
        sql_error();

      $sqlpos = $nextarg + $arglength + 1;
    }

    $nextarg = strpos($sql, '&', $nextarg + 1);
  }

  // rest anhäen
  $filtered_sql .= substr($sql, $sqlpos);

  //
  // ok ... hier ist filtered_sql fertig
  //

  /* todo:
    - errorlogging
    - LIMIT
    - DROP/DELETE ggf. blocken
  */

  // Zeitmessung fü Ausfü
//  require_once($rootpath . 'lib/bench.inc.php');
//  $cSqlExecution = new Cbench;
//  $cSqlExecution->start();

  $result = mysql_query($filtered_sql, $dblink);
  if ($result === false) sql_error();

//  $cSqlExecution->stop();
//  if (isset($sql_debug) && ($sql_debug == true))
//    $sqlcommands[] = $filtered_sql;
//
//  if ($cSqlExecution->diff() > $sql_warntime)
//  {
//    sql_warn('execution took ' . $cSqlExecution->diff() . ' seconds');
//  }

  return $result;
}


function sqlValue($result, $default)
{
  if ($r = mysql_fetch_row($result))
  {
    mysql_free_result($result);

    if ($r[0] == null)
      return $default;
    else
      return $r[0];
  }
  else
  {
    mysql_free_result($result);
    return $default;
  }
}

function dbconnect()
{
  global $dblink, $opt;

  //connect to the database by the given method - no php error reporting!
  $dblink = mysql_connect($opt['db']['server'], $opt['db']['username'], $opt['db']['password']);

  if ($dblink != false)
  {
    //database connection established ... set the used database
    if (mysql_select_db($opt['db']['name'], $dblink) == false)
    {
      //error while setting the database ... disconnect
      dbdisconnect();
      $dblink = false;
    }
  }

  return ($dblink !== false);
}

//disconnect the databse
function dbdisconnect()
{
  global $dbpconnect, $dblink;

  //is connected and no persistent connect used?
  if ($dblink !== false)
  {
    mysql_close($dblink);
    $dblink = false;
  }
}

?>
