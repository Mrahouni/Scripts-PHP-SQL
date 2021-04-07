---
title: Modifier l'historique
category: Git
---

## Modifier le message du dernier commit

```shell
git commit --amend
```

La commande ci-dessus charge le dernier message dans l'éditeur,

* Modifier message ;
* Enregistrer et quitter l'éditeur afin de prendre les modifs en compte.

## Ajouter des fichiers au dernier commit

```shell
git add mon_fichier
git commit --amend
```

## Éditer l'historique avec rebase

```shell
# Historique des trois derniers commits
git rebase -i HEAD~3
```

Ce qui va affichier ceci dans votre éditeur de texte.

```shell
pick 6cbdbe2 Message du commit 3
pick 0a75a2d Message du commit 2
pick da74a4e Message du commit 1

# Rebase b8d6fe1..da74a4e onto b8d6fe1 (3 commands)
#
# Commands:
# p, pick = use commit
# r, reword = use commit, but edit the commit message
# e, edit = use commit, but stop for amending
# s, squash = use commit, but meld into previous commit
# f, fixup = like "squash", but discard this commit's log message
# x, exec = run command (the rest of the line) using shell
# d, drop = remove commit
#
# These lines can be re-ordered; they are executed from top to bottom.
#
# If you remove a line here THAT COMMIT WILL BE LOST.
#
# However, if you remove everything, the rebase will be aborted.
#
# Note that empty commits are commented out
```

Chaque commit est précédé du mot `pick`, utiliser tous les mots-clés possibles.

* Pour éditer, un commit changer `pick` par `edit` ;
* Enregistrer et quitter l'éditeur afin de prendre les modifs en compte ;
* Et suivre les instructions.

Pour valider, les changements utiliser la commande :

```shell
git rebase NOM_DE_MA_BRANCH
```

Dans le cas, ou les commits modifier sont déjà présent sur la branch distante, il faura "forcer"

```shell
git push --force origin NOM_DE_MA_BRANCH
```
