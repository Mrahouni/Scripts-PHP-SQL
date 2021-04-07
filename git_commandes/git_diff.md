---
title: Diff
category: Git
---

```shell
# Affiche la différence entre le contenu du dernier commit et celui du 
# répertoire de travail. Cela correspond à ce qui serait commité par git commit -a.
git diff HEAD

# Affiche la différence entre le contenu pointé par A et celui pointé par B.
git diff A B

# Diff entre un dossier présent sur deux branches
git diff master..MA_BRANCH chemin/vers/mon_dossier
```