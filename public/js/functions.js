// Remove accents and convert to uppercase
function capitalizeInput(input) {
    return input.normalize('NFD').replace(/[\u0300-\u036F]/g, "").toUpperCase();
}


// Convert string date from 'dd/mm/yyy' to 'yyyy-mm-dd'
function formatStringToDate(strDate) {
    let dateParts = strDate.split('/');

    if (strDate) {
        return dateParts[2] + '-' + dateParts[1] + '-' + dateParts[0];
    } else {
        return '';
    }
}


// Add sorting to given table to all columns but the last n ones
function sortTable(table, n = 0) {
    tableHeadRows = table.querySelectorAll('thead > tr > th');
      
    for(let i = 0, c = tableHeadRows.length - n; i < c; i++) {
        tableHeadRows[i].addEventListener('click', () => {
          sortTableColumn(table, i);
        });
    }
}

// Used in sortTable
// Add sorting to given table on click on given column n
function sortTableColumn(table, n) {
    let rows, switching = true, i, x, y, shouldSwitch, dir, switchcount = 0;

    // Set the sorting direction to ascending:
    dir = 'asc'; 
    
    /* Make a loop that will continue until
    no switching has been done: */
    while (switching) {
        // Start by saying: no switching is done:
        switching = false;

        rows = table.getElementsByTagName('tr');

        /* Loop through all table rows (except the
        first, which contains table headers): */
        for (i = 1, c = rows.length - 1; i < c; i++) {
            // Start by saying there should be no switching:
            shouldSwitch = false;

            /* Get the two elements you want to compare,
            one from current row and one from the next: */
            x = rows[i].getElementsByTagName('td')[n];
            y = rows[i + 1].getElementsByTagName('td')[n];

            if (x.getAttribute('data-is-date')) {
                xSplit = x.innerHTML.split('/');
                ySplit = y.innerHTML.split('/');

                xVal = xSplit[2] + xSplit[1] + xSplit[0];
                yVal = ySplit[2] + ySplit[1] + ySplit[0];
            } else {
                xVal = x.innerHTML.toLowerCase();
                yVal = y.innerHTML.toLowerCase();
            }

            /* Check if the two rows should switch place,
            based on the direction, asc or desc: */
            if (dir === 'asc') {
                if (xVal > yVal) {
                    // If so, mark as a switch and break the loop:
                    shouldSwitch = true;
                    break;
                }
            } else if (dir === 'desc') {
                if (xVal < yVal)  {
                    // If so, mark as a switch and break the loop:
                    shouldSwitch = true;
                    break;
                }
            }
        }

        if (shouldSwitch) {
            /* If a switch has been marked, make the switch
            and mark that a switch has been done: */
            rows[i].parentNode.insertBefore(rows[i + 1], rows[i]);
            switching = true;

            // Each time a switch is done, increase this count by 1:
            switchcount ++; 
        } else {
            /* If no switching has been done AND the direction is "asc",
            set the direction to "desc" and run the while loop again. */
            if (switchcount === 0 && dir === 'asc') {
                dir = 'desc';
                switching = true;
            }
        }
    }
}


// Used in list and recap.html.twig
function fillMissionsTable(table, filters, url, callback, noMissionsDiv, loadingAlert, badge, closedStatus = null) {
    // Hide message
    noMissionsDiv.innerHTML = '';

    // Show loading alert
    loadingAlert.removeAttribute('hidden');

    // Send filters to controller
    ajaxPost(url, filters, JSONResponse => {
        // Retrieve missions matching filters
        let data = JSON.parse(JSONResponse);
        
        // Empty table body
        let tbody = table.querySelector('tbody');
        tbody.innerHTML = '';

        if (data) {
          let missions, properties, shortDescription, button, counter;
          
          [missions, properties, shortDescription, button, counter] = callback(data);

          // Add missions to table
          missionsToTbody(tbody, missions, properties, shortDescription, button, closedStatus);

          // Update badge with number of missions finished but not closed
          badge.innerHTML = counter;

          // Unhide table
          table.removeAttribute('hidden');
        } else {
          // Display message
          noMissionsDiv.innerHTML = 'Aucune mission à afficher !';
        }

        // Hide loading alert
        loadingAlert.setAttribute('hidden', 'true');
    });
}

// Used in fillMissionsTable
function missionsToTbody(tbody, missions, properties, shortDescription, button, closedStatus = null) {
    missions.forEach( mission => {
        // Create tr
        let tr = document.createElement('tr');

        tr.id = 'mission-' + mission.id;

        if (mission.status === closedStatus) {
          tr.className = 'text-secondary bg-inactive';
        }

        // Create td
        properties.forEach( prop => {
            let td,
                innerHTML = '',
                classes = [],
                dataAttributes = [];

            // Special case for short description
            if (shortDescription && prop === 'description') {
                let div = document.createElement('div');

                div.className = 'description-ellipsis';
                
                div.setAttribute('data-toggle', 'popover');
                div.setAttribute('data-content', mission.description);
                
                div.innerHTML = mission.description;

                td = document.createElement('td');
                td.appendChild(div);
            } else {
                if (mission[prop]) {
                    // Dates have special data-attributes
                    if (prop.match(/^date/)) {
                        dataAttributes.push({
                            'name': 'data-is-date',
                            'value': 'true'
                        });
                    }
        
                    // dateCreated is in bold
                    if (prop === 'dateCreated') {
                        classes.push('font-weight-bold');
                    }
        
                    if (prop === 'gla' || prop === 'volunteer') {
                    // Gla and volunteer have classes and different innerHTML
                        innerHTML = mission[prop].name;

                        classes.push(prop);
                        classes.push(prop + '-' + mission[prop].id);
                    } else if (prop === 'accomodation') {
                    // Accomodation has special innerHTML
                        innerHTML = mission[prop].street + '<br>';

                        if (mission[prop].name) {
                          innerHTML += mission[prop].name + '<br>'; 
                        }

                        innerHTML += mission[prop].postalCode + ' ' + mission[prop].city;
                    } else {
                        innerHTML = mission[prop];
                    }
                }

                td = createTd(innerHTML, classes, dataAttributes);
            }
            
            tr.appendChild(td);
        });

        // Possibly add button to view mission
        if (button) {
            let td = document.createElement('td'),
                a = document.createElement('a');

                a.href = mission.url;
                a.className = 'btn btn-sm btn-primary';
                a.innerHTML = '<i class="fas fa-eye"></i> Voir';

            td.appendChild(a);

            tr.appendChild(td);
        }

        tbody.appendChild(tr);
    });
}

// Used in missionsToTbdoy
function createTd(text, classes = null, dataAttributes = null) {
    let td = document.createElement('td');

    if (classes) {
        classes.forEach(className => {
            td.classList.add(className);
        });
    }

    if (dataAttributes) {
        dataAttributes.forEach( dataAttr => {
            td.setAttribute(dataAttr.name, dataAttr.value);
        });
    }

    td.innerHTML = text;

    return td;
}