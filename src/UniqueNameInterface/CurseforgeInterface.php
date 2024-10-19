<?php

declare(strict_types=1);

namespace App\UniqueNameInterface;

class CurseforgeInterface
{
    public const BASE_URL = 'https://api.curseforge.com/v1/';
    public const BASE_URl_MODS_SEARCH = self::BASE_URL. 'mods/search';
    public const BASE_URl_CATEGORIES = self::BASE_URL. 'categories';
    public const BASE_URL_SPECIFIC_MOD = self::BASE_URL. 'mods/';

    public const API_DATA = 'data';
    public const API_PAGINATION = 'pagination';

    public const API_KEY_GAMEID = 'gameId';
    public const API_KEY_GAMEVERSION = 'gameVersion';
    public const API_KEY_MODLOADERTYPE = 'modLoaderType';
    public const API_KEY_LINKS = 'links';
    public const API_KEY_LINKS_WEBSITEURL = 'websiteUrl';
    public const API_KEY_SUMMARY = 'summary';

    public const API_KEY_ID = 'id';
    public const API_KEY_INDEX = 'index';
    public const API_KEY_CATEGORIES = 'categories';
    public const API_KEY_CATEGORIES_NAME = 'classId';
    public const API_KEY_SEARCHFILTER = 'searchFilter';
    public const API_KEY_PAGESIZE = 'pageSize';
    public const API_KEY_LOGO = 'logo';
    public const API_KEY_LOGO_THUMBNAILURL = 'thumbnailUrl';
    public const API_KEY_SEARCHSORTFIELD = 'ModsSearchSortField';
    public const API_KEY_SEARCHSORTFIELD_ENUM = 'enum';
    public const API_KEY_SORTBY = 'sortBy';
    public const API_KEY_ORDERBY = 'searchOrder';
    public const API_KEY_ORDERBY_DOWNLOADCOUNT = 'downloadCount';
    public const API_KEY_SEARCHSORTFIELD_FEATURED = 'Featured';
    public const API_KEY_SEARCHSORTFIELD_FEATURED_KEY = '1';
    public const API_KEY_SEARCHSORTFIELD_POPULARITY = 'Popularity';
    public const API_KEY_SEARCHSORTFIELD_POPULARITY_KEY = '2';
    public const API_KEY_SEARCHSORTFIELD_LASTUPDATED = 'Last Updated';
    public const API_KEY_SEARCHSORTFIELD_LASTUPDATED_KEY = '3';
    public const API_KEY_SEARCHSORTFIELD_NAME = 'Name';
    public const API_KEY_SEARCHSORTFIELD_NAME_KEY = '4';
    public const API_KEY_SEARCHSORTFIELD_AUTHOR = 'Author';
    public const API_KEY_SEARCHSORTFIELD_AUTHOR_KEY = '5';
    public const API_KEY_SEARCHSORTFIELD_TOTALDOWNLOADS = 'Total Downloads';
    public const API_KEY_SEARCHSORTFIELD_TOTALDOWNLOADS_KEY = '6';
    public const API_KEY_SEARCHSORTFIELD_CATEGORY = 'Category';
    public const API_KEY_SEARCHSORTFIELD_CATEGORY_KEY = '7';
    public const API_KEY_SEARCHSORTFIELD_EARLYACCESS = 'Early Access';
    public const API_KEY_SEARCHSORTFIELD_EARLYACCESS_KEY = '9';
    public const API_KEY_SEARCHSORTFIELD_FEATUREDRELEASED = 'Featured Released';
    public const API_KEY_SEARCHSORTFIELD_FEATUREDRELEASED_KEY = '10';
    public const API_KEY_SEARCHSORTFIELD_RELESEADATE = 'Released Date';
    public const API_KEY_SEARCHSORTFIELD_RELESEADATE_KEY = '11';
    public const API_KEY_SEARCHSORTFIELD_RATING = 'Rating';
    public const API_KEY_SEARCHSORTFIELD_RATING_KEY = '12';

    public const API_DATA_LATESTFILES = 'latestFiles';
    public const API_DATA_LATESTFILES_DOWNLOADURL = 'downloadUrl';
    public const API_DATA_NAME_FILE = 'fileName';
    public const API_DATA_NAME = 'name';

}
