<?php

namespace src\Utils\Database\Updates;

use PDOException;

/**
 * This is a unit test for all the table-structure methods in OcDb.
 * It can only be run manually via the the sysadmin interface.
 * It can be safely run anytime anywhere (also on production sites),
 * to test if the update system is healthy.
 *
 * If this update runs through without any error or warning messages,
 * everything is ok.
 */

class C001Test extends UpdateScript
{
    public function getProperties()
    {
        return [
            'uuid' => '32371BEC-9FF1-6066-A483-C3A3A38F49C6',  // do not change
            'run' => 'manual',  // do not change
        ];
    }

    const TABLE = 'db_update_tests';

    private $runNo;

    public function run()
    {
        // run all the structure-related methods twice, so we test "exists"
        // and "not exists" for both cases

        self::startTest();

        // remove old table for clean start conditions

        $this->db->dropTableIfExists(self::TABLE);
        $this->assertTrue(
            'dropTableIfExists', 1,
            !$this->db->tableExists(self::TABLE)
        );

        // run everything twice, to test both cases of "IfNotExists".

        foreach ([1, 2] as $this->runNo) {
            $this->testRunsPerMethod = [];

            // run all structure creation methods

            $this->db->createTableIfNotExists(
                self::TABLE,
                ['uuid varchar(36) NOT NULL', 'created datetime NOT NULL'],
                ['engine' => 'innodb']  // must be InnoDB e.g. for foreign key tests
            );
            $this->db->addColumnIfNotExists(
                self::TABLE, 'fnumber', 'float(5,1) unsigned DEFAULT 99', 'some float number'
            );
            $this->db->addColumnIfNotExists(
                self::TABLE, 'id', 'int(11) DEFAULT NULL', '', 'uuid'
            );
            $this->db->addColumnIfNotExists(
                self::TABLE, 'modifytest', "char(2) DEFAULT 'A'", 'test column'
            );
            $this->db->addPrimaryKeyIfNotExists(
                self::TABLE, 'uuid'
            );
            $this->db->addUniqueIndexIfNotExists(
                self::TABLE, 'created'
            );
            $this->db->addIndexIfNotExists(
                self::TABLE, 'uc', ['uuid', 'created']
            );
            /*  not available at OC RO
            $this->db->addFulltextIfNotExists(
                self::TABLE, 'uft', ['uuid']
            );
            */
            $this->db->addForeignKeyIfNotExists(
                self::TABLE, 'id', 'caches', 'cache_id', 'ON DELETE RESTRICT ON UPDATE RESTRICT'
            );

            // verify the result and test structure-querying methods

            $this->assertTrue(
                'tableExists', 10,
                $this->db->tableExists(self::TABLE)
            );
            $this->assertTrue('tableExists', 3,
                !$this->db->tableExists(self::TABLE.self::TABLE)
            );

            $this->assertTrue(
                'columnExists', 11,
                $this->db->columnExists(self::TABLE, 'id')
            );
            $this->assertTrue(
                'columnExists', 12,
                !$this->db->columnExists(self::TABLE, 'noid')
            );

            $this->assertStrMatch(
                'getFullColumnType', 13,
                "float\(5,1\) unsigned DEFAULT (99(\.0)?|'99(\.0)?')",
                $this->db->getFullColumnType(self::TABLE, 'fnumber')
            );
            $this->assertStrMatch(
                'getFullColumnType', 14,
                'varchar\(36\) not null',
                $this->db->getFullColumnType(self::TABLE, 'uuid')
            );
            $this->assertStrMatch(
                'getFullColumnType', 15,
                 "int\(11\) default null",
                 $this->db->getFullColumnType(self::TABLE, 'id')
            );
            $this->assertStrMatch(
                'getColumnComment', 16,
                'some float number',
                $this->db->getColumnComment(self::TABLE, 'fnumber')
            );

            $this->assertTrue('indexExists', 20,
                $this->db->indexExists(self::TABLE, 'primary')
            );
            $this->assertTrue('indexExists', 21,
                $this->db->indexExists(self::TABLE, 'id')
            );
            $this->assertTrue('indexExists', 22,
                $this->db->indexExists(self::TABLE, 'uc')
            );
            /*  not available at OC RO
            $this->assertTrue('indexExists', 23,
                $this->db->indexExists(self::TABLE, 'uft')
            );
            */
            $this->assertTrue('foreignKeyExists', 24,
                $this->db->foreignKeyExists(self::TABLE, 'id', 'caches')
            );

            // test structure changes

            $this->db->updateColumnType(
                self::TABLE, 'modifytest', 'text NOT NULL'
            );
            $this->assertStrMatch(
                'getFullColumnType', 30,
                 "text not null",
                 $this->db->getFullColumnType(self::TABLE, 'modifytest')
            );
            $this->assertStrMatch(
                'getColumnComment', 31,
                'test column',
                $this->db->getColumnComment(self::TABLE, 'modifytest')
            );

            $this->db->updateColumnComment(
                self::TABLE, 'id', 'new comment'
            );
            $this->assertStrMatch(
                'getColumnComment', 32,
                'new comment',
                $this->db->getColumnComment(self::TABLE, 'id')
            );
            $this->assertStrMatch(
                'getFullColumnType', 33,
                 "int\(11\) default null",
                 $this->db->getFullColumnType(self::TABLE, 'id')
            );

            foreach ([1, 2] as $n) {
                $this->db->dropIndexIfExists(
                    self::TABLE, 'created'
                );
                $this->assertTrue(
                    'indexExists', 40 + $n,
                    !$this->db->indexExists(self::TABLE, 'created')
                );

                $this->db->dropForeignKeyIfExists(
                    self::TABLE, 'id', 'caches'
                );
                $this->assertTrue(
                    'foreignKeyExists', 42 + $n,
                    !$this->db->foreignKeyExists(self::TABLE, 'id', 'caches')
                );
            }
        }

        // test routines

        $trigger = 'db_update_test_trigger';
        $procedure = 'db_update_test_proc';
        $function = 'db_update_test_func';

        $this->db->createOrReplaceTrigger(
            $trigger, 'BEFORE UPDATE ON '.self::TABLE." FOR EACH ROW BEGIN SET @A=1; END"
        );
        $this->assertTrue(
            'triggerExists', 50,
            $this->db->triggerExists($trigger)
        );
        $this->db->createOrReplaceTrigger(
            $trigger, 'BEFORE UPDATE ON '.self::TABLE." FOR EACH ROW BEGIN SET @A=1; END"
        );
        $this->db->dropTriggerIfExists($trigger);
        $this->assertTrue(
            'triggerExists', 51,
            !$this->db->triggerExists($trigger)
        );
        $this->db->dropTriggerIfExists($trigger);

        $this->db->createOrReplaceProcedure(
            $procedure, [], "BEGIN SET @A=1; END"
        );
        $this->assertTrue(
            'procedureExists', 52,
            $this->db->procedureExists($procedure)
        );
        $this->db->createOrReplaceProcedure(
            $procedure, [], "BEGIN SET @A=2; END"
        );
        $this->db->dropProcedureIfExists($procedure);
        $this->assertTrue(
            'procedureExists', 53,
            !$this->db->procedureExists($procedure)
        );
        $this->db->dropProcedureIfExists($procedure);

        $this->db->createOrReplaceFunction(
            $function, [], "INT", "DETERMINISTIC", "BEGIN SET @A=1; RETURN @A; END"
        );
        $this->assertTrue(
            'functionExists', 54,
            $this->db->functionExists($function)
        );
        $this->db->createOrReplaceFunction(
            $function, [], "INT", "DETERMINISTIC", "BEGIN SET @A=2; RETURN @A; END"
        );
        $this->db->dropFunctionIfExists($function);
        $this->assertTrue(
            'functionExists', 55,
            !$this->db->functionExists($function)
        );
        $this->db->dropFunctionIfExists($function);

        // test other OcDb methods, which may be used for DB updates

        $this->runNo = 0;

        $this->db->simpleQuery(
            "INSERT INTO ".self::TABLE." (uuid, id, created)
            VALUES ('uuid1', 1, NOW())"
        );
        $this->assertTrue(
            'simpleQueryValue', 30,
            $this->db->simpleQueryValue("SELECT id FROM ".self::TABLE, 0) == 1
        );

        $this->db->multiVariableQuery(
            "INSERT INTO ".self::TABLE. " (uuid, id, created)
            VALUES (:1, :2, :3)",
            'uuid2', 2, '2018-01-01'
        );
        $this->assertTrue(
            'simpleQueryValue', 31,
            $this->db->multiVariableQueryValue(
                "SELECT uuid FROM ".self::TABLE." WHERE id = :1",
                '',
                2
            ) == 'uuid2'
        );

        $row = $this->db->dbResultFetchOneRowOnly(
            $this->db->simpleQuery("SELECT * FROM ".self::TABLE." LIMIT 1")
        );
        $this->assertTrue('dbResultFetchOneRowOnly', 32, $row['uuid'] == 'uuid1');

        $col = $this->db->dbFetchOneColumnArray(
            $this->db->simpleQuery("SELECT id FROM ".self::TABLE),
            'id'
        );
        $this->assertTrue('dbFetchOneColumnArray', 33, $col[0] == 1);

        $dict = $this->db->dbResultFetchAllAsDict(
            $this->db->simpleQuery("SELECT id, uuid, created FROM ".self::TABLE)
        );
        $this->assertTrue('dbResultFetchAllAsDict', 34, $dict[2]['uuid'] == 'uuid2');

        self::finishTest();
    }

    public function rollback()
    {
        self::startTest();

        $this->db->dropTableIfExists(self::TABLE);
        $this->assertTrue(
            'dropTableIfExists', 0,
            !$this->db->tableExists(self::TABLE)
        );
        $this->db->dropTableIfExists(self::TABLE);

        self::finishTest();
    }


    private function assertTrue($methodName, $testNo, $ok, $comment = '')
    {
        if (!$ok) {
            echo $methodName . " " . $testNo.".".$this->runNo . " failed" . $comment ."\n";
        }
    }

    private function assertStrMatch($methodName, $testNo, $regex, $str)
    {
        $this->assertTrue(
            $methodName, $testNo,
            preg_match("/^".$regex."$/i", $str),
            ": expected ".$regex.", got ".$str
        );
    }

};

return new C001Test;
