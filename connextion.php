<?php
require ('session_manager.php');
// Connexion à la base de données
$serveur = "localhost";
$utilisateur = "root";
$motdepasse = "";
$base = "gestion_projet";

$conn = mysqli_connect($serveur, $utilisateur, $motdepasse, $base);

if (!$conn) {
    die("Erreur de connexion à la base de données : " . mysqli_connect_error());
}



// Vérification si les champs Email et Password sont envoyés via POST
if (isset($_POST["Email"]) && isset($_POST["Password"])) {
    $Email = mysqli_real_escape_string($conn, $_POST["Email"]);
    $Password = $_POST["Password"];
} else {
    die("Veuillez remplir les champs Email et Password.");
}

// Vérification dans la table "boutique" (admin)
$sqlAdmin = "SELECT * FROM boutique WHERE Email = ?";
$stmtAdmin = mysqli_prepare($conn, $sqlAdmin);
mysqli_stmt_bind_param($stmtAdmin, "s", $Email);
mysqli_stmt_execute($stmtAdmin);
$resultAdmin = mysqli_stmt_get_result($stmtAdmin);

// Si un compte admin est trouvé
if ($rowAdmin = mysqli_fetch_assoc($resultAdmin)) {
    // Vérification avec un mot de passe non haché
    if ($Password === $rowAdmin['password']) {
        $_SESSION['email'] = $Email;
        $_SESSION['role'] = 'admin';
        $_SESSION['utilisateur'] = $rowAdmin['Email']; // Stocker l'utilisateur connecté
        header("Location: Administrateur.php");
        exit();
    } else {
        echo "<h2>Mot de passe incorrect pour l'administrateur.</h2>";
        exit();
    }
}

// Vérification dans la table "utilisateur" (utilisateurs normaux)
$sqlUser = "SELECT * FROM utilisateur WHERE Email = ?";
$stmtUser = mysqli_prepare($conn, $sqlUser);
mysqli_stmt_bind_param($stmtUser, "s", $Email);
mysqli_stmt_execute($stmtUser);
$resultUser = mysqli_stmt_get_result($stmtUser);

// Si un compte utilisateur est trouvé
if ($rowUser = mysqli_fetch_assoc($resultUser)) {
    // Vérification avec un mot de passe haché
    if (password_verify($Password, $rowUser['password'])) {
        $_SESSION['email'] = $Email;
        $_SESSION['role'] = 'utilisateur';
        $_SESSION['utilisateur'] = $rowUser['Email']; // Stocker l'utilisateur connecté
        header("Location: tous.php");
        exit();
    } else {
        echo "<h2>Mot de passe incorrect pour l'utilisateur.</h2>";
        exit();
    }
}

// Si aucun compte n'est trouvé
echo "<h2>Aucun compte correspondant trouvé.</h2>";
exit();
?>
