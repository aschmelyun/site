<?php

namespace Aschmelyun\BasicFeeds;

class SimpleXMLExtended extends \SimpleXMLElement
{
    public function addCData($text): void
    {
        $node = dom_import_simplexml($this);
        $no = $node->ownerDocument;
        $node->appendChild($no->createCDATASection($text));
    }
}