---
title: Installation projet
category: Git
---


## Initialiser un projet

```shell
cd chemin/vers/mon_dossier
echo "# MON_PROJET" >> README.md
git init
git add README.md
git commit -m "Initial commit"
git remote add origin ....git
git push -u origin master
```

## Récuperer le repo sur Github

```shell
git clone ....git chemin/vers/mon_dossier
```

Mon repo est composé d'au moins deux branch.

develop : dédié au développement et résolution de bug.
master : reflète le code en production. Personne ne doit travailler directement sur cette branch.

Pour récupérer votre branch develop

```shell
git branch -a
git checkout origin/develop
git checkout -b develop origin/develop
git branch
```

## Developpement : Branch develop

## Prod : Branch Master

```shell
# On se met sur la branche master
git checkout master
# On merge la branche develop
git merge develop
# Pour pousser les changements
git push origin master
# Penser à revenir sur develop
git checkout develop
```
