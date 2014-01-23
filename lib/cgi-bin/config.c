/*
  Licensed under dual BSD/GPL license.
  Author: "Damian Kaczmarek" <rush@rushbase.net>

  See respective license files in the main directory of the project.
*/

#include <stdio.h>
#include <string.h>
#include <stdint.h>
#include <stdlib.h>
#include <ctype.h>
#include "config.h"

#include "hashtable_itr.h"

config_handle* config_load(const char* filename)
{
    config_handle* handle = malloc(sizeof(config_handle));
    handle->filename = strdup(filename);
    FILE* f = fopen(filename, "r");
    char buffer[16384];
    const char *section = strdup("");
    struct hashtable* sectionh = NULL;

    // hashtable of hashtables, each for one section
    handle->h = create_hashtable(8, hash_from_string, string_equal_fn);

    sectionh = create_hashtable(8, hash_from_string, string_equal_fn);

    // create also a section hash table for NULL
    hashtable_insert(handle->h, strdup(""), sectionh);


    while(fgets(buffer, sizeof(buffer), f)) {
        char* assign;
        if((assign = strchr(buffer, '=')) && assign != buffer) {
            char* valbegin = assign+1;

            char* varend = assign--;

            while(assign > buffer && isspace(*assign)) //cut any trailing whitespace after var name
                assign--;
            *(assign+1) = 0;
            char* varbegin = buffer;
            while(*varbegin && isspace(*varbegin))
                varbegin++;
            if(varend < varbegin)// sanity check
                continue;
            assign++;
            while(*valbegin && isspace(*valbegin))
                valbegin++;

            char *valend;
            char* comment = strstr(valbegin, "//");
            if(comment) {
                *comment = 0;
                valend = comment-1;
            }
            else
                valend = valbegin + strlen(valbegin) - 1; // remove trailing spaces from value
            while(valend > valbegin && (isspace(*valend)))
                *(valend--) = 0;

            char* value = strdup(valbegin); // duplicate value, one char after '='

            hashtable_insert(sectionh, strdup(varbegin), value);
        }
        else {
            char* linebegin = buffer;
            while(*linebegin && isspace(*linebegin))
                linebegin++;
            if(*linebegin != '[') // not a config section
                continue;
            linebegin++;
            char* sectionend = strchr(linebegin, ']');
            if(!sectionend)
                continue;
            *sectionend = 0;
            free((void*)section);
            section = strdup(linebegin);

            // create new only if not found, also if found, set the current pointer
            // to the existing section hashtable
            if(!(sectionh = hashtable_search(handle->h, (void*)section))) {
                sectionh = create_hashtable(8, hash_from_string, string_equal_fn);
                hashtable_insert(handle->h, strdup(section), sectionh);
            }
        }
    }
    if(section)
        free((void*)section);
    fclose(f);
    return handle;
}

int config_save(config_handle* handle)
{
    if(!handle)
        return 1;
    FILE* f = fopen(handle->filename, "w");
    if(!f)
        return 2;

    int sectioncount = 0;
    struct hashtable_itr*itr = hashtable_count(handle->h)?hashtable_iterator(handle->h):NULL;
    if(itr) {
        do {
            char* key = hashtable_iterator_key(itr);
            if(*key != 0) {
                if(sectioncount++ != 0)
                    fprintf(f, "\n");
                fprintf(f, "[%s]\n", key?key:"NULL");
            }
            struct hashtable *h2 = hashtable_iterator_value(itr);

            struct hashtable_itr*itr2 = hashtable_count(h2)?hashtable_iterator(h2):NULL;
            if(itr2) {
                do {
                    fprintf(f, "%s=%s\n", hashtable_iterator_key(itr2), hashtable_iterator_value(itr2));
                }while(hashtable_iterator_advance(itr2));
                free(itr2);
            }

        } while(hashtable_iterator_advance(itr));
        free(itr);
    }
    fclose(f);
    return 0;
}

void config_free(config_handle* handle)
{
    if(!handle)
        return;

    struct hashtable_itr*itr = hashtable_count(handle->h)?hashtable_iterator(handle->h):NULL;
    if(itr) {
        do {
            char* key = hashtable_iterator_key(itr);

            struct hashtable* h2 = hashtable_iterator_value(itr);
            struct hashtable_itr*itr2 = hashtable_count(h2)?hashtable_iterator(h2):NULL;
            if(itr2) {
                do {
                    free(hashtable_iterator_value(itr2));
                }while(hashtable_iterator_remove(itr2));
                free(itr2);
            }
            hashtable_destroy(hashtable_iterator_value(itr), 0, 1);

        } while(hashtable_iterator_remove(itr));
        free(itr);
    }
    hashtable_destroy(handle->h, 0, 1);

    free(handle->filename);
    free(handle);
}

const char* config_get(config_handle* h, const char* section, const char* variable, const char* default_value)
{
    struct hashtable* sectionh = hashtable_search(h->h, (void*)section);
    if(!sectionh)
        return default_value;
    char* val = (char*)hashtable_search(sectionh, (void*)variable);
    if(!val)
        return default_value;
    return val;
}

int config_set(config_handle* h, const char* section, const char* variable, const char* value)
{
    struct hashtable* sectionh = hashtable_search(h->h, (void*)section);
    if(!sectionh)
        return 2;

    struct hashtable_itr* itr = hashtable_iterator(sectionh);
    if(!itr)
        return 3;

    if(!hashtable_iterator_search(itr, sectionh, (void*)variable)) {
        free(itr);
        return 1;
    }
    hashtable_iterator_new_value(itr, strdup(value), 1);
    return 0;
}

int config_get_int(config_handle* h, const char* section, const char* variable, int default_value)
{
    const char* str = config_get(h, section, variable, NULL);
    if(!str)
        return default_value;
    int val;
    sscanf(str, "%i", &val);
    return val;
}

int config_set_int(config_handle* h, const char* section, const char* variable, int value)
{
    char buf[32];
    snprintf(buf, 32, "%i", value);
    return config_set(h, section, variable, buf);
}

float config_get_float(config_handle* h, const char* section, const char* variable, float default_value)
{
    const char* str = config_get(h, section, variable, NULL);
    if(!str)
        return default_value;
    double val = default_value;

    if(sscanf(str, "%lf", &val)) {
        if(strchr(str, '%')) // regard percents as float
            val /= 100;
    }
    else
        return default_value;
    return (float)val;
}

int config_set_float(config_handle* h, const char* section, const char* variable, float value)
{
    char buf[32];
    snprintf(buf, 32, "%f", value);
    return config_set(h, section, variable, buf);
}
