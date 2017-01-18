-- 2017-01-18 Change cache size from Normal to Regular.
-- @author: Harrie Klomp

UPDATE cache_size SET en = 'Regular' WHERE cache_size.id = 4;
