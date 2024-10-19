let paginator = $('.paginator'),
    currentIndex = paginator.data('index'),
    modsOnPage = 20,
    url = new URL('/mods/browse', window.location.href),
    totalCount = paginator.data('all-mods-loaded'),
    queryParams = new URLSearchParams(window.location.search),
    paramsToAdd = {}
;

queryParams.delete('index');
queryParams.delete('name');
queryParams.delete('category');
queryParams.delete('searchFilter');

$(document).on('click', '.modPreviousPage', function(){
    let displayPreviousPages = Math.floor((currentIndex - modsOnPage) / modsOnPage) * modsOnPage,
        modsBrowseName = $('.modsBrowseName'),
        modsBrowseCategory = $('.modsBrowseCategory'),
        modsSortBy = $('.modsSortBy')
    ;

    if (currentIndex !== 0) {
        paramsToAdd.index = displayPreviousPages;
    } else {

        return;
    }
    if (modsBrowseName.val().length > 1) {
        paramsToAdd.searchFilter = modsBrowseName.val().trim();
    }
    if (modsBrowseCategory.val() !== null) {
        paramsToAdd.categories = modsBrowseCategory.val().trim();
    }

    if (modsSortBy.val() !== null) {
        paramsToAdd.sortBy = modsSortBy.val().trim();
    }

    for (key in paramsToAdd) {
        queryParams.append(key, paramsToAdd[key]);
    }

    window.location.replace(url + '?&' + queryParams.toString());
})

$(document).on('click', '.modNextPage', function(){
    let displayPreviousPages = Math.ceil((currentIndex + modsOnPage) / modsOnPage) * modsOnPage,
        modsBrowseName = $('.modsBrowseName'),
        modsBrowseCategory = $('.modsBrowseCategory'),
        modsSortBy = $('.modsSortBy')
    ;

    if (currentIndex + modsOnPage > totalCount) {

        return;
    } else {
        paramsToAdd.index = displayPreviousPages;
    }

    if (modsBrowseName.val().length > 1) {
        paramsToAdd.searchFilter = modsBrowseName.val().trim();
    }
    if (modsBrowseCategory.val() !== null) {
        paramsToAdd.category = modsBrowseCategory.val().trim();
    }

    if (modsSortBy.val() !== null) {
        paramsToAdd.sortBy = modsSortBy.val().trim();
    }

    for (key in paramsToAdd) {
        queryParams.append(key, paramsToAdd[key]);
    }

    window.location.replace(url + '?' + queryParams.toString());
})
