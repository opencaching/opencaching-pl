-- following: fix ISO 639 code for Japanese


UPDATE languages SET short='JA' WHERE short='JP';
UPDATE cache_desc SET language='JA' WHERE language='JP';
UPDATE caches SET
    desc_languages = REPLACE(desc_languages, 'JP', 'JA'),
    default_desclang = REPLACE(default_desclang, 'JP', 'JA');
