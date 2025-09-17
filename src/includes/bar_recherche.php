<div class="bar_de_recherche">
    <form action="recherche.php" method="GET">
        <div class="search-block">
            <img src="assets/icons/home-05.png" alt="Départ">
            <label for="lieu_depart">Départ</label>
            <input type="text" name="lieu_depart" id="lieu_depart" placeholder="Ville de départ" required
                value="<?= htmlspecialchars($_GET['lieu_depart'] ?? '') ?>">
        </div>

        <div class="search-block">
            <img src="assets/icons/marker-05.png" alt="Arrivée">
            <label for="lieu_arrive">Arrivée</label>
            <input type="text" name="lieu_arrive" id="lieu_arrive" placeholder="Ville d'arrivée" required
                value="<?= htmlspecialchars($_GET['lieu_arrive'] ?? '') ?>">
        </div>

        <div class="search-block">
            <img src="assets/icons/calendar-check.png" alt="Date">
            <label for="date_depart">Date</label>
            <input type="date" name="date_depart" id="date_depart" required
                value="<?= htmlspecialchars($_GET['date_depart'] ?? '') ?>">
        </div>

        <div class="search-block">
            <img src="assets/icons/users-profiles-check.png" alt="Places">
            <label for="nb_place">Places</label>
            <input type="number" name="nb_place" id="nb_place" min="1" required
                value="<?= htmlspecialchars($_GET['nb_place'] ?? 1) ?>">
        </div>

        <button type="submit" class="btn btn-primary">Rechercher</button>
    </form>
</div>
