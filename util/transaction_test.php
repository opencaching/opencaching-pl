<?php
use Utils\Database\OcDb;
/*
 * This script checks if transactions is working in OC database
 * This is for test purpose only.
 *
 */

function check_database_count($db, $expected, $line)
{
    echo "<br>Expect <b>$expected</b> row(s)<br>";
    $count = $db->simpleQueryValue("select count(1) from transaction_test", -1);
    if ($count != $expected) {
        echo "Expected $expected row(s), found $count row(s) in database, line $line!<br>";
        $s = $db->simpleQuery("select * from transaction_test");
        echo '<table><tr><th>id</th><th>name</th></tr>';
        while (($row = $db->dbResultFetch($s)) !== false) {
            echo "<tr><td>{$row['id']}</td><td>{$row['name']}</td></tr>";
        }
        echo '</table>';
        die;
    }
}

$rootpath = "../";
require_once('../lib/common.inc.php');
// ob_start();
echo '<b>start</b><br>';
$db = OcDb::instance();
$db->simpleQuery("drop table if exists transaction_test;");
$db->simpleQuery("
        create table transaction_test (
            id int(10) not null auto_increment,
            name varchar(50) not null,
            PRIMARY KEY (id)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8;");

$db2 = new dataBase();

// test transaction isolation
$db2->beginTransaction();
$db2->simpleQuery("insert into transaction_test (name) values('Asia')");
$db2->simpleQuery("insert into transaction_test (name) values('Kasia')");
$db2->simpleQuery("insert into transaction_test (name) values('Natalia')");
check_database_count($db, 0, __line__);

// test commit
$db2->commit();
check_database_count($db, 3, __line__);

// test no-transaction
$db2->simpleQuery("insert into transaction_test (name) values('Basia')");
check_database_count($db, 4, __line__);

// test rollback
$db2->beginTransaction();
$db2->simpleQuery("delete from transaction_test where name = 'Kasia'");
if ($db2->rowCount() !== 1) {
    echo "Expected 1 row to be deleted!<br>";
    die;
}
check_database_count($db, 4, __line__);
check_database_count($db2, 3, __line__);
$db2->rollback();
check_database_count($db2, 4, __line__);

$db2->simpleQuery("delete from transaction_test;");
$db2->simpleQuery("insert into transaction_test (name) values('Kasia')");

// test transation nesting
$db2->beginTransaction();
$db2->simpleQuery("insert into transaction_test (name) values('Zosia')");
$db2->beginTransaction();
$db2->simpleQuery("insert into transaction_test (name) values('Gosia')");
$db2->simpleQuery("insert into transaction_test (name) values('Marta')");
$db2->commit();

check_database_count($db, 1, __line__);
$db2->simpleQuery("insert into transaction_test (name) values('Hermenegilda')");
check_database_count($db, 1, __line__);
$db2->commit();
check_database_count($db, 5, __line__);
check_database_count($db2, 5, __line__);

// test transaction nesting with rollback
$db2->beginTransaction();
$db2->simpleQuery("insert into transaction_test (name) values('Zosia')");
$db2->beginTransaction();
$db2->simpleQuery("insert into transaction_test (name) values('Gosia')");

$db2->beginTransaction();
$db2->simpleQuery("insert into transaction_test (name) values('Monika')");
$db2->commit();

$db2->simpleQuery("insert into transaction_test (name) values('Marta')");
$db2->rollback();

check_database_count($db, 5, __line__);
$db2->simpleQuery("insert into transaction_test (name) values('Hermenegilda')");
check_database_count($db, 5, __line__);
check_database_count($db2, 10, __line__);
$db2->commit(); // should rollback transaction!
check_database_count($db, 5, __line__);
check_database_count($db2, 5, __line__);

$db2->simpleQuery("delete from transaction_test;");
$db2->beginTransaction();
$db2->simpleQuery("insert into transaction_test (name) values('Michalina')");
//$db->simpleQuery("drop table if exist transaction_test;");
echo 'If you see this message, it means that each tests <b>PASSED</b>!<br>';

// should leave table empty!
?>
