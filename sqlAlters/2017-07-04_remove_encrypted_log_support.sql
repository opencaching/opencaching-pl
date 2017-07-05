-- 2017.07.04 deg
--

-- remove "encrypt" from cache_log table (see issue #1063)
--

ALTER TABLE `cache_logs` DROP `encrypt`;