<?php

declare(strict_types=1);

namespace App\Service\Mods\Curseforge;


use App\Entity\Server;
use App\UniqueNameInterface\CurseforgeInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Component\DependencyInjection\Attribute\Autowire;


class CurseforgeService
{

    private string $apiKey = '';
    public function __construct (
        private HttpClientInterface $client,
        #[Autowire('%env(CURSEFORGE_API_KEY)%')] string $apiKey
    ){
        $this->apiKey = $apiKey;
    }

    public function getMods (
        Server  $server,
        int     $indexStart,
        string  $sortBy = '',
        string  $category = '',
        string  $searchFilter = '',
    ): array {
        $paramsArray = [
            CurseforgeInterface::API_KEY_GAMEID         => $_ENV['CURSEFORGE_MINECRAFT_GAME_ID'],
            CurseforgeInterface::API_KEY_GAMEVERSION    => $server->getVersion(),
            CurseforgeInterface::API_KEY_MODLOADERTYPE  => $server->getType(),
            CurseforgeInterface::API_KEY_INDEX          => $indexStart,
            CurseforgeInterface::API_KEY_PAGESIZE       => 20,
            CurseforgeInterface::API_KEY_ORDERBY        => CurseforgeInterface::API_KEY_ORDERBY_DOWNLOADCOUNT,
        ];

        if ($sortBy !== '') {
            $paramsArray[CurseforgeInterface::API_KEY_SEARCHFILTER] = $sortBy;
        }
        if ($category !== '') {
            $paramsArray[CurseforgeInterface::API_KEY_CATEGORIES_NAME] = $category;
        }
        if ($searchFilter !== '') {
            $paramsArray[CurseforgeInterface::API_KEY_SEARCHFILTER] = $searchFilter;
        }

        $req = $this->client->request(
            'GET',
            CurseforgeInterface::BASE_URl_MODS_SEARCH,
            $this->getHeaders($paramsArray)
        );


        return $req->toArray();
    }

    public function getCategories (): array {
        $paramsArray = [
            CurseforgeInterface::API_KEY_GAMEID         => $_ENV['CURSEFORGE_MINECRAFT_GAME_ID'],
        ];

        $req = $this->client->request(
            'GET',
            CurseforgeInterface::BASE_URl_CATEGORIES,
            $this->getHeaders($paramsArray)
        );

        return $req->toArray();
    }

    private function getHeaders (
        array   $params = []
    ): array {
        return [
            'query'     => $params,
            'headers'   => [
                'x-api-key'     => $this->apiKey,
                'Content-Type'  => 'application/json',
                'Accept'        => 'application/json'
            ]
        ];
    }

    public function getSortableFields (): array {
        return [
            [
                CurseforgeInterface::API_KEY_SORTBY => CurseforgeInterface::API_KEY_SEARCHSORTFIELD_FEATURED,
                CurseforgeInterface::API_KEY_SEARCHSORTFIELD_ENUM => CurseforgeInterface::API_KEY_SEARCHSORTFIELD_FEATURED_KEY
            ],
            [
                CurseforgeInterface::API_KEY_SORTBY => CurseforgeInterface::API_KEY_SEARCHSORTFIELD_POPULARITY,
                CurseforgeInterface::API_KEY_SEARCHSORTFIELD_ENUM => CurseforgeInterface::API_KEY_SEARCHSORTFIELD_POPULARITY_KEY
            ],
            [
                CurseforgeInterface::API_KEY_SORTBY => CurseforgeInterface::API_KEY_SEARCHSORTFIELD_LASTUPDATED,
                CurseforgeInterface::API_KEY_SEARCHSORTFIELD_ENUM => CurseforgeInterface::API_KEY_SEARCHSORTFIELD_LASTUPDATED_KEY
            ],
            [
                CurseforgeInterface::API_KEY_SORTBY => CurseforgeInterface::API_KEY_SEARCHSORTFIELD_NAME,
                CurseforgeInterface::API_KEY_SEARCHSORTFIELD_ENUM => CurseforgeInterface::API_KEY_SEARCHSORTFIELD_NAME_KEY
            ],
            [
                CurseforgeInterface::API_KEY_SORTBY => CurseforgeInterface::API_KEY_SEARCHSORTFIELD_AUTHOR,
                CurseforgeInterface::API_KEY_SEARCHSORTFIELD_ENUM => CurseforgeInterface::API_KEY_SEARCHSORTFIELD_AUTHOR_KEY
            ],
            [
                CurseforgeInterface::API_KEY_SORTBY => CurseforgeInterface::API_KEY_SEARCHSORTFIELD_TOTALDOWNLOADS,
                CurseforgeInterface::API_KEY_SEARCHSORTFIELD_ENUM => CurseforgeInterface::API_KEY_SEARCHSORTFIELD_TOTALDOWNLOADS_KEY
            ],
            [
                CurseforgeInterface::API_KEY_SORTBY => CurseforgeInterface::API_KEY_SEARCHSORTFIELD_CATEGORY,
                CurseforgeInterface::API_KEY_SEARCHSORTFIELD_ENUM => CurseforgeInterface::API_KEY_SEARCHSORTFIELD_CATEGORY_KEY
            ],
            [
                CurseforgeInterface::API_KEY_SORTBY => CurseforgeInterface::API_KEY_SEARCHSORTFIELD_EARLYACCESS,
                CurseforgeInterface::API_KEY_SEARCHSORTFIELD_ENUM => CurseforgeInterface::API_KEY_SEARCHSORTFIELD_EARLYACCESS_KEY
            ],
            [
                CurseforgeInterface::API_KEY_SORTBY => CurseforgeInterface::API_KEY_SEARCHSORTFIELD_FEATUREDRELEASED,
                CurseforgeInterface::API_KEY_SEARCHSORTFIELD_ENUM => CurseforgeInterface::API_KEY_SEARCHSORTFIELD_FEATUREDRELEASED_KEY
            ],
            [
                CurseforgeInterface::API_KEY_SORTBY => CurseforgeInterface::API_KEY_SEARCHSORTFIELD_RELESEADATE,
                CurseforgeInterface::API_KEY_SEARCHSORTFIELD_ENUM => CurseforgeInterface::API_KEY_SEARCHSORTFIELD_RELESEADATE_KEY
            ],
            [
                CurseforgeInterface::API_KEY_SORTBY => CurseforgeInterface::API_KEY_SEARCHSORTFIELD_RATING,
                CurseforgeInterface::API_KEY_SEARCHSORTFIELD_ENUM => CurseforgeInterface::API_KEY_SEARCHSORTFIELD_RATING_KEY
            ],
        ];
    }

    public function getModSpecific (
        int     $modId
    ): array {
        $req = $this->client->request(
            'GET',
            CurseforgeInterface::BASE_URL_SPECIFIC_MOD. $modId,
            $this->getHeaders()
        );

        return $req->toArray()[CurseforgeInterface::API_DATA];
    }

}
