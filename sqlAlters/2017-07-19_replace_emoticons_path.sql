-- 2017.07.19 
--

-- after tinymce update link to emoticons is changed and needs to be chenged in DB as well

UPDATE cache_logs SET text = REPLACE(text, 'lib/tinymce/plugins/emotions/images/smiley-', 'lib/tinymce4/plugins/emoticons/img/smiley-')
UPDATE cache_desc SET `desc` = REPLACE(`desc`, 'lib/tinymce/plugins/emotions/images/smiley-', 'lib/tinymce4/plugins/emoticons/img/smiley-')
