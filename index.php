
<?php
// Vérifiez si le formulaire a été soumis
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Récupérez les données du formulaire
    $email = $_POST["email"];
    $telephone = $_POST["telephone"];
    $montant = $_POST["montant"];
    $reference = 'DEMO' . uniqid();
    $currency = "XAF" ;
    $donneesPaiement = [
        'amount' => $montant,
        'email' => $email,
        'reference' => $reference,
        'currency' => $currency,
        'description' => 'DEPOT D\'ARGENT'
    ];

   // Créer une ressource curl
    $ch = curl_init();

    // Paramètres de la requête curl pour l'initialisation du paiement
     $publicKey ='pk.DW6ASrbfvkp9vy57Sh9QdEsZv5kungOF9LnN7k3qy9fs7lA3iAKTsvxX1DVPBsj5cs0wgqZTu1YMdw6VoncXjrUUO33zyyoDLx3RV1hRwysCVa2wMiYNn0ENTuURv';
    $url = "https://api.notchpay.co/payments/initialize";
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $donneesPaiement);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Authorization:' . $publicKey,
        'Cache-Control: no-cache'
    ]);
    curl_setopt($ch,CURLOPT_RETURNTRANSFER, true); 

    // Exécuter la requête curl pour l'initialisation du paiement
    $result = curl_exec($ch);
    $responseArray = json_decode($result, true);
 
    $referencepay = isset($responseArray['transaction']['reference']) ? $responseArray['transaction']['reference'] : '';
  
   
  
    // Vérifier si l'initialisation du paiement a réussi
    if ($responseArray['status'] === 'Accepted') {
        // URL de l'API NotchPay pour compléter le paiement
        $paymentUrl = 'https://api.notchpay.co/payments/' . $referencepay;

        // Données pour compléter le paiement
        $paymentData = [
            'channel' => 'cm.mobile',
            'data' => [
                'phone' => $telephone
            ]
        ];

        // Convertir les données en format JSON
        $jsonData = json_encode($paymentData);

        // Paramètres de la requête curl pour compléter le paiement
        curl_setopt($ch, CURLOPT_URL, $paymentUrl);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'PUT');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Authorization: ' . $publicKey,
            'Accept: application/json',
            'Content-Type: application/json'
        ]);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $jsonData);
       
        // Exécuter la requête curl pour compléter le paiement
        $response = curl_exec($ch);
       $responseValid = json_decode($response, true);
   
        // {ajouter ici le contenue de script.php}
        
        } else {
            echo "Erreur lors de la complétion du paiement.";
        }
        // Fermer la ressource curl
    curl_close($ch);
    } 
   
    



?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Formulaire PHP avec Bootstrap</title>
    <!-- Bootstrap CSS -->
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container">
        <h2 class="text-center mt-5">Notch Pay</h2>
        <form action="process_form.php" method="post" class="mt-4">
            <div class="form-group">
                <label for="email">Email:</label>
                <input type="email" class="form-control" id="email" name="email" required>
            </div>
            <div class="form-group">
                <label for="telephone">Téléphone:</label>
                <input type="tel" class="form-control" id="telephone" name="telephone" required>
            </div>
            <div class="form-group">
                <label for="montant">Montant:</label>
                <input type="number" class="form-control" id="montant" name="montant" required>
            </div>
            <button type="submit" class="btn btn-primary">Envoyer</button>
        </form>
    </div>

    <!-- Bootstrap JS et jQuery (nécessaire pour les composants interactifs de Bootstrap) -->
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
