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

    if (!empty($id) && !empty($nname)) {
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
        .success { color: green; }
    </style>
</head>
<body>
    <h1>Datensatz in SQL Server einfügen</h1>

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
    <?php endif; ?>
</body>
</html>
