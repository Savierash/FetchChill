function changeContent(section) {
    // Hide all content sections
    var sections = document.querySelectorAll('.content-section');
    sections.forEach(function(sec) {
        sec.style.display = 'none';
    });

    // Show the selected content section
    var selectedSection = document.getElementById(section);
    selectedSection.style.display = 'block';
}
