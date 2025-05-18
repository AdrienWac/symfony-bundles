Liste des méthodes utilisées du Genrator
=> generateClass : 
    - gestion des class_data ? ( à investiguer)
    - définie le chemin relatif de destination du fichier de classe
        Exemple: 'src/Domain/UseCase/Foo/Bar.php' 
    - définie les variables utilisées par le template de fichier de classe
        Génère les valeurs de class_name et namespace
    - Ajoute l'opération via l'opération add_operation
    - Retourne le chemin relatif du fichier de classe

=> addOperation:
    - Vérifie que le fichier de class n'existe pas
    - Créé le chemin relatif du fichier de classe
    - Récupère le chemin du fichier de template et vérifie l'existence du fichier de template
    - Ajouter les éléments au tableau des opérations à réaliser

=> writeChanges:
    - Pour chaque opérations à réaliser
      - Si le fichier de template ne contient rien
        - On créé le fichier et on passe à l'opération suivante
      - Sinon on créé le fichier avec le contenu du template
    - On réinitialise le tableau des opérations à réaliser

=> getFileContentsForPendingOperation
    - Vérifie que le fichier est bien dans la liste des opérations à réaliser
    - Merge le tableau des paramètres du template avec le chemin relatif du fichier de classe
    - Parse le template avec le file manager