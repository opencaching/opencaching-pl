/*
  Licensed under dual BSD/GPL license.
  Based on original mapper.php from opencaching.pl sources
  Author: "Damian Kaczmarek" <rush@rushbase.net>

  See respective license files in the main directory of the project.
*/


#ifdef WITH_FASTCGI
#include <fcgi_stdio.h>
#endif
#include <stdio.h>
#include <stdlib.h>
#include <string.h>
#include "microcgi.h"
#include <SDL/SDL.h>
#include <SDL/SDL_image.h>
#include <SDL/SDL_gfxBlitFunc.h>
#include <SDL/SDL_ttf.h>
#include <math.h>
#include <mysql/mysql.h>
#include <mysql/errmsg.h>
#include "IMG_savepng.h"
#include "config.h"

#define LABEL_FONT_SIZE 10


typedef struct geotile  {
	double lon;
	double lat;
	double lonWidth;
	double latHeight;
} geotile;

#define CACHE_TYPES_NUM 13 // number of different types, it equals maxid+1

const char* type2name(int type)
{
	switch(type)
    {
	default:
    case 1: return "unknown"; break;
    case 2: return "traditional"; break;
    case 3: return "multi"; break;
    case 4: return "virtual"; break;
    case 5: return "webcam"; break;
    case 6: return "event"; break;
    case 7: return "quiz"; break;
    case 8: return "moving"; break;
    case 9: return "quiz"; break;
    case 10: return "traditional"; break;
	case 11: return "podcache"; break;
	case 12: return "challenge"; break;
    }
}

void latlon_to_pix(double lat, double lon, geotile rect, int *x, int *y)
{
	lat = fabs(lat);
	lon = fabs(lon);
	double x_min = 0, x_max = 256;
	double y_min = 0, y_max = 256;
	double lon_max = rect.lon, lon_min = rect.lon+rect.lonWidth;
	double lat_min = rect.lat, lat_max = rect.lat+rect.latHeight;
	
	*x = round(x_min + (x_max - x_min) * ( 1 - (lon - lon_min) / (lon_max - lon_min) ));
	*y = round(y_max - (y_max - y_min) * ( (lat - lat_min) / (lat_max - lat_min) ));
}

geotile get_lat_long_xyz(int x, int y, int zoom)
{
	double lon = -180;
	double lonWidth = 360;

	double lat = -1;
	double latHeight = 2;

	int tilesAtThisZoom = 1 << (zoom);
	lonWidth = 360.0 / (double)tilesAtThisZoom;
	lon = -180.0 + (((double)x) * lonWidth);
	latHeight = 2.0 / (double)tilesAtThisZoom;
	lat = (( ((double)tilesAtThisZoom)/2 - y - 1) * latHeight);

	// convert lat and latHeight to degrees in transverse mercator projection
	// note that in fact the coordinates go from about -85 to +85 not -90 to 90!
	latHeight += lat;
	latHeight = (2 * atan(exp(M_PI * latHeight))) - (M_PI / 2);
	latHeight *= 180 / M_PI;

	lat = (2 * atan(exp(M_PI * lat))) - (M_PI / 2);
	lat *= (180.0 / M_PI);

	latHeight -= lat;

	if(lonWidth < 0) {
		lon = lon + lonWidth;
		lonWidth = -lonWidth;
	}
	if(latHeight < 0) {
		lat = lat + latHeight;
		latHeight = -latHeight;
	}

	return (geotile){lon, lat, lonWidth, latHeight};
}

static SDL_Surface* create_image(int w, int h)
{
#if SDL_BYTEORDER == SDL_BIG_ENDIAN
    Uint32 rmask = 0xff000000, gmask = 0x00ff0000, bmask = 0x0000ff00, amask = 0x000000ff;
#else
    Uint32 rmask = 0x000000ff, gmask = 0x0000ff00, bmask = 0x00ff0000, amask = 0xff000000;
#endif

	
	
	SDL_Surface *surface = SDL_CreateRGBSurface(SDL_SWSURFACE, w, h , 32,
												rmask,
												gmask,
												bmask,
												amask);

	SDL_Rect r = (SDL_Rect){0,0,w,h};
	SDL_FillRect(surface, &r,
				 SDL_MapRGBA(surface->format, 255, 255, 255, 0));

	return surface;
}

#ifdef WITH_FASTCGI
#define _LOOPBEGIN while(FCGI_Accept() >= 0) {
#define _LOOPEND }
#else
#define _LOOPBEGIN for(int i = 0;i < 1;++i) {
#define _LOOPEND }
#endif

#define DATA_PATH "data"

int main(void)
{
	MYSQL *conn = NULL;
	char buf[4096];

	config_handle* conf = config_load(DATA_PATH"/mapper.ini");
	if(!conf) {
		fprintf(stderr, "Config "DATA_PATH"/mapper.ini could not have been loaded.");
		return 1;
	}

	conn = mysql_init(NULL);


	const char *server = config_get(conf, "Connection", "Host", "localhost");
	const char *user = config_get(conf, "Connection", "Username", "");
	const char *password = config_get(conf, "Connection", "Password", "");
	const char *database = config_get(conf, "Connection", "DatabaseName", "ocpl");
	int port = config_get_int(conf, "Connection", "Port", 0);

   
	/* Connect to database */
	if (!mysql_real_connect(conn, server,
							user, password, database, port, NULL, 0)) {
#ifdef WITH_FASTCGI
		_LOOPBEGIN;
#endif
		fprintf(stdout, "Content-type: text/plain; charset=utf-8\r\n\r\n");
		fprintf(stdout, "%s\n", mysql_error(conn));
#ifdef WITH_FASTCGI
		_LOOPEND;
#endif
		mysql_close(conn);
		mysql_library_end();		
		config_free(conf);
		return -1;
	}
	mysql_query(conn,"SET NAMES utf8;");
#ifdef WITH_FASTCGI

	SDL_Surface *fcgi_cacheimgs[20][CACHE_TYPES_NUM];
	SDL_Surface *fcgi_redflagimg[20];
	SDL_Surface *fcgi_foundimg[20];
	SDL_Surface *fcgi_archivedimg[20];
	SDL_Surface *fcgi_markerimg[20];
	SDL_Surface *fcgi_markerfoundimg[20];
	SDL_Surface *fcgi_markernewimg[20];
	SDL_Surface *fcgi_markerownimg[20];


	for(int z = 0;z < 20;++z) {
		for(int i = 0;i < CACHE_TYPES_NUM;++i) {
			snprintf(buf, sizeof(buf), "%s/%s%i.png", DATA_PATH, type2name(i), z);
			fcgi_cacheimgs[z][i] = IMG_Load(buf);
			if(!fcgi_cacheimgs[z][i])
				fcgi_cacheimgs[z][i] = IMG_Load(DATA_PATH"/traditional.png");
		}
		snprintf(buf, sizeof(buf), "%s/redflagmap%i.png", DATA_PATH, z);
		fcgi_redflagimg[z] = IMG_Load(buf);
		snprintf(buf, sizeof(buf), "%s/foundmap%i.png", DATA_PATH, z);
		fcgi_foundimg[z] = IMG_Load(buf);
		snprintf(buf, sizeof(buf), "%s/archivedmap%i.png", DATA_PATH, z);
		fcgi_archivedimg[z] = IMG_Load(buf);
		snprintf(buf, sizeof(buf), "%s/marker%i.png", DATA_PATH, z);
		fcgi_markerimg[z] = IMG_Load(buf);
		snprintf(buf, sizeof(buf), "%s/markerfound%i.png", DATA_PATH, z);
		fcgi_markerfoundimg[z] = IMG_Load(buf);
		snprintf(buf, sizeof(buf), "%s/markernew%i.png", DATA_PATH, z);
		fcgi_markernewimg[z] = IMG_Load(buf);
		snprintf(buf, sizeof(buf), "%s/markerown%i.png", DATA_PATH, z);
		fcgi_markerownimg[z] = IMG_Load(buf);
	}
#endif
	TTF_Font* font = NULL;

	TTF_Init();
	snprintf(buf, sizeof(buf), "%s/DejaVuSans.ttf", DATA_PATH);	
	font = TTF_OpenFont(buf, LABEL_FONT_SIZE);
//	TTF_SetFontStyle(font, TTF_STYLE_BOLD);

	_LOOPBEGIN;

	microcgi_init();

	int x = microcgi_getint(CGI_GET, "x");
	int y = microcgi_getint(CGI_GET, "y");
	int zoom = microcgi_getint(CGI_GET, "z");
	if(zoom < 4) {		
		microcgi_cleanup();
		continue;
	}
	if(zoom > 19)
		zoom = 19;
	int userid = microcgi_getint(CGI_GET, "userid");

	geotile rect = get_lat_long_xyz(x, y, zoom);
	SDL_Surface *im = create_image(256, 256);

	int show_signs = !(strcmp(microcgi_getstr(CGI_GET, "signes"), "true"));

	double bound = 0.15;
	if(show_signs && zoom >= 13)
		bound = 0.55;
	char *h_sel_ignored = NULL;
	char *h_ignored = NULL;
	char *own_not_attempt = NULL;
	char *filter_by_type_string = NULL;
	char *filter_by_type_string2 = NULL;

	if(strcmp(microcgi_getstr(CGI_GET, "h_ignored"), "true") == 0) {
		h_sel_ignored = strdup("cache_ignore.id as ignored,");
		snprintf(buf, sizeof(buf), "LEFT JOIN cache_ignore ON (cache_ignore.user_id='%i' AND cache_ignore.cache_id=caches.cache_id)");
		h_ignored = strdup(buf);
	}
	else {
		h_sel_ignored = strdup("0,");
		h_ignored = strdup("");
	}
	if(strcmp(microcgi_getstr(CGI_GET, "be_ftf"), "true") == 0) {
		own_not_attempt = strdup("caches.founds>0");
		microcgi_setstr(CGI_GET, "h_temp_unavail", "true");
		microcgi_setstr(CGI_GET, "h_arch", "true");
	}
	else {
		snprintf(buf, sizeof(buf), "caches.cache_id IN (SELECT cache_id FROM cache_logs WHERE user_id='%i' AND (type = 1 OR type=8))", userid);
		own_not_attempt = strdup(buf);
	}
	if(strcmp(microcgi_getstr(CGI_GET, "h_nogeokret"), "true") == 0) {
		filter_by_type_string = strdup(" AND caches.cache_id IN (SELECT cache_id FROM caches AS caches WHERE wp_oc IN (SELECT wp FROM gk_item_waypoint WHERE id IN (SELECT id FROM gk_item WHERE stateid<>1 AND stateid<>4 AND typeid<>2)) OR (wp_gc IN (SELECT wp FROM gk_item_waypoint WHERE id IN (SELECT id FROM gk_item WHERE stateid<>1 AND stateid<> 4 AND typeid<>2)) AND wp_gc <> '') OR (wp_nc IN (SELECT wp FROM gk_item_waypoint WHERE id IN (SELECT id FROM gk_item WHERE stateid<>1 AND stateid<>4 AND typeid<>2)) AND wp_nc <> '')) ");
		filter_by_type_string2 = strdup(" AND caches.cache_id IN (SELECT cache_id FROM foreign_caches AS caches WHERE wp_oc IN (SELECT wp FROM gk_item_waypoint WHERE id IN (SELECT id FROM gk_item WHERE stateid<>1 AND stateid<>4 AND typeid<>2)) OR (wp_gc IN (SELECT wp FROM gk_item_waypoint WHERE id IN (SELECT id FROM gk_item WHERE stateid<>1 AND stateid<> 4 AND typeid<>2)) AND wp_gc <> '') OR (wp_nc IN (SELECT wp FROM gk_item_waypoint WHERE id IN (SELECT id FROM gk_item WHERE stateid<>1 AND stateid<>4 AND typeid<>2)) AND wp_nc <> '')) ");
	}
	else {
		filter_by_type_string = strdup("");
		filter_by_type_string2 = strdup("");
	}

	char query[2][4096];
	snprintf(query[0], sizeof(query[0]), "SELECT %s caches.cache_id, caches.name, caches.wp_oc as wp, caches.votes, caches.score, caches.latitude, caches.longitude, caches.type, caches.status as status, datediff(now(), caches.date_hidden) as old, caches.user_id, IF(%s, 1, 0) as found "
			 "FROM caches AS caches "
			 "%s "
			 "WHERE ( caches.latitude BETWEEN %lf AND %lf ) AND ( caches.longitude BETWEEN %lf AND %lf ) %s",
			 h_sel_ignored, own_not_attempt, h_ignored,
			 (rect.lat-rect.latHeight*bound),
			 (rect.lat + rect.latHeight + rect.latHeight*bound),
			 (rect.lon - rect.lonWidth*bound),
			 (rect.lon + rect.lonWidth + rect.lonWidth*bound), filter_by_type_string);

	snprintf(query[1], sizeof(query[1]), "SELECT %s caches.cache_id, caches.name, caches.wp_oc as wp, caches.votes, caches.score, caches.latitude, caches.longitude, caches.type, caches.status as status, datediff(now(), caches.date_hidden) as old, caches.user_id, IF(%s, 1, 0) as found "
			 "FROM foreign_caches AS caches "
			 "%s "
			 "WHERE ( caches.latitude BETWEEN %lf AND %lf ) AND ( caches.longitude BETWEEN %lf AND %lf ) %s",
			 h_sel_ignored, own_not_attempt, h_ignored,
			 (rect.lat-rect.latHeight*bound),
			 (rect.lat + rect.latHeight + rect.latHeight*bound),
			 (rect.lon - rect.lonWidth*bound),
			 (rect.lon + rect.lonWidth + rect.lonWidth*bound), filter_by_type_string2);

	double min_score = microcgi_getdouble(CGI_GET, "min_score");
	if(microcgi_getstr(CGI_GET, "min_score")[0] == 0)
		min_score = -3;
	double max_score = microcgi_getdouble(CGI_GET, "max_score");
	if(microcgi_getstr(CGI_GET, "max_score")[0] == 0)
		max_score = 3;

	MYSQL_RES *res;
	MYSQL_ROW row;
         
	SDL_Surface *cacheimgs[CACHE_TYPES_NUM];
	bzero(cacheimgs, sizeof(cacheimgs));
	SDL_Surface *redflagimg = NULL;
	SDL_Surface *foundimg = NULL;
	SDL_Surface *archivedimg = NULL;
	SDL_Surface* markerimg = NULL;
	SDL_Surface* markerimgfound = NULL;
	SDL_Surface* markerimgnew = NULL;
	SDL_Surface* markerimgown = NULL;

#ifndef WITH_FASTCGI
	for(int i = 0;i < CACHE_TYPES_NUM;++i) {
		snprintf(buf, sizeof(buf), "%s/%s%i.png", DATA_PATH, type2name(i), zoom);
		cacheimgs[i] = IMG_Load(buf);
		if(!cacheimgs[i])
			cacheimgs[i] = IMG_Load(DATA_PATH"/traditional.png");
	}
	snprintf(buf, sizeof(buf), "%s/redflagmap%i.png", DATA_PATH, zoom);
	redflagimg = IMG_Load(buf);
	snprintf(buf, sizeof(buf), "%s/foundmap%i.png", DATA_PATH, zoom);
	foundimg = IMG_Load(buf);
	snprintf(buf, sizeof(buf), "%s/archivedmap%i.png", DATA_PATH, zoom);
	archivedimg = IMG_Load(buf);
	snprintf(buf, sizeof(buf), "%s/marker%i.png", DATA_PATH, zoom);
    markerimg = IMG_Load(buf);
	snprintf(buf, sizeof(buf), "%s/markerfound%i.png", DATA_PATH, zoom);
    markerimgfound = IMG_Load(buf);
	snprintf(buf, sizeof(buf), "%s/markernew%i.png", DATA_PATH, zoom);
    markerimgnew = IMG_Load(buf);
	snprintf(buf, sizeof(buf), "%s/markerown%i.png", DATA_PATH, zoom);
    markerimgown = IMG_Load(buf);

#else
	for(int i = 0;i < CACHE_TYPES_NUM;++i) {
		cacheimgs[i] = fcgi_cacheimgs[zoom][i];
	}
	redflagimg = fcgi_redflagimg[zoom];
	foundimg = fcgi_foundimg[zoom];
	archivedimg = fcgi_archivedimg[zoom];
	markerimg = fcgi_markerimg[zoom];
	markerimgown = fcgi_markerownimg[zoom];
	markerimgfound = fcgi_markerfoundimg[zoom];
	markerimgnew = fcgi_markernewimg[zoom];
#endif

	int hide_unknown = !(strcmp(microcgi_getstr(CGI_GET, "h_u"), "true"));
	int hide_traditional = !(strcmp(microcgi_getstr(CGI_GET, "h_t"), "true"));
	int hide_multi = !(strcmp(microcgi_getstr(CGI_GET, "h_m"), "true"));
	int hide_virtual = !(strcmp(microcgi_getstr(CGI_GET, "h_v"), "true"));
	int hide_webcam = !(strcmp(microcgi_getstr(CGI_GET, "h_w"), "true"));
	int hide_event = !(strcmp(microcgi_getstr(CGI_GET, "h_e"), "true"));
	int hide_quiz = !(strcmp(microcgi_getstr(CGI_GET, "h_q"), "true"));
	int hide_mobile = !(strcmp(microcgi_getstr(CGI_GET, "h_o"), "true"));
	int hide_ignored = !(strcmp(microcgi_getstr(CGI_GET, "h_ignored"), "true"));
	int hide_own = !(strcmp(microcgi_getstr(CGI_GET, "h_own"), "true"));
	int hide_found = !(strcmp(microcgi_getstr(CGI_GET, "h_found"), "true"));
	int be_ftf = !(strcmp(microcgi_getstr(CGI_GET, "be_ftf"), "true"));
	int hide_available = !(strcmp(microcgi_getstr(CGI_GET, "h_avail"), "true"));
	int hide_temp_unavailable = !(strcmp(microcgi_getstr(CGI_GET, "h_temp_unavail"), "true"));
	int hide_archived = !(strcmp(microcgi_getstr(CGI_GET, "h_arch"), "true"));	
	int hide_noattempt = !(strcmp(microcgi_getstr(CGI_GET, "h_noattempt"), "true"));
	int hide_pl = (strcmp(microcgi_getstr(CGI_GET, "h_pl"), "true"));
	int hide_de = (strcmp(microcgi_getstr(CGI_GET, "h_de"), "true"));
	int hide_noscore = !(strcmp(microcgi_getstr(CGI_GET, "h_noscore"), "false"));
	int mapid = microcgi_getint(CGI_GET, "mapid");

	for(int i = 0;i < 2;++i) {
		if(i == 0 && hide_pl)
			continue;
		if(i == 1 && hide_de)
			continue;

		/* send SQL query */
		if (mysql_query(conn, query[i])) {
			if(mysql_errno(conn) == CR_SERVER_GONE_ERROR) {
				if (!mysql_real_connect(conn, server,
								user, password, database, port, NULL, 0)) {
					printf("Content-type: text/plain; charset=utf-8\r\n\r\n");
					fprintf(stdout, "Can't reconnect to MySQL: %s\n", mysql_error(conn));
				}
			}
			else {
					printf("Content-type: text/plain; charset=utf-8\r\n\r\n");
					fprintf(stdout, "%s\n", mysql_error(conn));				
			}
			goto end_of_request;
		}
		res = mysql_use_result(conn);		

		/* output fields 1 and 2 of each row */
		while ((row = mysql_fetch_row(res)) != NULL) {
			int ignored = row[0]?atoi(row[0]):0;
			int cacheid = atoi(row[1]);
			const char *name = row[2];
			const char *wp = row[3];
			int votes = atoi(row[4]);
			double score = atof(row[5]);
			double latitude = atof(row[6]);
			double longitude = atof(row[7]);
			int type = atoi(row[8]);
			if(type < 0 || type > 10)
				type = 0;
			int status = atoi(row[9]);
			unsigned int old = atoi(row[10]);
			int cache_userid = atoi(row[11]);
			int found = atoi(row[12]);

			if( (hide_unknown && type == 1) || // hide unknown caches
				(hide_traditional && type == 2) || // hide traditional caches
				(hide_multi && type == 3) || // hide multi caches
				(hide_virtual && type == 4) || // hide virtual caches
				(hide_webcam && type == 5) || // hide events
				(hide_event && type == 6) || // hide events
				(hide_quiz && type == 7) || // hide quiz caches
				(hide_mobile && type == 8) || // hide mobile caches
				(hide_ignored && ignored) || // hide ignored caches
				(hide_own && cache_userid == userid) || // hide own caches
				(hide_found && found) || // hide found caches
				(be_ftf && (found >= 1 || status != 1 || cache_userid == userid)) || // be first to find the cache!
				(hide_available && status == 1) || // hide all available caches
				(hide_temp_unavailable && status == 2) || // hide all temporary unavailable caches
				(hide_archived && status == 3) || // hide archived caches
				(hide_noattempt && !found) || // hide caches not yet fond
				( (score <= min_score || score >= max_score) && votes >= 3 && i == 0) || // hide caches not matching score criteria
				(votes < 3 && hide_noscore && i == 0) || // hide caches without definite score
				status > 3 ||
				0
				) // hide caches blocked by RR
				continue;

					   
			int orig_x, orig_y;
			latlon_to_pix(latitude, longitude, rect, &orig_x, &orig_y);		

			int x = orig_x, y = orig_y;
			

			int xpoint_offset = markerimg->w/2;
			int ypoint_offset = 1*markerimg->h/10;

			SDL_Rect r = (SDL_Rect){x - xpoint_offset, y - markerimg->h + ypoint_offset};

			x = r.x + markerimg->w/2;
			y = r.y + 4*markerimg->h/10;

			if(cache_userid == userid) {
				if(markerimgown)
					SDL_gfxBlitRGBA(markerimgown, NULL, im, &r);
			}
			else if(foundimg && found) {
				if(markerimgfound)
					SDL_gfxBlitRGBA(markerimgfound, NULL, im, &r);
			}
			else if(old < 10) {
				if(markerimgnew)
					SDL_gfxBlitRGBA(markerimgnew, NULL, im, &r);
			}
			else {
				if(markerimg && font)
					SDL_gfxBlitRGBA(markerimg, NULL, im, &r);
			}


			if(cacheimgs[type]) {

				if(status == 3 || status == 2) {
					;
				}

				SDL_Rect r = (SDL_Rect){x-cacheimgs[type]->w/2,y-cacheimgs[type]->h/2,0,0};
				SDL_gfxBlitRGBA(cacheimgs[type], NULL, im, &r);

				if(status == 3 || status == 2) {
					;
				}				
			}

			if(foundimg && found) {
				SDL_Rect r = (SDL_Rect){x-foundimg->w/2,y-foundimg->h/2,0,0};
				SDL_gfxBlitRGBA(foundimg, NULL, im, &r);
			}
			if(archivedimg && status == 3) {
				SDL_Rect r = (SDL_Rect){x-archivedimg->w/2,y-archivedimg->h/2,0,0};
				SDL_gfxBlitRGBA(archivedimg, NULL, im, &r);
			}
			else if(redflagimg && status == 2) {
				SDL_Rect r = (SDL_Rect){x-redflagimg->w/2,y-redflagimg->h/2,0,0};
				SDL_gfxBlitRGBA(redflagimg, NULL, im, &r);
			}

			if(font && show_signs && zoom > 13) {
				SDL_Color fgcolor = {30, 30, 30};
				SDL_Color bgcolor = {255, 255, 255};
				if(mapid == 1 || mapid == 2) {
					fgcolor = (SDL_Color){255, 255, 255};
					bgcolor = (SDL_Color){30, 30, 30};
				}

				SDL_Surface* fglabel = TTF_RenderUTF8_Blended(font, name, fgcolor);
				SDL_Surface* bglabel = TTF_RenderUTF8_Blended(font, name, bgcolor);
				SDL_Rect r = (SDL_Rect){orig_x - fglabel->w/2, orig_y + fglabel->h/2};
				

				SDL_Rect r2;
				r2 = r;
				r2.x = r.x - 1;
				SDL_gfxBlitRGBA(bglabel, NULL, im, &r2);
				r2 = r;
				r2.x = r.x + 1;
				SDL_gfxBlitRGBA(bglabel, NULL, im, &r2);
				r2 = r;
				r2.y = r.y + 1;
				SDL_gfxBlitRGBA(bglabel, NULL, im, &r2);
				r2 = r;
				r2.y = r.y - 1;
				SDL_gfxBlitRGBA(bglabel, NULL, im, &r2);

				SDL_gfxBlitRGBA(fglabel, NULL, im, &r);
				SDL_FreeSurface(fglabel);
			}

		}
		mysql_free_result(res);
	}
	printf("Content-type: image/png\r\n\r\n");

	print_png(stdout, im, 5);

end_of_request:;

#ifndef WITH_FASTCGI
	for(int i = 0;i< CACHE_TYPES_NUM;++i)
		SDL_FreeSurface(cacheimgs[i]);
	SDL_FreeSurface(redflagimg);
	SDL_FreeSurface(foundimg);
	SDL_FreeSurface(archivedimg);
	SDL_FreeSurface(markerimg);
	SDL_FreeSurface(markerimgown);
	SDL_FreeSurface(markerimgfound);
	SDL_FreeSurface(markerimgnew);
#endif
	SDL_FreeSurface(im);

	if(h_sel_ignored)
		free(h_sel_ignored);
	if(h_ignored)
		free(h_ignored);
	if(own_not_attempt)
		free(own_not_attempt);
	if(filter_by_type_string)
		free(filter_by_type_string);
	if(filter_by_type_string2)
		free(filter_by_type_string2);

	microcgi_cleanup();	

	_LOOPEND;

	TTF_CloseFont(font);

#ifdef WITH_FASTCGI
	for(int z = 0;z < 20;++z) {
		for(int i = 0;i < CACHE_TYPES_NUM;++i)
			SDL_FreeSurface(fcgi_cacheimgs[z][i]);
		SDL_FreeSurface(fcgi_redflagimg[z]);
		SDL_FreeSurface(fcgi_foundimg[z]);
		SDL_FreeSurface(fcgi_archivedimg[z]);
		SDL_FreeSurface(fcgi_markerimg[z]);
		SDL_FreeSurface(fcgi_markernewimg[z]);
		SDL_FreeSurface(fcgi_markerfoundimg[z]);
		SDL_FreeSurface(fcgi_markerownimg[z]);
	}
#endif

	/* Release memory used to store results and close connection */
	mysql_close(conn);
	mysql_library_end();

	config_free(conf);

	return 0;
}
