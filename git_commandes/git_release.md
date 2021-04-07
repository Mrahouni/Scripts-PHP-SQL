---
title: Créer une release
category: Git
---

Créer une nouvelle branche `release/aaaammyy_xx`

```bash
$ git checkout -b release/20140707_01 dev
```

Modifier le numéro de version dans le fichier racine READ.md.

```bash
$ git commit -a -m "Bumped version 20140707_01"
```

Se mettre sur la branche `dev` et :

```bash
$ git merge release/20140707_01
```

Pour finaliser la release, merger les corrections éventuelles sur la `master` et la `dev`

```bash
$ git checkout master && git merge --no-ff release/20140707_01 && git push origin master
$ git checkout dev && git merge --no-ff release/20140707_01 && git branch -d release/20140707_01 && git push origin dev
```

Marquage de la version sur la branch master

```bash
$ git tag -a 20140707_01 -m "New prod 20140707_01" master && git push origin master --tags
```
