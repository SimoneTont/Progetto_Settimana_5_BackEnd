<?php
    session_start(); // leggo una sessione esistente
    session_destroy(); // distruggo una sessione esistente
    header('Location: http://localhost'); // reindirizzo l'utente alla homepage