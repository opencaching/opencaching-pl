-- Remove duplicates leaving the records with lowest id
DELETE cw1
FROM `cache_watches` cw1, `cache_watches` cw2
WHERE
    cw1.`id` > cw2.`id`
    AND cw1.`cache_id`=cw2.`cache_id`
    AND cw1.`user_id`=cw2.`user_id`;

-- Set correct watcher values in caches table
UPDATE `caches` SET `watcher`=(
    SELECT COUNT(*)
    FROM `cache_watches`
    WHERE
        `cache_watches`.`cache_id`=`caches`.`cache_id`
    GROUP BY `cache_watches`.`cache_id`
);

-- Drop id column being the primary key in cache_watches
ALTER TABLE `cache_watches` DROP COLUMN `id`;

-- Add (cache_id, user_id) pair as a primary key
ALTER TABLE `cache_watches` ADD PRIMARY KEY (`cache_id`, `user_id`);
