<header>
    <div class="logo">
        <img src="assets/icons/logo_header.png" alt="logo EcoRide header">
    </div>

    <div class="menu">
        <div class="nav_links">
        <nav>
            <ul>
                <li><a href="index.php">Accueil</a></li>
                <li><a href="recherche.php" id="recherche_covoit">Covoiturage</a> </li>
                <li><a href="contact.php" id="contact">Contact</a></li>
                <li><a href="mon_compte.php" class="bouton_connexion">Mon Compte</a></li>
            </ul>
        </nav>
        </div>
        

        <div class="hamburger">
            <span></span>
            <span></span>
            <span></span>
        </div>

    </div>

</header>

<script>
    const hamburger = document.querySelector('.hamburger');
    const navLinks = document.querySelector('nav ul');

    hamburger.addEventListener('click', () => {
        navLinks.classList.toggle('active');
    });
</script>