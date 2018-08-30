<?php
namespace Utils\Feed;

/**
 * This structure stores data about one Atom feed entry
 * Mandatory fields: ID, title, link, author->name
 */

class AtomFeedEntry
{

    /** @var string */
    private $title;

    /** @var string */
    private $link;

    /** @var string */
    private $id;

    /** @var \DateTime */
    private $updated;

    /** @var \DateTime */
    private $published;

    /** @var string */
    private $summary;

    /** @var string */
    private $content;

    /** @var AtomFeedAuthor */
    private $author;

    public function __construct()
    {
        // Set default values
        $this->updated = new \DateTime();
    }

    /**
     * Returns entry title
     *
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Returns entry link
     *
     * @return string
     */
    public function getLink()
    {
        return $this->link;
    }

    /**
     * Returns entry ID
     *
     * @return string
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Returns entry updated date
     *
     * @return \DateTime
     */
    public function getUpdated()
    {
        return $this->updated;
    }

    /**
     * Returns entry published date
     *
     * @return \DateTime
     */
    public function getPublished()
    {
        return $this->published;
    }

    /**
     * Returns entry summary field
     *
     * @return string
     */
    public function getSummary()
    {
        return $this->summary;
    }

    /**
     * Returns entry content field
     *
     * @return string
     */
    public function getContent()
    {
        return $this->content;
    }

    /**
     * Returns entry author object
     *
     * @return \Utils\Feed\AtomFeedAuthor
     */
    public function getAuthor()
    {
        return $this->author;
    }

    /**
     * Sets entry title
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
     * Sets entry link
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
     * Sets entry ID
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
     * Sets entry updated date
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
     * Sets entry published date
     *
     * @param \DateTime $published
     * @return \DateTime
     */
    public function setPublished(\DateTime $published)
    {
        $this->published = $published;
        return $this->getPublished();
    }

    /**
     * Sets entry summary field
     *
     * @param string $summary
     * @return string
     */
    public function setSummary($summary)
    {
        $this->summary = $summary;
        return $this->getSummary();
    }

    /**
     * Sets entry content field
     *
     * @param string $content
     * @return string
     */
    public function setContent($content)
    {
        $this->content = $content;
        return $this->getContent();
    }

    /**
     * Sets author of the entry
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
     * Returns complete <entry> section.
     * If there is not enought data - returns empty string
     *
     * @return string
     */
    public function getFormatedContent()
    {
        if (! $this->isValid()) {
            return '';
        }

        $result = '<entry>' . PHP_EOL;
        $result .= '<title>' . htmlspecialchars($this->getTitle()) . '</title>' . PHP_EOL;
        if (! empty($this->getPublished())) {
            $result .= '<published>' . $this->getPublished()->format(\DateTime::ATOM) . '</published>' . PHP_EOL;
        }
        $result .= '<updated>' . $this->getUpdated()->format(\DateTime::ATOM) . '</updated>' . PHP_EOL;
        $result .= '<id>' . $this->getId() . '</id>' . PHP_EOL;
        $result .= '<link href="' . $this->getLink() . '" />' . PHP_EOL;
        if (! empty($this->getSummary())) {
            $result .= '<summary type="html">' . htmlspecialchars($this->getSummary()) . '</summary>' . PHP_EOL;
        }
        if (! empty($this->getContent())) {
            $result .= '<content type="html">' . htmlspecialchars($this->getContent()) . '</content>' . PHP_EOL;
        }
        if (! empty($this->getAuthor())) {
            $result .= $this->getAuthor()->getFormatedContent();
        }

        $result .= '</entry>' . PHP_EOL;

        return $result;
    }

    /**
     * Check if object is valid to create <entry> section
     * Fields: id, title, link are mandatory and valid author object
     *
     * @return boolean
     */
    public function isValid()
    {
        if (empty($this->getTitle())) {
            return false;
        }
        if (empty($this->getId())) {
            return false;
        }
        if (empty($this->getLink())) {
            return false;
        }
        if (empty($this->getAuthor()) || ! $this->getAuthor()->isValid()) {
            return false;
        }
        return true;
    }

}