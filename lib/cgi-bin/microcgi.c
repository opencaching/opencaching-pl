/*
  Licensed under dual BSD/GPL license.
  Author: "Damian Kaczmarek" <rush@rushbase.net>

  See respective license files in the main directory of the project.
*/

#ifdef WITH_FASTCGI
#include <fcgi_stdio.h>
#endif
#include "microcgi.h"
#include "hashtable.h"
#include <stdint.h>
#include <stdlib.h>
#include <stdio.h>
#include <string.h>

// len should be at least the length of src
static void unencode(const char *src, size_t len, char *dest)
{
  const char* last = src + len;

  for(; src != last; src++, dest++)
    if(*src == '+')
      *dest = ' ';
    else if(*src == '%' && src + 2 < last) {

      int code;
      if(sscanf(src+1, "%2x", &code) != 1)
    code = '?';
      *dest = code;
      src +=2;
    }
    else
      *dest = *src;
  *dest = '\0';
}


static struct hashtable* getvars;
static struct hashtable* postvars;

static void handle_formdata(formtype type, const char* getquery)
{
  size_t len = strlen(getquery);
  char getquery2[len+1];
  strncpy(getquery2, getquery, len+1);
  char *from = getquery2;
  char *to;

  while((to = strchr(from, '&'))) {
    *to = 0;
    char buf[to-from+1];
    unencode(from, to - from, buf);
    char *assign = strchr(buf, '=');
    if(assign == NULL) {
      microcgi_setstr(type, buf, "");
    }
    else {
      *assign=0;
      assign++;
      microcgi_setstr(type, buf, assign);
    }
    from = to+1;
  }
  to = from + strlen(from);
  char buf[to-from+1];
  unencode(from, to - from, buf);
  char *assign = strchr(buf, '=');
  if(assign == NULL) {
    microcgi_setstr(type, buf, "");
  }
  else {
    *assign=0;
    assign++;
    microcgi_setstr(type, buf, assign);
  }
}


void microcgi_init(void)
{
  getvars = create_hashtable(8, hash_from_string, string_equal_fn);
  postvars = create_hashtable(8, hash_from_string, string_equal_fn);

  const char *getquery = getenv("QUERY_STRING");
  if(getquery) {
    handle_formdata(CGI_GET, getquery);
  }
  getquery = getenv("CONTENT_LENGTH"); // according to specification, data length has to be provided
  if(!getquery)
    return;
  int len = atoi(getquery);
  if(len <= 0)
    return;
  char postquery[len+2];
  fgets(postquery, len, stdin);
  handle_formdata(CGI_POST, postquery);

}

void microcgi_setstr(formtype type, const char* variable, const char* value)
{
    struct hashtable *target = type==CGI_GET?getvars:postvars;

    void * t = hashtable_search(target, (void*)variable);
    if(t) {
        free(hashtable_remove(target, (void*)variable));
    }
    hashtable_insert(getvars, strdup(variable), strdup(value));
}

const char* microcgi_getstr(formtype type, const char* variable)
{
    struct hashtable *target = type == CGI_GET?getvars:postvars;
    const char *str = hashtable_search(target, (void*)variable);
    if(str)
        return str;
    else
        return "";
}

double microcgi_getdouble(formtype type, const char* variable)
{
  const char *found = microcgi_getstr(type, variable);
  if(found) {
    double d;
    sscanf(found, "%lf", &d);
    return d;
  }
  else
    return 0.0l;
}

int microcgi_getint(formtype type, const char* variable)
{
  const char *found = microcgi_getstr(type, variable);
  if(found) {
    int i;
    sscanf(found, "%i", &i);
    return i;
  }
  else
    return 0;
}

void microcgi_cleanup(void)
{
  if(getvars)
    hashtable_destroy(getvars, 1, 1);
  getvars = NULL;
  if(postvars)
    hashtable_destroy(postvars, 1, 1);
  postvars = NULL;
}
