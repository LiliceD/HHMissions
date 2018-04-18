function sortTable(table, n) {
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