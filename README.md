[![Contributors][contributors-shield]][contributors-url]
[![MIT License][license-shield]][license-url]


<!-- PROJECT LOGO -->
<br />
<p align="center">
  <a href="https://github.com/othneildrew/Best-README-Template">
    <img src="images/logo.png" alt="Logo" width="80" height="80">
  </a>

  <h3 align="center">Handi CV</h3>

  <p align="center">
    Refonte et migration du site vers Symfony 5.2
    <br />
    <a href="https://github.com/othneildrew/Best-README-Template"><strong>Voir la documentation »</strong></a>
    <br />
    <br />
    <a href="https://github.com/othneildrew/Best-README-Template">Voir la demo</a>
    ·
    <a href="https://github.com/othneildrew/Best-README-Template/issues">Voir le code</a>
  </p>
</p>



<!-- TABLE DES MATIERES -->
<details open="open">
  <summary>Table des matières</summary>
  <ol>
    <li>
      <a href="#about-the-project">A propos du projet</a>
      <ul>
        <li><a href="#built-with">Technologies</a></li>
      </ul>
    </li>
    <li>
      <a href="#getting-started">Pour démarrer</a>
      <ul>
        <li><a href="#prerequisites">Pré-requis</a></li>
        <li><a href="#installation">Installation</a></li>
      </ul>
    </li>
    <li><a href="#usage">Usage</a></li>
    <li><a href="#roadmap">Roadmap</a></li>
    <li><a href="#contributing">Contribuer</a></li>
    <li><a href="#license">License</a></li>
    <li><a href="#contact">Contact</a></li>
    <li><a href="#acknowledgements">Remerciements</a></li>
  </ol>
</details>



<!-- ABOUT THE PROJECT -->
## A propos du projet

[![Product Name Screen Shot][product-screenshot]](https://example.com)

Le projet de refonte de HandiCV consiste à faire évoluer le site vers la technologie Symfony, afin de le mettre au diapason avec les autres plateformes développées en interne.
Pour rappel, le site HandiCV, développé sur le CMS Drupal, a été mis en place par Philippe en 2004.
Au fil des années, le site est devenu une référence en matière de l'emploi et le handicap. En plus de centraliser les offres d'emploi ouvertes aux personnes en situation d'handicap, il répertorie les événements les plus en vue du secteur.
Le site propose également beaucoup de contenus didactiques sur le recrutement de personnes handicapées et des actualités y relatif.

Les étapes du projet:
* Analyse de la structuration actuelle du site et son contenu.
* Définition exhaustive des fonctionnalités de la nouvelle version.
* Développement par module avec tests fonctionnels.

### Développé avec

La nouvelle version repose sur les technologies suivantes:
* [Symfony](https://symfony.com)
* [Twig](https://twig.symfony.com/)
* [Bootstrap](https://getbootstrap.com)
* [JQuery](https://jquery.com)



<!-- DEMARRAGE -->
## Démarrage
Le framework Symfony nécessite npm ou yarn (gestionnaires de paquets officiel de Node.js) pour installer les dépendances, et php.
Le choix s'étant porté sur la dernière version de Symfony (5.2), la version 7.4 (au moins) de php est nécessaire.
Il est recommandé d'installer également Composer (Composer est un logiciel gestionnaire de dépendances libre écrit en PHP)

### Prérequis

Voici les
* npm
  ```sh
  npm install npm@latest -g
  ```
  php 7.4
  ```sh
  sudo apt install php libapache2-mod-php http://www.php.net/downloads.php
  ```
  Composer
  ```sh
  php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');"
  php -r "if (hash_file('sha384', 'composer-setup.php') === '756890a4488ce9024fc62c56153228907f1545c228516cbf63f885e036d37e9a59d27d63f46af1d4d07ee0f76181c7d3') { echo 'Installer verified'; } else { echo 'Installer corrupt'; unlink('composer-setup.php'); } echo PHP_EOL;"
  php composer-setup.php
  php -r "unlink('composer-setup.php');"
  ```

### Installation

1. Installation du projet symfony (en mode complet)
   ```sh
   composer create-project symfony/skeleton handicv
   ```
2. Création de la base de données et liaison avec les entités
   ```sh
   php bin/console doctrine:database:create
   php bin/console make:migration
   php bin/console doctrine:migrations:migrate
   ```
3. Mise à jour des dépendances
   ```sh
   npm install
   ```
4. Installation de Faker et génération de fausses données
   ```sh
   composer require --dev orm-fixtures
   composer require fzaninotto/faker
   composer req string
   ```
5. Installation de Bootstrap
   ```sh
   npm install sass-loader sass webpack --save-dev
   npm install postcss-loader autoprefixer --dev
   npm install bootstrap@next
   npm i @popperjs/core
   ```
6. Installation de Bootstrap
   ```sh
   composer require symfony/mailer
   composer require symfony/http-client
   composer require symfony/google-mailer
   ```
7. Installation de l'authentification avec Google
   ```sh
   composer require knpuniversity/oauth2-client-bundle
   composer require league/oauth2-google
   ```
8. Installation de EasyAdmin
   ```sh
   composer require easycorp/easyadmin-bundle
   symfony console make:admin:dashboard.css
   symfony console make:admin:crud
   ```

9. Export de données au format pdf
   ```sh
   composer require dompdf/dompdf
   ```

10. Automatisation des slugs
    ```sh
    composer req antishov/doctrine-extensions-bundle
    ```

11. Extension Twig
    ```sh
    composer require twig/string-extra
    ```

12. Gestionnaire de fichiers Vich et ckeditor
    ```sh
    composer require vich/uploader-bundle
    composer require friendsofsymfony/ckeditor-bundle
    php bin/console ckeditor:install
    php bin/console assets:install public
    ```

13. Voter de sécurisation d'acces aux contenus
   ```sh
   symfony console make:voter
   ```

14. Notifications par mail/SMS
   ```sh
   composer require symfony/notifier
   composer require symfony/twig-pack twig/cssinliner-extra twig/inky-extra
   ```

15. Pagination
   ```sh
   composer require knplabs/knp-paginator-bundle
   ```

16. Tests
   ```sh
   symfony console make:test
   php bin/phpunit
   ```
17. UX
   ```sh
   composer require symfony/ux-chartjs
   composer require symfony/ux-dropzone
   ```

17. Stripe / Paiement en ligne
   ```sh
   composer require stripe/stripe-php
   ```

<!-- USAGE EXAMPLES -->
## Usage

Utilisez cet espace pour montrer des exemples utiles de la façon dont un projet peut être utilisé. Des captures d'écran supplémentaires, des exemples de code et des démos fonctionnent bien dans cet espace. Vous pouvez également créer un lien vers plus de ressources.

_Pour plus d'examples, référez-vous à [Documentation](https://example.com)_



<!-- ROADMAP -->
## Roadmap

Voir les [questions ouvertes](https://github.com/othneildrew/Best-README-Template/issues) pour une liste des fonctionnalités proposées (et bugs connus).



<!-- CONTRIBUTING -->
## Contributing

Les contributions sont ce qui fait de la communauté open source un endroit incroyable pour apprendre, inspirer et créer. Toute contribution que vous apportez est grandement appréciée**.

1. Fork the Project
2. Create your Feature Branch (`git checkout -b feature/AmazingFeature`)
3. Commit your Changes (`git commit -m 'Add some AmazingFeature'`)
4. Push to the Branch (`git push origin feature/AmazingFeature`)
5. Open a Pull Request



<!-- LICENSE -->
## License

Distribué sous la License MIT. Voir `LICENSE` for plus d'informations.



<!-- CONTACT -->
## Contact

Yves Ininahazwe - yves.ininahazwe@talents-handicap.com
Salah Bahri - salah.bahri@talents-handicap.com

Project Link: [https://github.com/your_username/repo_name](https://github.com/your_username/repo_name)



<!-- ACKNOWLEDGEMENTS -->
## Acknowledgements
* [Philippe](https://www.webpagefx.com/tools/emoji-cheat-sheet)



<!-- MARKDOWN LINKS & IMAGES -->
<!-- https://www.markdownguide.org/basic-syntax/#reference-style-links -->
[contributors-shield]: https://img.shields.io/github/contributors/othneildrew/Best-README-Template.svg?style=for-the-badge
[contributors-url]: https://github.com/othneildrew/Best-README-Template/graphs/contributors
[forks-shield]: https://img.shields.io/github/forks/othneildrew/Best-README-Template.svg?style=for-the-badge
[forks-url]: https://github.com/othneildrew/Best-README-Template/network/members
[stars-shield]: w?style=for-the-badge
[stars-url]: https://github.com/othneildrew/Best-README-Template/stargazers
[issues-shield]: https://img.shields.io/github/issues/othneildrew/Best-README-Template.svg?style=for-the-badge
[issues-url]: https://github.com/othneildrew/Best-README-Template/issues
[license-shield]: https://img.shields.io/github/license/othneildrew/Best-README-Template.svg?style=for-the-badge
[license-url]: https://github.com/othneildrew/Best-README-Template/blob/master/LICENSE.txt
[linkedin-shield]: https://img.shields.io/badge/-LinkedIn-black.svg?style=for-the-badge&logo=linkedin&colorB=555
[linkedin-url]: https://linkedin.com/in/othneildrew
[product-screenshot]: images/screenshot.png
