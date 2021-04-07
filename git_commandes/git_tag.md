---
title: Tag
category: Git
---

## Retourne la liste des tags disponible

```shell
git tag
# ou
git tag -l
git tag --list
```

## Créer un tag 

```shell
# `-m` permet d'ajouter un message associé au tag
git tag -a v1.0 -m "Mon commentaire pour la version 1.0"

# Ajouter un tag à partir d'un commit anterieur
git tag -a v1.0 -m "Mon commentaire pour la version 1.0" md5_commit
```

## Vérifier un tag en affichant les informations

```shell
git tag -v nom_de_l_étiquette
```

 ## Pousser les tags sur la branch distante

```shell
git push origin nom_de_l_étiquette

# Pousser plusieurs tags sur la branch
git push origin --tags
```
