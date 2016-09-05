<?php
/**
 * This is pagintaion model (part of a pagination chunk).
 * To use it:
 * - create model object
 * - load pagination chunk to the view
 * - (optionally) set number of records which which needs to be share on pages by
 * - (optionally) set the name of the variable set in links to request pages
 *
 */
namespace lib\Objects\ChunkModels;

use Utils\Uri\Uri;

class PaginationModel {

    const DEFAULT_PAGE_PARAM_NAME   = '_page';
    const DEFAULT_STEP_VALUE        = 10;
    const DEFAULT_PAGES_LIST_SIZE   = 10;

    private $pageParamName = self::DEFAULT_PAGE_PARAM_NAME;

    private $currentPage = null;            // current page of results
    private $recordsPerPage = null;         // number of records per page

    private $pagesListSize;                 // length of pages list
    private $recordsCount;                  // pages over this count will not be seen

    private $errorOccured = false;          // if error occured
    private $errorMsg;                      // error message to display


    public function __construct($recordsPerPage=null){

        $this->loadPaginationUrlParams();

        if(!is_null($recordsPerPage)){
            $this->recordsPerPage = $recordsPerPage;
        }else{
            $this->recordsPerPage = self::DEFAULT_STEP_VALUE;
        }

        $this->pagesListSize = self::DEFAULT_PAGES_LIST_SIZE;
    }

    /**
     * Returns value to use as a LIMIT offset value in SQL query
     * to generate current page
     *
     * @return number
     */
    public function getQueryOffset(){
        if( is_null($this->currentPage) || is_null($this->recordsPerPage) ){
            // TODO: hot to handle errors?
            error_log("Pagination model used without initialization!");
            return 0;
        }

        //calculate current Limit
        return ($this->currentPage - 1)*($this->recordsPerPage);

    }

    /**
     * Returns value to use in SQL LIMIT
     *
     * @return number
     */
    public function getRecordsPerPageNum(){
        if( is_null($this->currentPage) || is_null($this->recordsPerPage) ){
            // TODO: hot to handle errors?
            error_log("Pagination model used without initialization!");
            return 0;
        }

        return $this->recordsPerPage;
    }


    /**
     * Set the number of pages which should be seen as numbers at the list
     * @param integer $size
     */
    public function setPagesListSize($size){
        $this->pagesListSize = $size;
    }

    /**
     * Set the number of all records which needs to be share on pages
     * It allow to enable "go-to-last page" marker.
     *
     * @param integer $numberOfRecords
     */
    public function setRecordsCount($numberOfRecords){
        $this->recordsCount = intval($numberOfRecords);
    }

    /**
     * Set the name of the param set in URLw
     * @param String $pageParam
     */
    public function setGetParamName($pageParam){
        $this->pageParamName = $pageParam;
    }


    /**
     * Return true if pagination is broken (by misconfguration or improper values of arguments)
     * @return boolean
     */
    public function error(){
        return $this->errorOccured;
    }

    /**
     * Returns error for display.
     * @return string - error message
     */
    public function getErrorMsg(){
        return $this->errorMsg;
    }

    /**
     * Return list of PageModel objects to use in chunk template
     * It should be use in chunk template only!
     */
    public function getPagesList(){
        $result = [];


        //calculate the range of the list
        $leftPage = $this->currentPage - floor($this->pagesListSize/2);
        $rightPage = $this->currentPage + floor($this->pagesListSize/2);

        $lastPage = null;
        if(!is_null($this->recordsCount)){
            $lastPage = ceil($this->recordsCount / $this->recordsPerPage);
        }

        if($this->pagesListSize % 2==0){
            // take one element from left
            $leftPage++;
        }

        if($leftPage <= 0){
            $rightPage += 1 - $leftPage;
            $leftPage = 1;
        }


        // add "left markers" on the list
        if($leftPage > 1){

            $destPage = $this->currentPage - $this->pagesListSize;
            if($destPage<0){
                $destPage=1;
            }
            // "<<" mark
            $result[] =
            new PageModel( "&lt;&lt;", false, $this->getLink(1), tr('pagination_first'));

            // "<" mark
            $result[] =
            new PageModel( "&lt;", false, $this->getLink($destPage), tr('pagination_left'));
        }

        // generate pages marks
        for($i = $leftPage; $i <= $rightPage; $i++){
            if( !is_null($lastPage) && $i > $lastPage ){
                // last page found
                break;
            }
            $page = new PageModel(
                $i, ($i==$this->currentPage),
                $this->getLink($i), tr('pagination_page')." $i.");
            $result[] = $page;
        }

        // calculate page number under '>' marker
        $destPage = $this->currentPage + $this->pagesListSize;
        if( !is_null($lastPage) ){

            if($lastPage > $rightPage){

                // add "right marker" - ">"
                if($destPage > $lastPage){
                    $destPage = $lastPage;
                }

                // ">" mark
                $result[] =
                new PageModel( "&gt;",false,$this->getLink($destPage), tr('pagination_right'));

                // ">" mark
                $result[] =
                new PageModel( "&gt;&gt;",false,$this->getLink($lastPage), tr('pagination_last'));

            }

        }else{
            // ">" mark
            $result[] =
            new PageModel( "&gt;",false,$this->getLink($destPage), tr('pagination_right'));
        }


        return $result;
    }

    private function getLink($pageNum){
        return Uri::setOrReplaceParamValue($this->pageParamName, $pageNum);
    }

    private function loadPaginationUrlParams(){

        //look for pagination param
        if(! isset($_GET[$this->pageParamName]) ){
            //no such param - this is initial, first page
            $this->currentPage = 1;

        }else{
            $this->currentPage = $_GET[$this->pageParamName];

            // check if currentPage has proper integer value
            if(!is_numeric($this->currentPage) || $this->currentPage < 1){
                $this->errorOccured = true;
                $this->errorMsg = 'Improper page param value!';
            }else{
                $this->currentPage = intval($this->currentPage);
            }
        }
    }
}

/**
 * This class is a model of single pagination mark
 * This is for pagination chunk internal use only.
 */
class PageModel{

    public $text;
    public $isActive;
    public $link;
    public $tooltip;

    public function __construct($text, $isActive, $link, $tooltip){
        $this->isActive = $isActive;
        $this->link = $link;
        $this->text = $text;
        $this->tooltip = $tooltip;
    }
}
