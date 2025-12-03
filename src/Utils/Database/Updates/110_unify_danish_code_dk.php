<?php

// created on 2025-12-01 by stefopl

namespace src\Utils\Database\Updates;

return new class () extends UpdateScript {
    public function getProperties()
    {
        return [
            // see /docs/DbUpdate.md
            'uuid' => 'E4ED1041-FDA7-E7D5-DF6A-FE2F8799B48E',
            'run' => 'manual',
        ];
    }

    // IMPORTANT:
    // Any output by 'echo', 'print' etc. will be PUBLIC (see #1923).
    // Do not output any sensitive information.

    public function run()
    {
        $languages = $this->db->simpleQuery("
            SELECT * FROM languages where short='DK' OR short='DA'
        ")->fetch();
        $descCounts = $this->db->simpleQuery("
            SELECT 
                SUM(language = 'DA') AS desc_da,
                SUM(language = 'DK') AS desc_dk
            FROM cache_desc
        ")->fetch();
        $msg = sprintf(
            "Before update: languages %s",
            print_r($languages, true)
        );
        $msg .= sprintf(
            "Before update: cache_desc DA=%d DK=%d",
            $descCounts['desc_da'],
            $descCounts['desc_dk']
        );
        echo $msg . "\n";
        error_log($msg);

        $rows = $this->db->simpleQuery("
            SELECT id AS desc_id, language
            FROM cache_desc
            WHERE language IN ('DA','DK')
            ORDER BY id
        ")->fetchAll();

        echo "cache_desc DA/DK list:\n";
        error_log("cache_desc DA/DK list:");
        foreach ($rows as $row) {
            $line = sprintf("desc_id=%d language=%s", $row['desc_id'], $row['language']);
            echo $line . "\n";
            error_log($line);
        }

        $this->db->simpleQuery("
            UPDATE cache_desc
            SET language = 'DK'
            WHERE language = 'DA'
        ");

        $afterDescCounts = $this->db->simpleQuery("
            SELECT 
                SUM(language = 'DA') AS desc_da,
                SUM(language = 'DK') AS desc_dk
            FROM cache_desc
        ")->fetch();

        $msgAfter = sprintf(
            "After update: cache_desc DA=%d DK=%d",
            $afterDescCounts['desc_da'],
            $afterDescCounts['desc_dk']
        );
        echo $msgAfter . "\n";
        error_log($msgAfter);
    }

    public function rollback()
    {
        // If possible and feasible, provide code here which reverses the
        // changes made by run(). Otherwiese please REMOVE the rollback method.
        // This will disable the "rollback" action on the Admin.DbUpdate page.

        // The rollback will be run inside a transaction. It will also run
        // with set_time_limit(0), so don't create any endless loops!
    }
};
