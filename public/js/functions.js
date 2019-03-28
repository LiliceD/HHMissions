const CATEGORY_GLA = 'GLA';
const STATUS_CLOSED = 'Fermée';
const EMAIL_DOMAIN = '@'+'habitat-humanisme.org';

/**
 * Remove accents and convert to uppercase
 *
 * @param {string} text
 *
 * @returns {string}
 */
function capitalizeText(text) {
    return text.normalize('NFD').replace(/[\u0300-\u036F]/g, "").toUpperCase();
}


/**
 * Convert string date from 'dd/mm/yyy' to 'yyyy-mm-dd'
 *
 * @param {string} strDate
 *
 * @returns {string}
 */
function formatStringToDate(strDate) {
    let dateParts = strDate.split('/');

    if (strDate) {
        return dateParts[2] + '-' + dateParts[1] + '-' + dateParts[0];
    } else {
        return '';
    }
}


/**
 * Get the list of checked values from a jQuery collection of options
 *
 * @param {jQuery} options
 *
 * @returns {array}
 */
function getCheckedOptionsValues(options) {
    return $(':checkbox:checked', options)
        .get()
        .reduce((checkedOptions, option) => {
            checkedOptions.push(option.value);

            return checkedOptions;
        }, []);
}

function getSelectedValue(select) {
    return $('option:selected', select).val();
}


/**
 * Add sorting on clicking on one of the tables' headers
 */
function addTableSorting(tables) {
    tables.forEach(table => {
        table.tablesorter({dateFormat: "ddmmyyyy"});
    });
}



/**
 * Show number of GLA / Volunteer / Address options selected
 */
function displayNumberOfCheckedOptionsInBadges() {
    initializeBadges();
    addCheckboxesEvents();
}

function initializeBadges() {
    updateNumberOfCheckedOptionsInBadge();
}

function addCheckboxesEvents() {
    $('#mission_search_gla, #mission_search_volunteer, #mission_search_address')
        .on('change', () => {
            updateNumberOfCheckedOptionsInBadge();
        });
}

function updateNumberOfCheckedOptionsInBadge() {
    $('div.dropdown').each((index, div) => {
        let numberChecked = $(':checkbox:checked', div).length;
        $('span.badge', div).text(numberChecked);
    });
}


/**
 * -- FILL TABLE OF MISSIONS --
 */

/**
 * @param {jQuery} table
 * @param {Object} filters
 * @param {string} url
 * @param {Function} callback
 */
function fillMissionsTable(table, filters, url, callback) {
    let nbOfMissions = 0;

    preUpdateTable(table);

    return new Promise(resolve => $.post(url, filters, (data) => {
        if (data.missions.length) {
            let missions, properties;
            [missions, properties] = callback(data);

            addMissionsToTable(table, missions, properties);
            nbOfMissions = getNbOfMissions(missions);

            unHideTable(table);
        } else {
            displayNoMissionMessage(table);
        }

        postUpdateTable(table, nbOfMissions);

        return resolve(nbOfMissions);
    }));
}

/**
 * Empty table, hide no mission message and show loading spinner
 */
function preUpdateTable(table) {
    let tableParent = table.parent(),
        tableBody = table.children('tbody'),
        noMissionsDiv = $('.noMission', tableParent),
        loadingAlert = $('.loadingAlert', tableParent);

    table.hide();
    tableBody.empty();
    noMissionsDiv.hide();
    loadingAlert.removeAttr('hidden');
}

/**
 * Fill a given table with a list of missions, columns being the given list of properties to display
 *
 * @param {jQuery} table
 * @param {array} missions
 * @param {array} properties
 */
function addMissionsToTable(table, missions, properties) {
    let tableBody = table.children('tbody');

    missions.forEach( mission => {
        // Create row
        let row = createMissionRow(mission);

        // Create cells
        properties.forEach( prop => {
            applyPropertyToMissionRow(mission, prop, row);
        });

        tableBody.append(row);
    });
}

/**
 * Create a new tr element with classes and attributes for a mission
 *
 * @param {array} mission
 *
 * @returns {jQuery}
 */
function createMissionRow(mission) {
    let row = $(document.createElement('tr'));
    row.attr('id', 'mission-' + mission.id);

    row.addClass('mission');
    if (mission.status === STATUS_CLOSED) {
        row.addClass('text-secondary bg-inactive');
    }

    return row;

}

function applyPropertyToMissionRow(mission, prop, row) {
    if (mission[prop]) {
        let cell,
            text = '',
            classes = [];

        if ('description' === prop || 'conclusions' === prop) {
            cell = createDescriptionCell(mission[prop]);
        } else {
            switch (prop) {
                case 'gla':
                case 'volunteer':
                    text = mission[prop].name;
                    classes = [
                        prop,
                        prop + '-' + mission[prop].id
                    ];
                    break;
                case 'address':
                    text = getFullAddress(mission);
                    break;
                default:
                    text = mission[prop];
            }

            if ('dateCreated' === prop) {
                classes.push('font-weight-bold');
            }

            cell = createCell(text, classes);
        }

        row.append(cell);
    } else if ('button' === prop) {
        let button = createViewButton(mission.url);
        row.append(button);
    } else {
        row.append(createCell(''));
    }
}

/**
 * Create a td element with a given mission's description
 *
 * @param {string} description
 *
 * @returns {jQuery}
 */
function createDescriptionCell(description) {
    let cell = $(document.createElement('td')),
        div = $(document.createElement('div'));
    div.addClass('description-ellipsis')
        .attr('data-toggle', 'popover')
        .attr('data-content', description)
        .html(description);

    cell.append(div);

    return cell;

}

/**
 * Concatenate a mission's address's elements
 *
 * @param {array} mission
 *
 * @returns {string}
 */
function getFullAddress(mission) {
    let fullAddress = mission['address']['street'] + '<br>';
    fullAddress += mission['address']['name'] ? mission['address']['name'] + '<br>' : '';
    fullAddress += mission['address']['zipCode'] + ' ' + mission['address']['city'];
    return fullAddress;

}

/**
 * Create a td element with the given classes and containing the given text
 *
 * @param {string} text
 * @param {array} classes
 *
 * @returns {jQuery}
 */
function createCell(text, classes = []) {
    let cell = $(document.createElement('td'));
    classes.forEach(className => {
        cell.addClass(className);
    });

    cell.html(text);

    return cell;
}

/**
 * Create a td element containing a link to view a given url
 *
 * @param {string} url
 *
 * @returns {jQuery}
 */
function createViewButton(url) {
    let button = $(document.createElement('td')),
        a = $(document.createElement('a'));
    a.attr('href', url)
        .addClass('btn btn-sm btn-primary')
        .html('<i class="fas fa-eye"></i> Voir');

    button.append(a);

    return button;
}

/**
 * Get the number of not closed missions
 *
 * @param {array} missions
 *
 * @returns {int}
 */
function getNbOfMissions(missions) {
    return missions.filter(mission => {
        return mission.status !== STATUS_CLOSED;
    }).length
}

/**
 * Un-hide table and activate popovers and sorting
 */
function unHideTable(table) {
    table.show();

    activatePopovers();

    table.trigger('update', [true]);
}

function activatePopovers() {
    setTimeout(() => {
        $('[data-toggle="popover"]').popover({
            'placement': 'auto',
            'trigger': 'hover'
        });
    }, 1000);
}

function displayNoMissionMessage(table) {
    let noMissionsDiv = $('.noMission', table.parent());

    noMissionsDiv.show();
}

/**
 * Update table's badge, hide loading spinner and uncheck 'myMissions' checkbox
 */
function postUpdateTable(table, nbOfMissions) {
    let badge = $('.nbOfMissions', table.parent()),
        tableParent = table.parent(),
        loadingAlert = $('.loadingAlert', tableParent);

    badge.html(nbOfMissions);
    loadingAlert.attr('hidden', 'true');
    $('.myMissions', tableParent).prop('checked', false);
}

/**
 * -- END OF FILL TABLE OF MISSIONS --
 */