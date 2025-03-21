document.addEventListener("DOMContentLoaded", function () {
    const checkboxes = document.querySelectorAll('.task-filter');
    const taskSections = document.querySelectorAll('.task-category');

    function filterTasks() {
        let selectedStatuses = [];

        // Récupère toutes les checkboxes cochées
        checkboxes.forEach(checkbox => {
            if (checkbox.checked) {
                selectedStatuses.push(checkbox.value);
            }
        });

        // Toujours afficher les tâches actives (status = 1)
        taskSections.forEach(section => {
            const status = section.getAttribute('data-status');
            
            if (status === "1") { 
                section.style.display = "block"; // Toujours afficher les tâches actives
            } else {
                section.style.display = selectedStatuses.includes(status) ? "block" : "none";
            }
        });
    }

    // Ajoute un écouteur sur chaque checkbox
    checkboxes.forEach(checkbox => {
        checkbox.addEventListener("change", filterTasks);
    });

    // Appliquer le filtre au chargement
    filterTasks();
});
