CC=gcc
CFLAGS=-std=c99 -D_BSD_SOURCE -g `sdl-config --cflags` -DWITH_FASTCGI
BINARY=mapper.fcgi
OBJECTS=hashtable.o hashtable_itr.o microcgi.o mapper.o config.o IMG_savepng.o
LDFLAGS=-lmysqlclient -lm `sdl-config --libs` -lpng -lSDL_image -lSDL_gfx -lSDL_ttf -lfcgi

all: clean $(BINARY)

$(BINARY): $(OBJECTS)
	$(CC) $(OBJECTS) -o $(BINARY) $(LDFLAGS)

%.o: %.c
	$(CC) $< -c -o $@ $(CFLAGS)

clean:
	rm -f $(OBJECTS) $(BINARY)