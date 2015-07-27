USE opencachin_ocpl;

DROP TRIGGER IF EXISTS cacheDescBeforeInsert;
DROP TRIGGER IF EXISTS cacheDescAfterInsert;
DROP TRIGGER IF EXISTS cacheDescAfterUpdate;
DROP TRIGGER IF EXISTS cacheDescAfterDelete;

DROP TRIGGER IF EXISTS cacheRatingAfterInsert;
DROP TRIGGER IF EXISTS cacheRatingAfterUpdate;
DROP TRIGGER IF EXISTS cacheRatingAfterDelete;

DROP TRIGGER IF EXISTS cachesAfterInsert;
DROP TRIGGER IF EXISTS cachesAfterUpdate;
DROP TRIGGER IF EXISTS cachesAfterDelete;
