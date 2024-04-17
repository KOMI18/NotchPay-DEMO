<?php 
if ($response) {
           
              sleep(50);
              
                $curl = curl_init();
// ici on recupere toutes les transactions
                curl_setopt_array($curl, [
                  CURLOPT_URL => "https://api.notchpay.co/payments",
                  CURLOPT_RETURNTRANSFER => true,
                  CURLOPT_ENCODING => "",
                  CURLOPT_MAXREDIRS => 10,
                  CURLOPT_TIMEOUT => 30,
                  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                  CURLOPT_CUSTOMREQUEST => "GET",
                  CURLOPT_HTTPHEADER => [
                    "Authorization: $publicKey",
             
                  ],
                ]);
                
                $response = curl_exec($curl);
                $res = json_decode($response);
                $compteur = 0 ;
                // on verifie si la transaction actuelle est dans la liste des transaction et qu'elle a un status complete
                foreach ($res->items as $item) {
                    if ($item->status == "complete" && $item->reference == $referencepay) {
                    //    on insert dans la tranaction dans la base de donner si necceassaire ( exple de code avec myqli)
						try {
							
							// Insertion dans la table transactions
							$insert_query = $connexion->prepare("INSERT INTO transactions (send_id, receive_id, type_transaction, montant_transaction, compte_credite) VALUES (?, ?, ?, ?, ?)");
							$insert_query->bind_param("iissi", $send_id, $receive_id, $type_transaction, $montant_transaction, $compte_credite);
							$insert_query->execute();
			
							// Valider la transaction MySQL
							$connexion->commit();
							$success_message = "Transaction effectuée avec succès.";
						} catch (Exception $e) {
							// En cas d'erreur, annuler la transaction MySQL
							$connexion->rollback();
							$error = "Erreur lors de la transaction : " . $e->getMessage();
						} finally {
							// Fermeture de la requête
							$insert_query->close();
						}

                }
               
            } 
          
            } else {
            echo "Erreur de préparation de la requête INSERT INTO.";
            }