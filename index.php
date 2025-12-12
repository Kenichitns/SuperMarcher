<?php
// --- CONFIGURATION ---
function getBdd() {
    try {
        // Connexion simple
        return new PDO('mysql:host=localhost;dbname=supermarche;charset=utf8', 'root', '', array(PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION));
    } catch (Exception $e) {
        die('Erreur BDD : ' . $e->getMessage());
    }
}

// --- FONCTIONS ---
function getFamilles() {
    return getBdd()->query('SELECT * FROM famille'); 
}

// Correction : Nom au pluriel 'getProduits'
function getProduits($id) { 
    $stmt = getBdd()->prepare('SELECT * FROM produit WHERE famille_id = ?');
    $stmt->execute([$id]);
    return $stmt;
}

function getAdherent() {
    return getBdd()->query('SELECT * FROM adherent');
}

// --- ROUTAGE ---
$page = $_GET['page'] ?? 'accueil';
?>

<!DOCTYPE html>
<html>
<head>
    <title>Supermarché Shukdeb</title>
    <meta charset="utf-8">
    <link rel="stylesheet" href="style.css">
</head>
<body>

<div class="container">

    <?php if ($page === 'accueil'): ?>
        <div class="menu-accueil">
            <h1>Supermarché Mohamed</h1>
            <h2>Bienvenue</h2>
            
            <a href="?page=famille"><button class="btn-primary">Passer une commande</button></a><br>
            <a href="?page=compte"><button class="btn-primary">Accéder à votre compte</button></a><br>
            <button class="btn-outline" onclick="alert('Bientôt disponible')">Inscription & Fidélité</button><br>
            <br>
            <a href="?page=gestion"><button class="btn-secondary" style="font-size: 0.8em;">Gestion BDD</button></a>
        </div>

    <?php elseif ($page === 'famille'): ?>
        <div class="box">
            <h3>Faites vos courses</h3>
            <h4>Choisissez une catégorie</h4>
            
            <form action="index.php" method="GET"> 
                <input type="hidden" name="page" value="produit">
                
                <select name="famille_id" size="5">
                    <?php
                    // Attention : utilise bien id_famille et nomdefamille
                    foreach(getFamilles() as $row) {
                        echo "<option value='{$row['id_famille']}'>{$row['nomdefamille']}</option>";
                    }
                    ?>
                </select>
                <br><br>
                
                <a href="?page=accueil"><button type="button" class="btn-secondary">Retour Accueil</button></a>
                <input type="submit" value="Suivant" class="btn-success">
            </form>
        </div>

    <?php elseif ($page === 'produit'): ?>
        <div class="box">
            <h3>Faites vos courses</h3>
            <h4>Choisissez un produit</h4>
            
            <form action="" method="POST">
                <select name="produit_id" size="5">
                    <?php
                    if (isset($_GET['famille_id'])) {
                        // Correction : appel de getProduits (avec un s)
                        $produits = getProduits($_GET['famille_id']);
                        $count = 0;
                        foreach($produits as $row) {
                            // Attention : utilise id et nom (structure produit corrigée)
                            echo "<option value='{$row['id']}'>{$row['nom']}</option>";
                            $count++;
                        }
                        if($count == 0) echo "<option disabled>Aucun produit dans cette catégorie</option>";
                    }
                    ?>
                </select>
                <br><br>
                
                <a href="?page=famille"><button type="button" class="btn-secondary">← Retour Familles</button></a>
                
                <input type="submit" value="Valider Panier" class="btn-success">
                <button type="reset" class="btn-danger">Annuler</button>
            </form>
        </div>

    <?php elseif ($page === 'compte'): ?>
        <div class="box">
            <h3>Espace Clients</h3>
            <ul>
                <?php
                foreach(getAdherent() as $a) {
                    echo "<li>👤 " . htmlspecialchars($a['nom']) . "</li>";
                }
                ?>
            </ul>
            <br>
            <a href="?page=accueil"><button class="btn-secondary">Retour</button></a>
        </div>

    <?php elseif ($page === 'gestion'): ?>
        <div class="box">
            <h3>Admin BDD</h3>
            <p>Tables système :</p>
            <ul>
                <li>📂 adherent</li>
                <li>📂 client</li>
                <li>📂 commande</li>
                <li>📂 famille</li>
                <li>📂 fournisseur</li>
                <li>📂 produit</li>
            </ul>
            <a href="?page=accueil"><button class="btn-secondary">Retour</button></a>
        </div>

    <?php endif; ?>

</div>
</body>
</html>