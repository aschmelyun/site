<?php

namespace Aschmelyun\BasicFeeds;

class Feed
{

    public $xml;

    public $id;

    public $title;

    public $link;

    public $authors;

    public $updated;

    public $feedUrl;

    public function __construct(array $attributes)
    {
        $this->id = $attributes['id'];
        $this->title = $attributes['title'];
        $this->link = $attributes['link'];
        $this->authors = $attributes['authors'];
        $this->updated = $attributes['updated'] ?? date('c');
        $this->feedUrl = $attributes['feed'] ?? $attributes['link'] . 'feed';
    }

    public static function create(array $attributes): Feed
    {
        $feed = new Feed($attributes);

        $feed->init();

        return $feed;
    }

    public function entry($attributes): Feed
    {
        if (!isset($attributes['updated'])) {
            $attributes['updated'] = date('c');
        }

        $entry = $this->xml->addChild('entry');

        $entry->addChild('id', $attributes['id']);
        $entry->addChild('title', $attributes['title']);

        $link = $entry->addChild('link');
        $link->addAttribute('href', $attributes['link']);
        $link->addAttribute('rel', 'alternate');

        $entry->addChild('updated', $attributes['updated']);
        $entry->addChild('summary', $attributes['summary']);

        $content = $entry->addChild('content');
        $content->addAttribute('type', 'text/html');
        $content->addCData($attributes['content']);

        return $this;
    }

    public function toAtom(): string
    {
        return $this->xml->asXML();
    }

    private function init()
    {
        $xml = new SimpleXMLExtended('<?xml version="1.0" encoding="utf-8"?><feed xmlns="http://www.w3.org/2005/Atom" xmlns:content="http://purl.org/rss/1.0/modules/content/"></feed>');

        $xml->addChild('id', $this->id);

        $xml->addChild('title', $this->title);

        $link = $xml->addChild('link');
        $link->addAttribute('href', $this->link);

        $atomLink = $xml->addChild('atom:link');
        $atomLink->addAttribute('href', $this->feedUrl);
        $atomLink->addAttribute('rel', 'self');
        $atomLink->addAttribute('type', 'application/rss+xml');

        $authors = $xml->addChild('author');
        $authors->addChild('name', $this->authors);

        $xml->addChild('updated', $this->updated);

        $this->xml = $xml;
    }

}