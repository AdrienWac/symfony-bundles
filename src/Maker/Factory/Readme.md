Tu créé un use case Toto
 - Créer les "détails" 
	- Construire le chemin
        - Construire le namespace
        - Récupérer le chemin du template
        - Construire le fichier

Tu créé une Domain/.../Reques/TotoRequest
Tu créé un presenter Domain/API/.../TotoPresenter
Tu créé un response Domain/Response/.../TotoResponse


Je créé l'objet RequestFile
Je créé l'objet PresenterInterfaceFile
Je créé l'objet UseCaseFile

J'initialise la liste des fichiers à créé avec le UseCaseFile

Ask for create ResponseFile
    => Si oui
        => Je créé l'objet ResponseFile
        => J'ajoute l'objet ResponseFile à la liste des fichiers à créer

Ask for create PresenterInterfaceFile
    => Si oui
        => J'ajoute l'objet PresenterInterfaceFile à la liste des fichiers à créer

Ask for create RequestFile
    => Si oui
        => J'ajoute l'objet RequestFile à la liste des fichiers à créer
