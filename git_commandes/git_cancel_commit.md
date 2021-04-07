---
title: Annuler commit
category: Git
---

## Annuler commits (soft)

Seul le commit est retiré de Git ; vos fichiers, eux, restent modifiés. Vous pouvez alors à nouveau changer vos fichiers si besoin est et refaire un commit.

### Annuler le dernier commit

```shell
git reset HEAD^
```

Pour indiquer à quel commit on souhaite revenir, il existe plusieurs notations :

* HEAD : dernier commit ;
* HEAD^ : avant-dernier commit ;
* HEAD^^ : avant-avant-dernier commit ;
* HEAD~2 : avant-avant-dernier commit (notation équivalente) ;
* d6d98923868578a7f38dea79833b56d0326fcba1 : indique un numéro de commit ;
* d6d9892 : indique un numéro de commit version courte.

## Annuler commits (hard)

Si vous voulez annuler votre dernier commit et les changements effectués dans les fichiers, il faut faire un reset hard. *Cela annulera sans confirmation tout votre travail !*

### Annuler les commits et perdre tous les changements

```shell
git reset --hard HEAD^
```

### Annuler les modifications d’un fichier avant un commit

Si vous avez modifié plusieurs fichiers mais que vous n’avez pas encore envoyé le commit et que vous voulez restaurer un fichier tel qu’il était au dernier commit :

```shell
git checkout nom_du_fichier

# GIT --version 2.23
git restore nom_de_ma_branch
```

### Annuler/Supprimer un fichier avant un commit

Supposer que vous venez d’ajouter un fichier à Git avec `git add` et que vous vous apprêtez à le « commiter ». Cependant, vous vous rendez compte que ce fichier est une mauvaise idée et vous voulez annuler votre `git add`.

Il est possible de retirer un fichier qui avait été ajouté pour être « commité » en procédant comme suit :

```shell
git reset HEAD -- nom_du_fichier_a_supprimer
```
