<?php
namespace Utils\Feed;

use lib\Objects\OcConfig\OcConfig;
use Utils\Uri\Uri;

/**
 * Class to create complete RSS feed in Atom 1.0 standard
 *
 * Example of use:
 * $rss = new AtomFeed();
 * $rss->setId('https://some.site/feed.xml');
 * $rss->setTitle('Test Feed');
 *
 * $entry = new AtomFeedEntry();
 * $entry->setTitle('This is entry title');
 * $entry->setId('https://some.site/item/1234');
 * $entry->setLink('https://some.site/item/1234');
 * $entry->setAuthor(new AtomFeedAuthor());
 * $entry->getAuthor()->setName('Our best user');
 *
 * $rss->addEntry($entry);
 * $rss->publish();
 *
 * You can add entries as many as you want (before publish())
 */
class AtomFeed
{

    /** @var string */
    private $title;

    /** @var string */
    private $id;

    /** @var \DateTime */
    private $updated;

    /** @var string */
    private $link;

    /** @var string */
    private $icon;

    /** @var string */
    private $logo;

    /** @var AtomFeedAuthor */
    private $author;

    /** @var AtomFeedEntry[] */
    private $entries = [];

    /** @var string[] */
    private $errors = [];

    public function __construct()
    {
        // Set default values
        $this->setUpdated(new \DateTime());
        $this->setLink(Uri::getCurrentUriBase() . Uri::getCurrentUri(true));
        $this->setTitle(OcConfig::getSiteName());
        $this->setIcon(OcConfig::getAbsolute_server_URI() . 'images/oc_logo.png');
    }

    /**
     * Returns feed title
     *
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Returns feed ID
     *
     * @return string
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Returns feed last updated date
     *
     * @return \DateTime
     */
    public function getUpdated()
    {
        return $this->updated;
    }

    /**
     * Returns feed link
     *
     * @return string
     */
    public function getLink()
    {
        return $this->link;
    }

    /**
     * Returns feed icon
     *
     * @return string
     */
    public function getIcon()
    {
        return $this->icon;
    }

    /**
     * Returns feed logo
     *
     * @return string
     */
    public function getLogo()
    {
        return $this->logo;
    }

    /**
     * Returns feed author object
     *
     * @return \Utils\Feed\AtomFeedAuthor
     */
    public function getAuthor()
    {
        return $this->author;
    }

    /**
     * Sets feed title
     *
     * @param string $title
     * @return string
     */
    public function setTitle($title)
    {
        $this->title = $title;
        return $this->getTitle();
    }

    /**
     * Sets feed id
     *
     * @param string $id
     * @return string
     */
    public function setId($id)
    {
        $this->id = $id;
        return $this->getId();
    }

    /**
     * Sets last feed updated date
     *
     * @param \DateTime $updated
     * @return \DateTime
     */
    public function setUpdated(\DateTime $updated)
    {
        $this->updated = $updated;
        return $this->getUpdated();
    }

    /**
     * Sets feed link
     *
     * @param string $link
     * @return string
     */
    public function setLink($link)
    {
        $this->link = $link;
        return $this->getLink();
    }

    /**
     * Sets feed icon
     *
     * @param string $icon
     * @return string
     */
    public function setIcon($icon)
    {
        $this->icon = $icon;
        return $this->getIcon();
    }

    /**
     * Sets feed logo
     *
     * @param string $logo
     * @return string
     */
    public function setLogo($logo)
    {
        $this->logo = $logo;
        return $this->getLogo();
    }

    /**
     * Sets feed author
     *
     * @param AtomFeedAuthor $author
     * @return \Utils\Feed\AtomFeedAuthor
     */
    public function setAuthor(AtomFeedAuthor $author)
    {
        $this->author = $author;
        return $this->getAuthor();
    }

    /**
     * Adds AtomFeedEntry object into the feed
     * You can add many these objects, calling addEntry() multiple times
     *
     * @param AtomFeedEntry $entry
     */
    public function addEntry(AtomFeedEntry $entry)
    {
        $this->entries[] = $entry;
    }

    /**
     * Publishes feed to std output
     *
     * If feed data is valid - feed is published.
     * If is invalid - errors are shown
     */
    public function publish()
    {
        if ($this->isValid()) {
            header('Content-type: application/xml; charset="utf-8"');
            $result = $this->buildFeed();
        } else {
            $result = $this->buildErrorPage();
        }
        echo $result;
    }

    /**
     * Check if feed has enought data to publish valid Atom Feed
     *
     * @return boolean
     */
    public function isValid()
    {
        if (empty($this->getTitle())) {
            $this->addError('ERROR: No feed title');
        }

        if (empty($this->getId())) {
            $this->addError('ERROR: No feed ID');
        }

        if (empty($this->getUpdated())) {
            $this->addError('ERROR: No feed updated date');
        }

        return (! $this->hasErrors());
    }

    /**
     * Adds error into internal error table
     *
     * @param string $error
     */
    private function addError($error)
    {
        $this->errors[] = $error;
    }

    /**
     * Checks if there are any errors in internal table
     *
     * @return boolean
     */
    private function hasErrors()
    {
        return (count($this->errors) > 0);
    }

    /**
     * Returns ready Atom feed string. Used by publish() method
     *
     * @return string
     */
    private function buildFeed()
    {
        $result = '<?xml version="1.0" encoding="utf-8"?>' . PHP_EOL;
        $result .= '<feed xmlns="http://www.w3.org/2005/Atom">' . PHP_EOL;
        $result .= '<title type="text">' . strip_tags($this->getTitle()) . '</title>' . PHP_EOL;
        $result .= '<id>' . $this->getId() . '</id>' . PHP_EOL;
        $result .= '<updated>' . $this->getUpdated()->format(\DateTime::ATOM) . '</updated>' . PHP_EOL;
        $result .= '<link href="' . OcConfig::getAbsolute_server_URI() . '" />' . PHP_EOL;
        if (! empty($this->getLink())) {
            $result .= '<link type="application/atom+xml" rel="self" href="' . $this->getLink() . '" />' . PHP_EOL;
        }
        if (! empty(($this->getIcon()))) {
            $result .= '<icon>' . $this->getIcon() .'</icon>' . PHP_EOL;
        }
        if (! empty(($this->getLogo()))) {
            $result .= '<logo>' . $this->getLogo() . '</logo>' . PHP_EOL;
        }
        if (! empty($this->getAuthor())) {
            $result .= $this->getAuthor()->getFormatedContent();
        }

        foreach ($this->entries as $entry) {
            $result .= $entry->getFormatedContent();
        }

        $result .= '</feed>';

        return $result;
    }

    /**
     * Returns all stored errors.
     * Used by publish() if data for feed is nod valid
     *
     * @return string
     */
    private function buildErrorPage()
    {
        $result = 'Feed problem(s):' . PHP_EOL;
        foreach ($this->errors as $error) {
            $result .= $error . PHP_EOL;
        }
        return $result;
    }
}