<?php

session_start();

$grilles = [
    [
        [0, 0, 0, 0, 0, 0, 0, 0, 0, 0],
        [0, 1, 1, 1, 1, 1, 1, 0, 0, 0],
        [0, 1, 0, 0, 1, 0, 1, 0, 1, 0],
        [0, 1, 0, 0, 1, 0, 1, 0, 1, 0],
        [0, 1, 0, 0, 1, 0, 1, 0, 1, 0],
        [0, 1, 0, 0, 1, 0, 1, 0, 1, 0],
        [0, 1, 0, 0, 1, 0, 0, 0, 1, 0],
        [0, 1, 0, 1, 1, 1, 0, 1, 1, 0],
        [0, 0, 0, 0, 0, 0, 0, 0, 0, 0]
    ],
    [
        [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0],
        [0, 1, 1, 1, 1, 1, 1, 0, 0, 0, 0, 0],
        [0, 1, 0, 0, 1, 0, 1, 0, 1, 0, 0, 0],
        [0, 1, 0, 0, 1, 0, 1, 0, 1, 0, 1, 0],
        [0, 1, 0, 0, 1, 0, 1, 0, 1, 0, 1, 0],
        [0, 1, 0, 0, 1, 0, 1, 0, 1, 0, 1, 0],
        [0, 1, 0, 0, 1, 0, 0, 0, 1, 0, 1, 0],
        [0, 1, 1, 1, 1, 1, 0, 1, 1, 0, 1, 0],
        [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 1, 0],
        [0, 0, 0, 0, 1, 1, 0, 0, 1, 0, 0, 0],
        [0, 0, 0, 1, 1, 0, 1, 1, 0, 0, 0, 0],
        [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0]
    ],
    [
        [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0],
        [0, 1, 1, 1, 1, 1, 1, 0, 0, 0, 0, 0, 0, 0],
        [0, 1, 0, 0, 1, 0, 1, 0, 1, 0, 0, 0, 1, 0],
        [0, 1, 0, 0, 1, 0, 1, 0, 1, 0, 1, 0, 0, 0],
        [0, 1, 0, 0, 1, 0, 1, 0, 1, 0, 1, 1, 1, 0],
        [0, 1, 0, 0, 1, 0, 1, 0, 1, 1, 1, 0, 0, 0],
        [0, 1, 0, 0, 1, 0, 0, 0, 1, 1, 0, 0, 1, 0],
        [0, 1, 1, 1, 1, 1, 0, 1, 1, 0, 1, 0, 0, 0],
        [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 1, 0, 1, 0],
        [0, 0, 0, 0, 1, 1, 0, 0, 1, 0, 1, 0, 1, 0],
        [0, 0, 0, 1, 1, 0, 1, 1, 0, 1, 0, 0, 0, 0],
        [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0]
    ]
];

$msg = ""; // Ensure $msg is initialized

// Fonction pour obtenir la grille du jeu selon le score 
function getGameGrid($score)
{
    global $msg; // Make $msg accessible inside the function
    if ($score < 5) {
        $msg = "c'est parti, montre ce que tu sais faire";
        return $GLOBALS['grilles'][0];
    } elseif ($score < 10) {
        $msg = "tu commence a bien te debrouiller";
        return $GLOBALS['grilles'][1];
    } else {
        $msg = "tu es un pro";
        return $GLOBALS['grilles'][2];
    }
}

$_SESSION['msg'] = $msg;

if (!isset($_SESSION['score'])) {
    $_SESSION['score'] = 0;
}

if (!isset($_SESSION['playerPos'])) {
    $_SESSION['playerPos'] = [1, 1];
}

if (!isset($_SESSION['mousePos'])) {
    $_SESSION['mousePos'] = getRandomAccessiblePosition(getGameGrid($_SESSION['score']), $_SESSION['playerPos']);
}

if (isset($_POST['restart'])) {
    session_unset(); 
    header("Location: index.php"); 
    exit;
}

// Traitement du formulaire
if ($_SERVER['REQUEST_METHOD'] === 'POST' && !isset($_POST['restart'])) {
    $direction = $_POST['direction'] ?? null;
    $newPlayerPos = movePlayer($_SESSION['playerPos'], $direction, getGameGrid($_SESSION['score']));

    $_SESSION['playerPos'] = $newPlayerPos;

    if ($_SESSION['playerPos'] === $_SESSION['mousePos']) {
        $_SESSION['mousePos'] = getRandomAccessiblePosition(getGameGrid($_SESSION['score']), $_SESSION['playerPos']);
        $message = '<p class="success">Bravo! Vous avez attrapé la souris!</p>';
        $_SESSION['score']++;
    }
}

// Fonction pour déplacer le joueur 
function movePlayer($playerPos, $direction, $grid)
{
    $newPos = $playerPos;
    switch ($direction) {
        case 'up':
            $newPos[0]--;
            break;
        case 'down':
            $newPos[0]++;
            break;
        case 'left':
            $newPos[1]--;
            break;
        case 'right':
            $newPos[1]++;
            break;
    }

    if ($newPos[0] >= 0 && $newPos[0] < count($grid) && $newPos[1] >= 0 && $newPos[1] < count($grid[0]) && $grid[$newPos[0]][$newPos[1]] == 0) {
        return $newPos;
    }

    return $playerPos;
}

// Fonction pour obtenir une position aléatoire accessible
function getRandomAccessiblePosition($grid, $playerPos)
{
    $accessiblePositions = [];
    foreach ($grid as $rowIndex => $row) {
        foreach ($row as $colIndex => $cell) {
            if ($cell == 0 && !($rowIndex == $playerPos[0] && $colIndex == $playerPos[1])) {
                $accessiblePositions[] = [$rowIndex, $colIndex];
            }
        }
    }
    return $accessiblePositions[array_rand($accessiblePositions)];
}

// Fonction pour générer les nuages
function generateClouds($play, $playerPos)
{
    $clouds = [];
    $cloudRadius = 2;
    $rows = count($play);
    $cols = count($play[0]);

    for ($i = 0; $i < $rows; $i++) {
        for ($j = 0; $j < $cols; $j++) {
            if (abs($playerPos[0] - $i) > $cloudRadius || abs($playerPos[1] - $j) > $cloudRadius) {
                $clouds[$i][$j] = true;
            } else {
                $clouds[$i][$j] = false;
            }
        }
    }

    return $clouds;
}

// Fonction pour afficher le jeu et les nuages
function displayPlayWithClouds($play, $playerPos, $mousePos)
{
    $clouds = generateClouds($play, $playerPos);
    $output = '<table border="1" cellspacing="0" cellpadding="10" style="text-align: center;">';

    foreach ($play as $rowIndex => $row) {
        $output .= '<tr>';
        foreach ($row as $colIndex => $cell) {
            $output .= '<td>';

            if ($clouds[$rowIndex][$colIndex]) {
                $output .= '☁️';
            } elseif ($playerPos[0] == $rowIndex && $playerPos[1] == $colIndex) {
                $output .= '🐱';
            } elseif ($mousePos[0] == $rowIndex && $mousePos[1] == $colIndex) {
                $output .= '🐭';
            } elseif ($cell == 1) {
                $output .= '🧱';
            } else {
                $output .= '⬜';
            }

            $output .= '</td>';
        }
        $output .= '</tr>';
    }

    $output .= '</table>';

    return $output;
}

?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="./assets/css/style.css">
    <title>Jeu du chat et de la souris</title>
</head>

<body>
    <header>
        <h1>Jeu du Chat et de la Souris</h1>
        <nav>
            <ul>
                <li><a href="acceuil.html">Accueil</a></li>
                <li><a href="index.php">Jouer</a></li>
                <li><a href="regles.html">Règles</a></li>
            </ul>
        </nav>
    </header>

    <main>
        <section class="container">
            <h2>Que le jeu commence</h2>

            <h3><?php if (isset($message)) echo $message; else echo '<p>Attrapez la souris!</p>'; ?></h3>
            <p>Score : <?php echo $_SESSION['score']; ?></p>

            <article class="playArea">
                <div class="direction">
                    <h3>Direction</h3>
                    <p>Déplacez le chat avec les boutons ci-dessous</p>
                    <form method="post">
                        <div id="haut"> <button type="submit" name="direction" value="up">⬆️</button><br></div>
                        <div id="gauche"><button type="submit" name="direction" value="left">⬅️</button> </div>
                        <div id="droite"> <button type="submit" name="direction" value="right"> ➡️ </button><br> </div>
                        <div id="bas"><button type="submit" name="direction" value="down">⬇️</button> </div>
                    </form>
                    <form method="post">
                        <button type="submit" name="restart">Redémarrer</button>
                    </form>
                </div>
                <div id="jeu">
                    <?php echo displayPlayWithClouds(getGameGrid($_SESSION['score']), $_SESSION['playerPos'], $_SESSION['mousePos']); ?>
                </div>
                
                <p><?php echo $msg ?> </p>
            </article>
        </section>
    </main>

    <footer>
        <p>&copy; 2024 Jeu du chat et de la souris. Tous droits réservés.</p>
    </footer>
    <script src="./assets/js/main.js"></script>
</body>

</html>