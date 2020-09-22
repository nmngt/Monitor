<?php
namespace App\Checks;

use NGT\Monitor\Checks\CheckBase;
use NGT\Monitor\Checks\Exceptions\HeaderNotFoundException;

class LinkRelCanonical extends CheckBase {

    public $name = 'Link-rel-canonical';

    protected $errorMessage = 'The page is missing a <link rel="canonical"> tag or header.';

    protected $successMessage = 'The page has a <link rel="canonical"> tag or header.';

    protected $returnValue;

    public function validate() : bool
    {
        $canonicals = ['header' => ''];

        try {
            $linkHeader = $this->getResponseHeader('Link');
            foreach($linkHeader as $header) {
                if(strpos($header, 'rel=canonical') !== false || strpos($header, 'rel="canonical"') !== false) {
                    $canonicals['header'][] = $header;
                }
            }
        }
        catch(HeaderNotFoundException $e) {}

        $canonicals['body'] = $this->getDomElementFromBodyByXpath('/html/head/link[@rel="canonical"]/ @href');

        $this->returnValue = $canonicals;

        return $this->validateCanonicals($canonicals);
    }

    /**
     * @param array $canonicals
     * @return bool
     */
    private function validateCanonicals($canonicals) : bool
    {

        if(empty($canonicals['header']) && empty($canonicals['body'])) {
            return false;
        }

        if(! empty($canonicals['header']) && ! empty($canonicals['body'])) {
            $this->errorMessage = 'There are canonical HTTP-header tags as well as <link>-Tags in the page body.';
            return false;
        }

        if(! empty($canonicals['body'])) {
            return $this->validateBody($canonicals['body']);
        }
        if(! empty($canonicals['header'])) {
            return $this->validateHeader($canonicals['header']);
        }

        return true;
    }

    private function validateBody($canonical) : bool
    {
        if(strlen($canonical[0]['href']) == 0) {
            $this->errorMessage = 'The rel-canonical tag is set, but points to an empty path';

            return false;
        }
        if(sizeof($canonical) > 1) {
            $this->errorMessage = 'There are multiple <link rel="canonical"> tags on this page.';
            return false;
        }
        return true;
    }

    /**
     * @param array $canonical
     * @return bool
     */
    private function validateHeader($canonical) : bool
    {
        if(sizeof($canonical) > 1) {
            $this->errorMessage = 'There are multiple "Link: ... rel=canonical" headers on this page.';
            return false;
        }

        preg_match('~<(.*)>~', $canonical[0], $canonicalUrl);

        if(strlen($canonicalUrl[1]) == 0) {
            $this->errorMessage = 'The rel-canonical header is set, but points to an empty path.';
            return false;
        }

        if(strpos($canonical[0], '<') === false) {
            $this->errorMessage = 'The rel-canonical header seems to be malformed.';
            return false;
        }

        return true;
    }
}
