<?php
/**
 * This is pagination model (part of a pagination chunk).
 * To use it:
 * - create model object
 * - load pagination chunk to the view
 * - (optionally) set number of records which needs to be share on pages by
 * - (optionally) set the name of the variable set in links to request pages
 */

namespace src\Models\ChunkModels;

use src\Models\ChunkModels\Pagination\PageModel;
use src\Utils\Uri\Uri;

class PaginationModel
{
    public const DEFAULT_PAGE_PARAM_NAME = '_page';

    public const DEFAULT_STEP_VALUE = 10;

    public const DEFAULT_PAGES_LIST_SIZE = 10;

    private string $pageParamName = self::DEFAULT_PAGE_PARAM_NAME;

    // current page of results
    private int $currentPage;

    // number of records per page
    private int $recordsPerPage;

    // length of pages list
    private int $pagesListSize = self::DEFAULT_PAGES_LIST_SIZE;

    // pages over this count will not be seen
    private ?int $recordsCount = null;

    // if error occurred
    private bool $errorOccurred = false;

    // error message to display
    private string $errorMsg = '';

    public function __construct(int $recordsPerPage = null)
    {
        $this->loadPaginationUrlParams();

        $this->recordsPerPage = $recordsPerPage ?: self::DEFAULT_STEP_VALUE;
    }

    public function getQueryLimitAndOffset(): array
    {
        return [$this->getRecordsPerPageNum(), $this->getQueryOffset()];
    }

    /**
     * Returns value to use as a LIMIT offset value in SQL query
     * to generate current page
     */
    private function getQueryOffset(): int
    {
        //calculate current Limit
        return ($this->currentPage - 1) * ($this->recordsPerPage);
    }

    /**
     * Returns value to use in SQL LIMIT
     */
    private function getRecordsPerPageNum(): int
    {
        return $this->recordsPerPage;
    }

    /**
     * Set the number of pages which should be seen as numbers at the list
     */
    public function setPagesListSize(int $size)
    {
        $this->pagesListSize = $size;
    }

    /**
     * Set the number of all records which needs to be share on pages
     * It allows enabling "go-to-last page" marker.
     */
    public function setRecordsCount(int $numberOfRecords)
    {
        $this->recordsCount = $numberOfRecords;
    }

    /**
     * Set the name of the param set in URLw
     */
    public function setGetParamName(string $pageParam)
    {
        $this->pageParamName = $pageParam;
    }

    /**
     * Return true if pagination is broken (by misconfiguration or improper values of arguments)
     */
    public function error(): bool
    {
        return $this->errorOccurred;
    }

    /**
     * Returns error for display.
     * @return string - error message
     */
    public function getErrorMsg(): string
    {
        return $this->errorMsg;
    }

    /**
     * Return list of PageModel objects to use in chunk template
     * It should be use in chunk template only!
     *
     * @return PageModel[]
     */
    public function getPagesList(): array
    {
        $result = [];

        //calculate the range of the list
        $leftPage = $this->currentPage - floor($this->pagesListSize / 2);
        $rightPage = $this->currentPage + floor($this->pagesListSize / 2);

        $lastPage = null;

        if (! is_null($this->recordsCount)) {
            $lastPage = ceil($this->recordsCount / $this->recordsPerPage);
        }

        if ($this->pagesListSize % 2 == 0) {
            // take one element from left
            $leftPage++;
        }

        if ($leftPage <= 0) {
            $rightPage += 1 - $leftPage;
            $leftPage = 1;
        }

        // add "left markers" on the list
        if ($leftPage > 1) {
            $destPage = $this->currentPage - 1;

            if ($destPage <= 0) {
                $destPage = 1;
            }
            // "<<" mark
            $result[]
                = new PageModel('&lt;&lt;', false, $this->getLink(1), tr('pagination_first'));

            // "<" mark
            $result[]
                = new PageModel('&lt;', false, $this->getLink($destPage), tr('pagination_left'));
        }

        // generate pages marks
        for ($i = $leftPage; $i <= $rightPage; $i++) {
            if (! is_null($lastPage) && $i > $lastPage) {
                // last page found
                break;
            }
            $page = new PageModel(
                $i,
                ($i == $this->currentPage),
                $this->getLink($i),
                tr('pagination_page') . " {$i}."
            );
            $result[] = $page;
        }

        // calculate page number under '>' marker
        $destPage = $this->currentPage + 1;

        if (! is_null($lastPage)) {
            if ($lastPage > $rightPage) {
                // add "right marker" - ">"
                if ($destPage > $lastPage) {
                    $destPage = $lastPage;
                }

                // ">" mark
                $result[]
                    = new PageModel('&gt;', false, $this->getLink($destPage), tr('pagination_right'));

                // ">" mark
                $result[]
                    = new PageModel('&gt;&gt;', false, $this->getLink($lastPage), tr('pagination_last'));
            }
        } else {
            // ">" mark
            $result[]
                = new PageModel('&gt;', false, $this->getLink($destPage), tr('pagination_right'));
        }

        return $result;
    }

    private function getLink(int $pageNum): string
    {
        return Uri::setOrReplaceParamValue($this->pageParamName, $pageNum);
    }

    private function loadPaginationUrlParams()
    {
        $page = $_GET[$this->pageParamName] ?? 1;

        if (! is_numeric($page) || $page < 1) {
            $this->errorOccurred = true;
            $this->errorMsg = 'Improper page param value!';
            $this->currentPage = 1;
        } else {
            $this->currentPage = intval($page);
        }
    }
}
