Mister Gitlab
=============

# Introduction

Mister Gitlab est un outil développé en interne dont l'objectif est de pouvoir définir des éléments de mesure pour la qualité d'une "Merge Request".

Cela permettrait d'évaluer l'évolution de l'équipe et les axes d'amélioration.

# Les Threads

## Les Sévérités

Chaque "Thread" ne peut avoir qu'un seul niveau de sévérité à la fois.

|  **Severités** | **Description**                                                      |
|---------------:|----------------------------------------------------------------------|
|      **ALERT** | Doit être corrigé avant MEP.                                         |
|    **WARNING** | Devrait être corrigé dès que possible.                               |
| **SUGGESTION** | Peut améliorer la qualité du code et/ou de l'expérience utilisateur. |

## Les Catégories

Chaque "Thread" peut appartenir à plusieurs catégories.

|  **Catégories** | **Description**                                                                                                               |
|----------------:|-------------------------------------------------------------------------------------------------------------------------------|
|    **Sécurité** | Peut être sur une exposition de données ou juste permettre à un utilisateur malveillant d'obtenir des droits supplémentaires. |
| **Performance** | Peut être la cause de ralentissement sur tout ou partie du projet.                                                            |
|  **Lisibilité** | Manque de clarté dans l'intention du code. Doit être refactorisé/renommé/déplacé. Peut aussi être une source de confusion.    |
|        **Typo** | Une [typo](https://dictionary.cambridge.org/fr/dictionnaire/anglais/typo) tout simplement                                     |
| **Maintenance** | Va causer des soucis de maintenabilité dans un futur à court, moyen ou long terme.                                            |
|     **Qualité** | Ne correspond pas à nos conventions ou à celles définies par la communauté. (implique toutes les autres catégories)           |
|   **Stabilité** | Peut causer des soucis de stabilité et de fiabilité dans le runtime applicatif. (Exemple : division par 0)                    |

# Les Metrics

| **Nom**                      | **Description**                                                            | **Values**                               | **Utilité**                                   |
|------------------------------|----------------------------------------------------------------------------|------------------------------------------|-----------------------------------------------|
| Review To Fix Time (RTFT)    | Temps entre écriture du commentaire et implémentation de la résolution     | One of (Court, Acceptable, Long, Jamais) | Réactivité, Compréhension, Maturité technique |
| Number of Threads            | Nombre de threads ouvert                                                   | Doit rester < 30                         | Maintenance, Lisibilité                       |
| Thread / Files Ratio         | Ratio entre le nombre de threads ouverts et le nombre de fichiers modifiés | Doit rester < 1                          | Qualité, Lisibilité                           |
| Lines / Files Ratio          | Ratio entre la somme des lignes modifiés et le nombre de fichiers modifiés | Devrait rester < 40                      | Qualité, Lisibilité                           |
| Files Changed                | Nombre de fichiers changés                                                 | Devrait rester < 30                      | Qualité, Lisibilité                           |
| Lines Added                  | Nombre de lignes ajoutées                                                  | Devrait rester < 500                     | Qualité, Lisibilité                           |
| Lines Removed                | Nombre de lignes supprimées                                                | Devrait rester < 500                     | Qualité, Lisibilité                           |
| Replies per Thread Ratio     | Nombre de réponses / nombre de thread                                      | Devrait rester < 2.5                     | Qualité, Compréhension, Maturité technique    |
| Alert Ratio                  | Nombre de threads de type "ALERT" / Nombre de threads                      | Devrait rester == 0                      | Qualité, Sécurité, Stabilité                  |
| Warning Ratio                | Nombre de threads de type "WARNING" / Nombre de threads                    | Devrait rester < 0.5                     | Qualité, Sécurité, Stabilité                  |
| Readability Ratio            | Nombre de threads de catégorie "READABILITY" / Nombre de threads           | Devrait rester < 1                       | Qualité, Lisibilité, Maintenance              |
| Security Ratio               | Nombre de threads de catégorie "Sécurité" / Nombre de threads              | Devrait rester == 0                      | Qualité, Sécurité, Stabilité                  |
| Number of unresolved threads | Nombre de Threads non 'resolved'.                                          | Doit rester == 0                         | Maintenance, Qualité, Maturité technique      |

# Mister Gitlab

Pour que cet outil soit utile il faut rajouter des balises sur les commentaires de Merge Request.

Faisons déjà la différence entre l'ouverture d'un thread et une réponse à celui-ci.
Mister Gitlab ne se base que sur les labels présents dans le commentaire d'ouverture du thread, pas dans ses réponses.

Les balises (ou labels) doivent être sous l'une des formes suivantes :

- forcément entre `[` et `]`
- plusieurs paires de `[` et `]` peuvent être définies
- les labels peuvent être séparés par des `,` avec ou sans espaces
- peuvent mixer `severity` et `categories`
- sont insensibles à la casse et aux accents
- doivent faire partie des enums suivantes
    - severity:
        - alert
        - warning
        - suggestion
    - categories:
        - security, securite
        - performance
        - maintenability, maintenabilite, maintenance
        - stability, stabilite
        - quality, qualite
        - readability, lisibilite, question, questions
        - typo

**Multiples balises**

```md
[suggestion][security][performance] my comment
```

**Multiples éléments dans une balise**

```md
[suggestion,security][performance] my comment
```

**Multiples éléments au travers d'un commentaire**

```md
my [suggestion] is to use x instead of y to avoid any [performance] issue
```

# How to install

// TODO

# How to configure

In the `./user-config/` directory create as many files you need to configure your personal access tokens from gitlab with `api` scope. Each file must have this structure:

```json
{
    "name": "Gitlab xxxx",
    "host": "https://{gitlab.url}/",
    "token": "{gitlab.token}"
}
```

# How to use it

```bash
$ ./bin/console gitlab:merge-request:parse -h
```

## Tests

```shell
$ php -dmemory_limit=-1 ./vendor/bin/phpstan; 
$ ./vendor/bin/phpunit; 
$ ./vendor/bin/infection --only-covered;
```
