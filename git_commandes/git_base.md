---
title: Principales commandes
category: Git
---

## Status des fichiers

```shell
git status
```

## Lister les branchs

```shell
git branch -a
```

`*`sur la branche courante.

## Créer une branch

```shell
# Deux lignes: créer et basculer sur la nouvelle branch
git branch nom_de_ma_branch_nouvelle
git checkout nom_de_ma_branch_nouvelle

# Une seule ligne: créer et basculer
git checkout -b nom_de_ma_branch_nouvelle
```

## Supprimer une branch

```shell
# Si la branch est local et n'est pas créée sur le repo distant
git branch -d nom_de_ma_branch_local

# Si la branch est présente sur le repo distant
git push origin --delete nom_de_ma_branch_distante
```

## Changer de branch

```shell
git checkout nom_de_ma_branch

# GIT --version 2.23
git switch nom_de_ma_branch
```

## Premier commit

```shell
git add .
git commit -m "initial commit"
```

## Commit suivant

```shell
git add chemin_vers_mon_fichier
git commit -m "message du commit"
```

## Annuler le dernier commit et modifs

```shell
git reset --hard md5_commit
git push --force
```

## Antidaté un commit.

```shell
git add .
GIT_AUTHOR_DATE="2015-12-12 08:32 +100" git commit -m "Commit antidaté"
```

## Mettre à jour le dépôt local

```shell
git pull
```

## Mettre à jour le dépôt local d'une branch spécifique

```shell
git pull origin MA_BRANCH
```

## Envoyer ses commits vers le dépôt distant

```shell
git push
```

## Envoyer ses commits vers le dépôt distant sur une branch spécifique

```shell
git push origin MA_BRANCH
```

## Supprimer un fichier du répertoire de travail et de l'index

```shell
git rm nom_du_fichier
```

## Supprimer un fichier de l'index

```shell
git rmg --cached nom_du_fichier
```
