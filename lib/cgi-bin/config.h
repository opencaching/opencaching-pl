/*
  Licensed under dual BSD/GPL license.
  Author: "Damian Kaczmarek" <rush@rushbase.net>

  See respective license files in the main directory of the project.
*/

#ifndef CONFIG_H
#define CONFIG_H

struct hashtable;

typedef struct {
    char* filename;
    struct hashtable* h;
} config_handle;

config_handle* config_load(const char* filename);
void config_free(config_handle* handle);
int config_save(config_handle* handle);
const char* config_get(config_handle* h, const char* section, const char* variable, const char* default_value);
int config_set(config_handle* h, const char* section, const char* variable, const char* value);
int config_get_int(config_handle* h, const char* section, const char* variable, int default_value);
int config_set_int(config_handle* h, const char* section, const char* variable, int value);
float config_get_float(config_handle* h, const char* section, const char* variable, float default_value);
int config_set_float(config_handle* h, const char* section, const char* variable, float value);

#endif
