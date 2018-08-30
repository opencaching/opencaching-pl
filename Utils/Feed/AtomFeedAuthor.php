<?php
namespace Utils\Feed;

/**
 * This structure stores information about Atom feed or entry author.
 * Field name is mandatory. Others are optional
 */
class AtomFeedAuthor
{

    /** @var string */
    private $name;

    /** @var string */
    private $uri;

    /** @var string */
    private $email;

    public function __construct()
    {}

    /**
     * Returns author name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Returns author profile URI
     *
     * @return string
     */
    public function getUri()
    {
        return $this->uri;
    }

    /**
     * Returns author e-mail address
     *
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * Sets author name
     *
     * @param string $name
     * @return string
     */
    public function setName($name)
    {
        $this->name = $name;
        return $this->getName();
    }

    /**
     * Sets author profile URI
     *
     * @param string $uri
     * @return string
     */
    public function setUri($uri)
    {
        $this->uri = $uri;
        return $this->getUri();
    }

    /**
     * Sets author e-mail address
     *
     * @param string $email
     * @return string
     */
    public function setEmail($email)
    {
        $this->email = $email;
        return $this->getEmail();
    }

    /**
     * Returns complete <author> section.
     * If there is not enought data - returns empty string
     *
     * @return string
     */
    public function getFormatedContent()
    {
        if (! $this->isValid()) {
            return '';
        }
        $result = '<author>' . PHP_EOL;
        $result .= '<name>' . htmlspecialchars($this->getName()) . '</name>' . PHP_EOL;
        if (! empty($this->getUri())) {
            $result .= '<uri>' . $this->getUri() . '</uri>' . PHP_EOL;
        }
        if (! empty($this->getEmail())) {
            $result .= '<email>' . $this->getEmail() . '</email>' . PHP_EOL;
        }
        $result .= '</author>' . PHP_EOL;

        return $result;
    }

    /**
     * Check if object is valid to create <author> section
     * Field name is mandatory
     *
     * @return boolean
     */
    public function isValid()
    {
        if (empty($this->getName())) {
            return false;
        }
        return true;
    }

}