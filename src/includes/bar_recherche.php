<div class="bar_de_recherche">
        <form action="recherche.php" method="GET">
            <nav>
                <ul>
                <li>
                    <img src="assets/icons/home-05.png" alt="icone départ">
                    <input type="text" name="lieu_depart" placeholder="Départ" required
                        value="<?= htmlspecialchars($_GET['lieu_depart'] ?? '') ?>">
                </li>
                <li>
                    <img src="assets/icons/marker-05.png" alt="icone arrivé">
                    <input type="text" name="lieu_arrive" placeholder="Arrivé" required
                        value="<?= htmlspecialchars($_GET['lieu_arrive'] ?? '') ?>">

                </li>
                <li>
                    <img src="assets/icons/calendar-check.png" alt="icone calendrier">
                    <input type="date" name="date_depart" id="date_auj" value="" required
                        value="<?= htmlspecialchars($_GET['date_depart'] ?? '') ?>">

                </li>
                <li>
                    <img src="assets/icons/users-profiles-check.png" alt="icone personnes">
                    <input type="number" name="nb_place" min=1 required
                        value="<?= htmlspecialchars($_GET['nb_place'] ?? 1) ?>">
                </li>
                </ul>
            </nav>
        <button class="bouton_recherche" type="submit">Rechercher</button>
    </form>
</div>