NUTS tables
===========

    These tables (from EU data sources) contain definitions and geographical
data to define regions within the EU.
    Data: NUTS-2013
    File: eu-nuts.tar.gz

    Import these files into your database if your node operates within EU.

Important!
----------
    Always update cache_locations table after such an import
using the following command:

/path/to/do-wget-url util.sec/cache_locations/cache_location.class.php cache_location.html
