## Technical description of the function: Titled Cache

### How to activate function on node

To activate the function, you should 
set $titled_cache_period_prefix in settings.ini.php ('month' or 'week'),
set $titled_cache_nr_found  in settings.ini.php (default is 10),
add an entry to the crontab, which launches cache_titled_add.php?CRON in a proper period, for example each week. There is a file cache_titled_add.php in the main folder of the  service. 
For example: 
```
15 4 * * 5 wget -O - -q http://opencaching.pl/cache_titled_add.php?CRON
```

### Function description
There is an algorithm in  cache_titled_add.php, which chooses the best cache.
The ID of the best cache is wrote into the table: cache_titled.
A congratulation mail is sent to the owner of the cache (there is the text of the mail in a proper language file, var: (month)(week)_titled_cache_congratulations).
There is a new section: Titled Cache on the main page. There, you can see the newest best cache 
There is new section: Titled Caches on the page: My neighborhood. You can see 10 titled caches, which are in a neighborhood of the user.
Each titled cache gets a medal. This medal appears on each titled cache's description page, next to the title of cache and on a balloon of the cache on V3 map.

### Alghoritm description
The Algorithm has two phases

I phase
ratio = recommendations/number of visits
wsk = ratio – (length of life /5000)

- the algorithm considers caches which:  
	are active
	aren't meetings
	aren't titled yet
	have 10 or more number of visits ($titled_cache_nr_found in settings.ini.php)

- next, the algorithm sorts them by key
	wsk (descending)
	number of visits (descending)
	date created (descending)

- next, the algorithm writes 30 caches in temporary table with information about their region  

II phase
- the algorithm sorts these 30 caches by key:
  Region (ascending) – number of caches which were titled
  Number of visits (descending)
  date created (ascending)
  wsk (descending)

- The algorithm indicates the first of the sorting caches, which becomes Titled Cache. 
