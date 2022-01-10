<?php

namespace src\Models\ChunkModels\Pagination;

/**
 * This class is a model of single pagination mark
 * This is for pagination chunk internal use only.
 */
class PageModel
{
    public string $text;

    public bool $isActive;

    public string $link;

    public string $tooltip;

    public function __construct(string $text, bool $isActive, string $link, string $tooltip)
    {
        $this->isActive = $isActive;
        $this->link = $link;
        $this->text = $text;
        $this->tooltip = $tooltip;
    }
}
