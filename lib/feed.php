<?php
/**
 * Simple reader for RSS and Atom feeds.
 * Requires: SimpleXML, fopen_wrappers
 * Limitations: Not content encoding support.
 *
 * Usage:
 *     $feed = new Feed('http://www.example.com/feed.rss');
 *
 *     //Get items with next() or current()
 *     echo $feed->next()->title;           // "Blog post 1"
 *     echo $feed->next()->title;           // "Blog post 1"
 *     echo $feed->next()->title;           // "Blog post 2"
 *     echo $feed->current()->title;        // "Blog post 2"
 *
 *     //Feed data returned
 *     echo $feed->current()->title;        // "Blog post 2"
 *     echo $feed->current()->date;         // int(1265569159)
 *     echo $feed->current()->description;  // "Lorem ipsum dolar..."
 *     echo $feed->current()->link;         // "http://www.example.com/blog/2"
 *     echo $feed->current()->image;        // "http://www.example.com/blog/images/2.jpg"
 *     echo $feed->current()->author;       // "deg"
 *
 *     //Get multiple items in single call
 *     foreach ($feed->find(3) as $item) {
 *         echo $item->title;               // "Blog post 3" "Blog post 4" "Blog post 5"
 *     }
 *
 *     //Reset internal counter
 *     echo $feed->reset();
 *     echo $feed->next()->title;           // "Blog post 1"
 *
 *     //Get random items, without repeating
 *     echo $feed->random()->title;         // "Blog post 4"
 *     echo $feed->random()->title;         // "Blog post 3"
 *
 *     //Total number of items
 *     echo $feed->count();                 // int(10)
 */

class Feed {
    private $url;
    private $reader;
    private $current;
    private $remaining;

    public $cacheTime = 3600;

    /**
     * Create Atom reader object.
     *
     * @param string $url
     */
    public function __construct($url) {
        $this->url = $url;
        $this->reset();
    }

    /**
     * Reset current item to first RSS item.
     */
    public function reset() {
        $this->current = -1;
        $this->remaining = null;
    }

    /**
     * Get the next item in the feed.
     *
     * @return stdClass Object representing the item. Will return null when the list is exhausted.
     */
    public function next() {
        if ($this->current < $this->count()) {
            $this->current++;
            $next = $this->getReader()->item($this->current);
            return $next;
        }
    }

    /**
     * Get the current item in the feed.
     *
     * @return stdClass Object representing the item. Will return null when the list is exhausted.
     */
    public function current() {
        return $this->getReader()->item(max(0, $this->current));
    }

    /**
     * Get random item from the feed. Will not return an item more than once.
     *
     * @return stdClass Object representing the item. Will return null when the list is exhausted.
     */
    public function random() {
        if ($this->remaining === null) {
            $this->remaining = array();
            for ($i = 0; $i < $this->count(); $i++) {
                $this->remaining[] = $i;
            }
        }

        if (count($this->remaining)) {
            $picked = array_rand($this->remaining);
            $index = $this->remaining[$picked];
            unset($this->remaining[$picked]);
            return $this->getReader()->item($index);
        }
    }

    /**
     * Get X items from feed. Will advance pointer.
     *
     * @param int $count
     * @return array of stdClass
     */
    public function find($count) {
        $items = array();
        
        while ($item = $this->next()) {
            $items[] = $item;
            if (count($items) >= $count) {
                break;
            }
        }

        return $items;
    }

    /**
     * Get the number of items in the feed.
     *
     * @return int
     */
    public function count() {
        return $this->getReader()->count();
    }

    /**
     * Get FeedReader object for the feed.
     *
     * @return FeedReader
     */
    private function getReader() {
        if (!$this->reader) {
            $xml = $this->getXML();
            if (RSSReader::canRead($xml)) {
                $this->reader = new RSSReader($xml);
            } else if (AtomReader::canRead($xml)) {
                $this->reader = new AtomReader($xml);
            } else {
                $this->reader = new NullReader($xml);
            }
        }
        return $this->reader;
    }

    /**
     * Get XML element for the feed.
     *
     * @return SimpleXMLElement
     */
    private function getXML() {
        if ($xml = $this->getCacheXML()) {
            return $xml;
        } else if ($xml = $this->getURLXML()) {
            return $xml;
        } else {
            return new SimpleXMLElement("");
        }
    }

    /**
     * Get XML element for the feed from cache.
     *
     * @return SimpleXMLElement or null if cache doesn't exist.
     */
    private function getCacheXML() {
        //Store URL data in local cache.
        $cacheFilename = $this->getCacheFilename();
        if (file_exists($cacheFilename) && (time() - filemtime($cacheFilename)) < $this->cacheTime) {
            if ($data = file_get_contents($cacheFilename)) {
                return new SimpleXMLElement($data);
            }
        }
    }

    /**
     * Get XML element from the feed from the live URL.
     * Will cache XML data to disk.
     *
     * @return SimpleXMLElement or null if URL is unreachable.
     */
    private function getURLXML() {
        if ($data = @file_get_contents($this->url)) {
            try {
                $xml = new SimpleXMLElement($data);
                file_put_contents($this->getCacheFilename(), $data);
                return $xml;
            } catch (Exception $e) {
                return null;
            }
        }
    }

    /**
     * Name of the cache file for current URL.
     *
     * @return string
     */
    private function getCacheFilename() {
        return sys_get_temp_dir() . '/' . md5($this->url) . '.feed.cache';
    }
}

/**
 * Interface for reading items from feed.
 */
interface FeedReader {

    /**
     * Create reader from SimpleXMLElement.
     *
     * @param SimpleXMLElement $root
     */
    public function __construct(SimpleXMLElement $root);

    /**
     * Get single node.
     *
     * @return array or null.
     */
    public function item($index);

    /**
     * Get number of items.
     *
     * @return int.
     */
    public function count();

    /**
     * Can this reader understand the XML file?
     *
     * @param SimpleXMLElement $root
     * @return bool
     */
    public static function canRead(SimpleXMLElement $root);

}

/**
 * Concrete implementation of FeedReader that will never return an item.
 */
class NullReader implements FeedReader {

    public function __construct(SimpleXMLElement $root) {
        //Nothing
    }

    public function count() {
        return null;
    }

    public function item($index) {
        return null;
    }

    public static function canRead(SimpleXMLElement $root) {
        return true;
    }
}

/**
 * Concrete implementation of FeedReader that will read an Atom feed.
 */
class AtomReader implements FeedReader {

    private $root;

    public function __construct(SimpleXMLElement $root) {
        $this->root = $root;
    }

    public function count() {
        return count($this->root->entry);
    }

    public function item($index) {
        $node = $this->root->entry[$index];

        if (!$node) {
            return null;
        }

        $item = array(
            'title' => (string)$node->title,
            'description' => (string)$node->description,
            'image' => null,
            'link' => null,
            'date' => strtotime($node->published),
            'author' => (string)$node->author->name,
        );

        //Iterate through link nodes getting content URL and images.
        foreach ($node->link as $link) {
            if (strpos($link['type'], 'text') === 0 || $item['link'] === null) {
                $item['link'] = (string)$link['href'];
            }
            if (strpos($link['type'], 'image') === 0) {
                $item['image'] = (string)$link['href'];
            }
        }

        return (object)$item;
    }

    public static function canRead(SimpleXMLElement $root) {
        //Check for Atom namespace.
        return in_array('http://www.w3.org/2005/Atom', $root->getNamespaces());
    }
}

/**
 * Concrete implementation of FeedReader that will read an RSS feed.
 */
class RSSReader implements FeedReader {

    private $root;

    public function __construct(SimpleXMLElement $root) {
        $this->root = $root;
    }

    public function count() {
        return count($this->root->channel->item);
    }

    public function item($index) {
        $node = $this->root->channel->item[$index];

        if (!$node) {
            return null;
        }
        $dc = $node->children('http://purl.org/dc/elements/1.1/');
        return (object)array(
            'title' => (string)$node->title,
            'description' => (string)$node->description,
            'link' => (string)$node->link,
            'image' => null,
            'date' => strtotime($node->pubDate),
            'author' => (string)$dc->creator,
        );
    }

    public static function canRead(SimpleXMLElement $root) {
        //RSS feeds name their root node 'rss'.
        return $root->getName() == 'rss';
    }
}