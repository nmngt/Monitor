<?php
namespace App\Checks;

use NGT\Monitor\Checks\CheckBase;
use NGT\Monitor\Checks\Exceptions\HeaderNotFoundException;

class LinkHrefLang extends CheckBase
{
    public $name = 'Link-HREFlang';

    protected $errorMessage = 'The page is missing a <link rel="alternate" hreflang="..."> tag or header.';
    protected $successMessage = 'The page has a <link rel="alternate" hreflang="..."> tag or header.';
    protected $returnValue = '';

    public function validate()
    {
        return $this->validateHrefLang([
            'header' => $this->getNormalizedHeaderItems(),
            'body' => $this->getNormalizedBodyItems()
        ]);
    }

    /**
     * @param $hrefLangItems
     *
     * @return bool
     */
    private function validateHrefLang($hrefLangItems)
    {
        if($hrefLangItems['header'] === false || $hrefLangItems['body'] === false) {
            return false;
        }

        if(empty($hrefLangItems['header']) && empty($hrefLangItems['body'])) {
            return false;
        }

        if(! empty($hrefLangItems['header']) && ! empty($hrefLangItems['body'])) {
            $this->errorMessage = 'There are hrefLang HTTP-header tags as well as <link rel="alternate" hreflang="...">-Tags in the page body.';
            return false;
        }

        $itemsToTest = $hrefLangItems['body'];
        if(! empty($hrefLangItems['header'])) {
            $itemsToTest = $hrefLangItems['header'];
        }

        return $this->validateHrefLangItems($itemsToTest);
    }

    private function validateHrefLangItems($hrefLangItems)
    {
        $currentUri = $this->client->getUrl();
        $currentUriReferenced = false;
        $foundHrefLangs = [];

        foreach($hrefLangItems as $hrefLangItem)
        {
            $hrefLangValue = $hrefLangItem['hreflang'];
            $hrefLangHref = $hrefLangItem['href'];

            if(!$this->validateHrefLangValue($hrefLangValue)) {
                return false;
            }
            if(!$this->validateUniqueHrefLang($foundHrefLangs, $hrefLangValue)) {
                return false;
            }

            $currentUriReferenced = $currentUriReferenced || ($hrefLangHref == $currentUri);
        }

        if($currentUriReferenced == false) {
            $this->errorMessage = 'The current URL needs to be referenced.';
            return false;
        }
        return true;
    }

    private function validateHrefLangValue($hrefLangValue)
    {
        if(! preg_match( '~^([a-z]{2})(-[A-Za-z][a-z]+)?~', $hrefLangValue ) && 'x-default' != $hrefLangValue)
        {
            $this->errorMessage = 'One "hreflang" attribute has the unexpected value "' . $hrefLangValue . '"';
            return false;
        }
        return true;
    }

    /**
     * @param $foundHrefLangs
     * @param $hrefLangValue
     *
     * @return bool
     */
    private function validateUniqueHrefLang(&$foundHrefLangs, $hrefLangValue)
    {
        if(isset($foundHrefLangs[$hrefLangValue]))
        {
            $this->errorMessage = 'The hreflang value "'.$hrefLangValue.'" is referenced multiple times';
            return false;
        }
        $foundHrefLangs[$hrefLangValue] = $hrefLangValue;
        return true;
    }

    /**
     *
     * @return mixed
     */
    private function getNormalizedHeaderItems()
    {
        try {
            $linkHeader = $this->getResponseHeader('Link', true);

            if(preg_match('~rel=["\']?alternate["\']?~', $linkHeader) > 0 && strpos( $linkHeader, 'hreflang=' ) !== false)
            {
                $normalizedItems = $this->getNormalizedHeaderItem($linkHeader);

                return $normalizedItems !== false ? $normalizedItems : false;
            }
        }
        catch( HeaderNotFoundException $e ) {}

        return [];
    }

    private function getNormalizedHeaderItem($hrefLangItem)
    {
        $hrefLangItems = explode(',', $hrefLangItem);

        $normalizedItems = [];
        foreach($hrefLangItems as $hrefLangItem)
        {
            if(!preg_match( '~<(.*)>~', $hrefLangItem, $hrefLangResult)) {
                $this->errorMessage = 'The hreflang header is set, but seems to be broken:' . $hrefLangItem;
                return false;
            }

            $hrefLangHref = $hrefLangResult[1];
            if(!preg_match( '~hreflang=[\'"]?([a-zA-Z-]+)[\'"]?~', $hrefLangItem, $hrefLangValueResult)) {
                $this->errorMessage = 'The hreflang header is set, but seems to be broken:' . $hrefLangItem;
                return false;
            }

            $hrefLangValue = $hrefLangValueResult[1];
            $normalizedItems[] = [
                'hreflang' => (string) $hrefLangValue,
                'href' => (string) $hrefLangHref
            ];
        }
        return $normalizedItems;
    }

    private function getNormalizedBodyItems()
    {
        $hrefLangItems = $this->getDomElementFromBodyByXpath('/html/head/link[@rel="alternate"][@hreflang]');

        if(! is_array($hrefLangItems)) {
            return [];
        }

        $normalizedItems = [];
        foreach($hrefLangItems as $hrefLangItem)
        {
            $normalizedItems[] = [
                'hreflang' => (string) $hrefLangItem['hreflang'],
                'href' => (string) $hrefLangItem['href']
            ];
        }
        return $normalizedItems;
    }
}
