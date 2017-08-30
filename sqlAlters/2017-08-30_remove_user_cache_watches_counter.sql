-- 2017.08.30 kojoty
--

-- There is unneded counter of watched caches by user in table user.
-- Lets drop it.

ALTER TABLE user DROP COLUMN cache_watches;