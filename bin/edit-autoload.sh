#Tester que le fichier existe
echo $PWD
if [ -f '/composer.json' ]; then
    # Si le fichier existe, l'afficher
    echo "Le fichier ../composer.json existe."
else
    # Sinon, afficher un message d'erreur
    echo "Le fichier ../composer.json n'existe pas."
fi