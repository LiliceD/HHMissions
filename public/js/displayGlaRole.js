function displayGlaRole(isAdmin = true) {
    let categoryElt = document.getElementById('user_category'),
        checkboxElt = document.getElementById('user_glaRole'),
        glaRoleElt = checkboxElt.parentNode;

    // Show checkbox only when selected category is Volunteer
    function displayCheckbox() {
      if (categoryElt.value === 'ROLE_VOLUNTEER') {
        glaRoleElt.classList.remove('d-none');
      } else {
        glaRoleElt.classList.add('d-none');
      }
    }
    
    if (isAdmin) {
        // Update checkbox display on page load and category changes
        document.addEventListener('DOMContentLoaded', displayCheckbox);
        categoryElt.addEventListener('change', displayCheckbox);    
    } else {
        // Hide category and checkbox for non-admin connected user
        categoryElt.classList.add('d-none');
        glaRoleElt.classList.add('d-none');
    }
}