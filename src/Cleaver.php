<?php

namespace Aschmelyun\Cleaver;

use Aschmelyun\Cleaver\Compilers\JsonCompiler;
use Aschmelyun\Cleaver\Compilers\MarkdownCompiler;
use Aschmelyun\Cleaver\Engines\BladeEngine;
use Aschmelyun\Cleaver\Engines\ContentEngine;
use Aschmelyun\Cleaver\Engines\FileEngine;
use Aschmelyun\Cleaver\Output\Display;
use Aschmelyun\Cleaver\Output\Console;

class Cleaver
{

    private $buildTime;
    private $basePath;

    public function __construct(?string $basePath = null)
    {
        $this->buildTime['start'] = microtime(true);
        $this->buildTime['end'] = 0;

        $this->basePath = $basePath ? $basePath : dirname(__FILE__, 2);
    }

    public function build(?string $pageBuildOverride = null): void
    {
        $blade = new BladeEngine($this->basePath);

        $fileEngine = new FileEngine($this->basePath);
        $fileEngine->cleanOutputDir();

        $console = Console::init();

        foreach($fileEngine->getContentFiles($pageBuildOverride) as $contentFile) {
            $compiler = null;
            $ext = pathinfo($contentFile, PATHINFO_EXTENSION);
            switch($ext) {
                case 'json':
                    $compiler = new JsonCompiler($contentFile);
                    break;
                case 'md':
                case 'markdown':
                    $compiler = new MarkdownCompiler($contentFile);
                    break;
                default:
                    $console->error($contentFile, 'needs to be a json or markdown file');
                    break;
            }

            if($compiler && $compiler->checkContent()) {
                $compiler->json->cleaver = ContentEngine::generateCollection($fileEngine, $pageBuildOverride);

                if ($blade->save($blade->render($compiler->json))) {
                    $console->build($compiler->file, $compiler->json->path);
                    continue;
                }

                $console->error($compiler->file, 'there was a problem saving');
                continue;
            }
        }

        // generate XML feed for the site's blog entries
        $collection = ContentEngine::generateCollection($fileEngine, $pageBuildOverride);
        $posts = $collection->filter(function($item) {
            return $item->view === 'layout.post';
        });

        $posts = $posts->map(function ($item, $key) {
            $item->datetime = strtotime($item->published);

            return $item;
        });

        $posts = $posts->sortByDesc('datetime')
            ->take(5);

        $feed = \Aschmelyun\BasicFeeds\Feed::create([
            'id' => 'https://aschmelyun.com/',
            'title' => 'Andrew Schmelyun\'s Blog',
            'link' => 'https://aschmelyun.com/blog',
            'authors' => 'Andrew Schmelyun',
            'feed' => 'https://aschmelyun.com/feed.xml',
        ]);

        foreach($posts as $post) {
            $feed->entry([
                'id' => 'https://aschmelyun.com/' . $post->path,
                'title' => $post->title,
                'link' => 'https://aschmelyun.com/' . $post->path,
                'updated' => date('c', strtotime($post->published)),
                'summary' => $post->excerpt,
                'content' => $post->body
            ]);
        }

        file_put_contents(FileEngine::$outputDir . '/feed.xml', $feed->toAtom());

        $this->buildTime['end'] = microtime(true);

        $console->end($this->buildTime);

    }

}
