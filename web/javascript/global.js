/**
 * Load Stories
 * @param params
 */
function find(type, page) {
    switch (type) {
        case "changelog":
            $("#changelog").load("changelog", {
                "filters": getFilters()
            });
            break;

        case "stories":
            $("#stories").load("stories", {
                "filters": getFilters(),
                "page": page
            });
            break;
    }

    $(window).scrollTop(0);
}


/**
 * Get filters
 *
 * @returns object
 */
function getFilters() {
    var filters = {};
    filters.storyType = [];
    filters.storyStatus = [];
    filters.search = '';
    filters.label = '';

    $("input[name='storyType[]']:checked").each(function () {
        filters.storyType.push($(this).val());
    });

    $("input[name='storyStatus[]']:checked").each(function () {
        filters.storyStatus.push($(this).val());
    });

    filters.label = $("select[name='label']").val();
    filters.search = $("input:text[name='search']").val();
    filters.createdDateRange = $("select[name='createdDateRange']").val();
    filters.acceptedDateRange = $("select[name='acceptedDateRange']").val();

    return filters;
}

function initFilterHandlers(type) {


    // Filter on checkbox change
    $('input[type="checkbox"]').change(function () {
        find(type);
    });

    var delay = (function () {
        var timer = 0;
        return function (callback, ms) {
            clearTimeout(timer);
            timer = setTimeout(callback, ms);
        };
    })();

    // Filter on label
    $("label").change(function () {
        find(type);
    });

    // Filter on created date range
    $('select[name="createdDateRange"]').on('change', function () {
        find(type);
    });

    // Filter on accepted date range
    $('select[name="acceptedDateRange"]').on('change', function () {
        find(type);
    });

    // Filter on search query
    $('input:text[name="search"]').on('input', function () {
        delay(function () {
            find(type);
        }, 400);
    });
}