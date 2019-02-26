
# Credits

Source of GIS data:
- geonames.org: https://www.geonames.org/export/ licence: cc-by



# Countries information:

- countries data is based on data from http://download.geonames.org/export/dump/countryInfo.txt
- allCountriesCodes.json is generated based on attached countryInfo.txt file

- to recreate allCountriesCodes.json use bash command:

  cat countryInfo.txt | egrep -v '^#' | cut -f 1 | tr '\n' ',' | sed 's/,/","/g'| sed 's/..$/]/' | sed 's/^/["/' > allCountriesCodes.json

