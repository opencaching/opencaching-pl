# Description of OCPL folders structure (draft)

## 1. Inspirations and assumptions
	- motivation OCPL needs some cleanup 
	- the idea of PHP project universal structure: [https://github.com/php-pds/skeleton](https://github.com/php-pds/skeleton)
	- all accesses to folders are regulated by attached in git .htaccess files

## 2. Migration process
	- migration needs to be a process
	- files needs to be moved in git repo (to not break the repo history of files)	

## 2. Still open cases (TODO)

### `/okapi/` - where is the best place for OKAPI code ?
	- **contains** code of OKAPI - separate dir because OKAPI is an external but unique project for OCPL

## 3. Folders structure: root-level folders

### `/` (project root folder)
	- **access**: NO access for webserver (PHP not allowed)
	- finally it contains only:
		- README.md
		- LICENSE.txt
		- .htaccess
		- .gitignore
	- **migration**: all scripts from here will be temporary migrated to `/public`, but finally should be refactored to controllers 

### `/bin/` 
	- **access**: NO access for webserver (PHP not allowed - only command line)
	- root-level directory for command-line executable files
	- **migration:**  contains scripts which are used ONLY from command-line - now OCPL has such in :
		- `/ci`
		- `/util.sec`
		- ...?

### `/config/`
	- **access**: NO access from Internet, (PHP can only include from there)
	- root-level directory for configuration files
	- contains ONLY config files (PHP) 
	- **migration**: current `/Config` will be just renamed to `/config` + config scripts needs to be fixed

### `/docs/`
	- **access**: NO access for webserver (PHP not allowed)
	- root-level directory for documentation files
	- all doc files should be in markdown if possible
	- **migration** - no migration needed - folder stays as is :)

### `/public/`
	- **access**: access for webserver (PHP is allowed)
	- root-level directory for web server files
	- this folder contains in generally: 
		- index.php - the only PHP (dynamic) file here
		- JS files 
		- hosted JS libs (like tinyMCE)
		- CSSes
		- other STATIC files
	- folder structure - see below 
	- **migration** - see below

### `/mobile/`
	- **access**: blocked from main oc domain - mobile page has its own m.* domain (PHP is allowed)
	- **contains** - legacy mobile site

### `/resources/`
	- **access**: RW for webserver, (TODO: PHP ?)
	- a root-level directory for files which needs webserver RW access for uploaded files etc.
	- let's store main directory structure in git, but the content should be ignored (by git-ignore)
	- **migration** this should become more-less this what we called "ocpl-dynamic" so far

### `/src/`
	- **access**: No access from Internet, (PHP can include)
	- a root-level directory for PHP source code files
	- folder structure - see below
	- **migration** - almost all PHP scripts shoudl finally land here, but we need to migrate it partially

### `/vendor/`
	- external libraries maintained with composer

## 4. `/public/` folder structure

### `/public/index.php`
	- **ENTRY POINT** to the whole OCPL code - this code should route requests to proper Controller
	- finally it should be one of teh very only PHP script in `/public` folder (exception for OKAPI etc.)	
	
### `/public/views/`
	- **contains** JS + CSS strictly specific for given view
	- structure of this folder should reflect structure of `/srv/Views/` folder
	- if given view has other static content (like images) which are usefull ONLY by this view
	these images can be stored here as well

### `/public/js/`
	- **contains** JS "common" scripts used by many views - but not external libs (see above)

### `/public/js/libs/`
	- **contains** JS external libs which are hosted in OCPL code (like tinyMCE)
	- every lib has its own folder + README file with description how to update this lib
	- JS shoudl be loaded to Views in OCPL code by ONE chunk (every lib has only one reference in PHP code)

### `/public/img/`
	- **contains** common used images, icons etc.
	- images should be grouped in logical structure of folders
	- every folder with images should contains README file with description of origin/author of images
	
### `/public/xml/`
	- **contains** - legacy external interface to OC code


## 5. `/src/` folder structure
	- all subfolders names start with capital letter

### `/src/Controllers/`	
	- **migration** - generally the copy of the code of current `/Controllers`	
	
### `/src/Models/`
	- **migration** - generally the copy of the code of current `/lib/Objects`
	
### `/src/Views/`
	- **contains** code of PHP view templates
	- finally these scripts are used (included) ONLY by `/srv/Utils/View/View` class
	- **migration** - generally the copy of the PHP templates with code from current `/tpl/stdstyle`
	
### `/src/Utils/`
	- **migration** - generally the copy of the code from current `/Utils`
	
### `/src/Libs/`
	- **contains** EXTERNAL PHP libraries hosted in OCPL code
	- every libraru should be stored in separate folder 
	- every folder should contain README with information about given lib + update description

### `/i18n/`	
	- **contains**: translation files `en.php` + the rest which are generated by crowdin

