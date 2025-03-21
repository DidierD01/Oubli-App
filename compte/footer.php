<!-- Footer -->
<footer class="fixed-bottom">
    <div class="container">
        <p class="mb-0">&copy; 2025 Oubli App. Tous droits réservés.</p>
    </div>
</footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
    <script src="../js/app.js"></script>
    <script>
    // Fermer l'alerte automatiquement après 10 secondes (10000 ms)
    setTimeout(function () {
        const alertBox = document.getElementById("alertBox");
        if (alertBox) {
            alertBox.classList.remove("show");
            alertBox.classList.add("fade");
            alertBox.style.opacity = 0;
        }
    }, 10000);
    </script>
</body>
</html>