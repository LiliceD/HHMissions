function searchSuggestions(searchElementId, resultsDivId, userCategory, controllerUrl) {
  let searchElement = document.getElementById(searchElementId),
      resultsDiv = document.getElementById(resultsDivId),
      previousValue ='',
      previousRequest;


  // Show a dropdown of suggestions of users based on search input
  function userSuggestions(search) {
    // Ajax query to get GLAs matching search
    return ajaxPost(controllerUrl, {'search': search, 'category': userCategory}, (data) => {
      let users = data.split('|');

      resultsDiv.innerHTML = '';
      
      if (users && users[0] !== '') {
        // Add GLA to suggestions
        users.forEach(user => displaySuggestion(user));
      } else {
        // Display a message if no result if found
        let span = document.createElement('span');
        span.className = 'dropdown-item text-secondary';
        span.innerHTML = 'Aucun résultat';
        
        resultsDiv.appendChild(span);
      }

      // Show dropdown
      resultsDiv.style.display = "block";
    });
  }

  function displaySuggestion(user) {
    // Create a dropdown item for each GLA
    let a = document.createElement('a');
    a.className = 'dropdown-item';
    a.href = '#';
    a.innerHTML = user;

    a.addEventListener('click', (e) => {
      // Fill GLA input with selected name
      let value = e.target.innerHTML;
      searchElement.value = value;
      // Hide suggestions once one has been selected
      resultsDiv.style.display = "none";
    });

    resultsDiv.appendChild(a);
  }


  searchElement.addEventListener('input', (e) => {
    let search = searchElement.value.trim();
    // If search has changed, send another request
    if ( search != previousValue) {
      previousValue = search;

      // If previous request is still running, abort
      if (previousRequest && previousRequest.readyState < XMLHttpRequest.DONE) {
          previousRequest.abort();
      }
      previousRequest = userSuggestions(previousValue); // On stocke la nouvelle requête
    }
  });

  searchElement.addEventListener('focus', (e) => {
    let search = e.target.value.trim();
    userSuggestions(search);
  });

  searchElement.addEventListener('blur', () => {
    setTimeout(() => resultsDiv.style.display = "none", 200)
  });
}
