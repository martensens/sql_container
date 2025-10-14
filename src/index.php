 <?php
// --------------------
// Konfiguration
// --------------------
$server   = "mstisqlserver01.database.windows.net,1433"; // Unter Linux ggf. Host-IP wie 172.17.0.1
$database = "mstidatabase01";
$username = "mstiller";
$password = "Habenichts_01"; // Passwort anpassen

$pdo = null;
$connectError = "";
$message = "";

// --------------------
// Verbindung aufbauen
// --------------------
try {
    $dsn = "sqlsrv:Server=$server;Database=$database;Encrypt=yes;TrustServerCertificate=yes";
    $pdo = new PDO($dsn, $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    $connectError = "❌ Verbindung fehlgeschlagen: " . $e->getMessage();
}

// --------------------
// Formularverarbeitung
// --------------------
if ($pdo && $_SERVER["REQUEST_METHOD"] === "POST") {
    $id = $_POST["id"] ?? null;
    $nname = $_POST["NName"] ?? null;
    $city = $_POST["City"] ?? null;

    if (!empty($id) && !empty($nname) && !empty($city)) {
        try {
            $stmt = $pdo->prepare("INSERT INTO dbo.Customer (id, NName,City) VALUES (:id, :nname,:city)");
            $stmt->bindParam(":id", $id, PDO::PARAM_INT);
            $stmt->bindParam(":nname", $nname, PDO::PARAM_STR);
            $stmt->bindParam(":city", $city, PDO::PARAM_STR);
            $stmt->execute();
            $message = "✅ Datensatz erfolgreich eingefügt!";
        } catch (PDOException $e) {
            $message = "❌ Fehler beim Einfügen: " . $e->getMessage();
        }
    } else {
        $message = "⚠️ Bitte ALLE Felder ausfüllen!";
    }
}
?>
<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <title>Datensatz einfügen</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 2rem; }
        form { margin-bottom: 1rem; }
        label { display: block; margin-top: 0.5rem; }
        input { padding: 0.3rem; width: 200px; }
        button { margin-top: 1rem; padding: 0.5rem 1rem; }
        .msg { margin-top: 1rem; font-weight: bold; }
        .error { color: red; }
        .success { color: blueviolet; }
    </style>
</head>
<body>
    <h1>Datensatz in den SQL Server einfügen</h1>

    <?php if ($connectError): ?>
        <div class="msg error"><?= htmlspecialchars($connectError) ?></div>
    <?php else: ?>
        <form method="post">
            <label for="id">ID:</label>
            <input type="number" id="id" name="id" required>

            <label for="NName">Nachname:</label>
            <input type="text" id="NName" name="NName" required>

            <label for="City">City:</label>
            <input type="text" id="City" name="City" required>

            <button type="submit">Einfügen</button>
        </form>

        <?php if ($message): ?>
            <div class="msg <?= strpos($message, 'erfolgreich') !== false ? 'success' : 'error' ?>">
                <?= htmlspecialchars($message) ?>
            </div>
        <?php endif; ?>

        <?php if (strpos($message, 'erfolgreich') !== false): ?>
            <?php
            try {
                $stmt = $pdo->prepare("SELECT * FROM dbo.Customer");
                $stmt->execute();

                $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

                if ($rows && count($rows) > 0) {
                    echo "<h3>Alle Datensätze:</h3>";
                    echo "<table border='1' cellpadding='5' cellspacing='0'>";
                    echo "<tr><th>ID</th><th>Nachname</th><th>City</th></tr>";

                    foreach ($rows as $row) {
                        echo "<tr>";
                        echo "<td>" . htmlspecialchars($row['id']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['NName']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['City']) . "</td>";
                        echo "</tr>";
                    }

                    echo "</table>";
                } else {
                    echo "<p>Keine Datensätze gefunden.</p>";
                }
            } catch (PDOException $e) {
                echo "<p class='error'>❌ Fehler beim Abruf: " . htmlspecialchars($e->getMessage()) . "</p>";
            }
            ?>
        <?php endif; ?>
<?php endif; ?>
