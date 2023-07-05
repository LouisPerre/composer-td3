<?php

declare(strict_types=1);

namespace Louisperre\ComposerTd3;

use Symfony\Component\DomCrawler\Crawler;
use Symfony\Component\HttpClient\HttpClient;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class ApiTmdb
{
    /**
     * @throws TransportExceptionInterface
     * @throws \Exception
     * @return string[]
     */
    public function getSlugCategories(): array
    {
        $client = HttpClient::create();
        $response = $client->request(
            'GET',
            'https://www.themoviedb.org/bible/movie/59f3b16d9251414f20000006#:~:text=The%20genres%20currently%20available%20in,Movie%2C%20War%2C%20and%20Western.'
        );

        $statusCode = $response->getStatusCode();
        $categories = [];
        if ($statusCode === 200) {
            try {
                $content = $response->getContent();
                $crawler = new Crawler($content);
                $list = $crawler
                    ->filter('#59f73d539251416e71000016 p:first-of-type')
                    ->reduce(function (Crawler $node, $i) use (&$categories) : bool {
                        /** @var \DOMElement $category */
                        foreach ($node->filter('a') as $category) {
                            // @phpstan-ignore-next-line
                            $value = $category->attributes->item(0)->value;
                            $start = strpos($value, "genre/") + 6;
                            $end = strpos($value, "/movie", $start);
                            $result = substr($value, $start, $end - $start);
                            $categories[] = $result;
                        }
                        return ($i % 2) === 0;
                    });
            } catch (ClientExceptionInterface|RedirectionExceptionInterface|ServerExceptionInterface|TransportExceptionInterface $e) {
                throw new \Exception($e->getMessage());
            }
        }
        return $categories;
    }

    /**
     * @throws TransportExceptionInterface
     * @throws \Exception
     * @return string[]
     */
    public function getNameCategories(): array
    {
        $client = HttpClient::create();
        $response = $client->request(
            'GET',
            'https://www.themoviedb.org/bible/movie/59f3b16d9251414f20000006#:~:text=The%20genres%20currently%20available%20in,Movie%2C%20War%2C%20and%20Western.'
        );

        $statusCode = $response->getStatusCode();
        $categories = [];
        if ($statusCode === 200) {
            try {
                $content = $response->getContent();
                $crawler = new Crawler($content);
                $list = $crawler
                    ->filter('#59f73d539251416e71000016 p:first-of-type')
                    ->reduce(function (Crawler $node, $i) use (&$categories) : bool {
                        foreach ($node->filter('a') as $category) {
                            $categories[] = $category->textContent;
                        }
                        return ($i % 2) === 0;
                    });
            } catch (ClientExceptionInterface|RedirectionExceptionInterface|ServerExceptionInterface|TransportExceptionInterface $e) {
                throw new \Exception($e->getMessage());
            }
        }
        return $categories;
    }

    /**
     * @throws TransportExceptionInterface
     * @return array<int, array<string, string>>
     */
    public function GetMoviesWithCategory(string $category): array
    {
        $client = HttpClient::create();
        $response = $client->request(
            'GET',
            'https://www.themoviedb.org/genre/'. $category . '/movie'
        );
        $statusCode = $response->getStatusCode();
        $moviesList = [];
        if ($statusCode === 200) {
            try {
                $content = $response->getContent();
                $crawler = new Crawler($content);
                $list = $crawler
                    ->filter('#results_page_1 > div:not(#pagination_page_1)')
                    ->each(function (Crawler $node, $i) use (&$moviesList): bool {
//                        dump($node->count());
                        if ($node->count() > 0) {
                            $moviesList[] = [
                                'title' => $node->filter('div.details .wrapper .title h2')->count() > 0 ? $node->filter('div.details .wrapper .title h2')->text() : '',
                                'release_date' => $node->filter('span.release_date')->count() > 0 ? $node->filter('span.release_date')->text() : '',
                                'description' => $node->filter('div.wrapper .overview p')->count() > 0 ? $node->filter('div.wrapper .overview p')->text() : '',
                                'link' => $node->filter('div.details .wrapper .title a')->count() > 0 ? 'https://www.themoviedb.org' . $node->filter('div.details .wrapper .title a')->attr('href') : '',
                                'img' => $node->filter('img.poster')->count() > 0 ? 'https://image.tmdb.org' . $node->filter('img.poster')->attr('src') : ''
                            ];
                        }
                        return $i % 2 === 0;
                    });
            } catch (ClientExceptionInterface|RedirectionExceptionInterface|ServerExceptionInterface|TransportExceptionInterface $e) {
                throw new \Exception($e->getMessage());
            }
        }
        return $moviesList;
    }

    /**
     * @throws TransportExceptionInterface
     * @return string[]|null[]
     */
    public function GetMovieInformation(string $url): array
    {
        $client = HttpClient::create();
        $response = $client->request(
            'GET',
            $url
        );
        $statusCode = $response->getStatusCode();
        $listInfo = [];
        if ($statusCode === 200) {
            try {
                $content = $response->getContent();
                $crawler = new Crawler($content);
                $listInfo = [
                    'title' => $crawler->filter('div.title.ott_true > h2 > a')->count() > 0 ? $crawler->filter('div.title.ott_true > h2 > a')->text() : '',
                    'release' => $crawler->filter('div.facts span.release')->count() > 0 ? $crawler->filter('div.facts span.release')->text() : '',
                    'certification' => $crawler->filter('div.facts span.certification')->count() > 0 ? $crawler->filter('div.facts span.certification')->text() : '',
                    'runtime' => $crawler->filter('div.facts span.runtime')->count() > 0 ? $crawler->filter('div.facts span.runtime')->text() : '',
                    'genre' => $crawler->filter('div.facts span.genres')->count() > 0 ? $crawler->filter('div.facts span.genres')->text() : '',
                    'description' => $crawler->filter('div.header_info div.overview p')->count() > 0 ? $crawler->filter('div.header_info div.overview p')->text() : '',
                    'poster' => $crawler->filter('div.poster img.poster')->count() > 0 ? 'https://image.tmdb.org' . $crawler->filter('div.poster img.poster')->attr('src') : ''
                ];
            } catch (ClientExceptionInterface|RedirectionExceptionInterface|ServerExceptionInterface|TransportExceptionInterface $e) {
                throw new \Exception($e->getMessage());
            }
        }
        return $listInfo;
    }

}