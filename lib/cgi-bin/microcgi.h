/*
  Licensed under dual BSD/GPL license.
  Author: "Damian Kaczmarek" <rush@rushbase.net>

  See respective license files in the main directory of the project.
*/

#ifndef _MICROCGI_H_
#define _MICROCGI_H_

typedef enum { CGI_GET, CGI_POST } formtype;

void microcgi_init();

void microcgi_setstr(formtype type, const char* variable, const char* value);
const char* microcgi_getstr(formtype type, const char* variable);
double microcgi_getdouble(formtype type, const char* variable);
int microcgi_getint(formtype type, const char* variable);

void microcgi_cleanup();


#endif /* _MICROCGI_H_ */
