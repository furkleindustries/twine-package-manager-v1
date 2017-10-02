# Twine Package Manager (TwinePM, TPM)

A package manager for modules, styles, and themes useful in writing hypertext fiction. Provides a communal space for the sharing and usage of code.

## Table of contents

* [Installation](#installation)
    * [Repositories](#repositories)
    * [Virtualization](#virtualization)
    * [Containerization](#containerization)
* [Subrepository Links](#subrepository-links)
* [Structure](#structure)
    * [Front-end](#front-end)
    * [Back-end](#back-end)

<a name="installation" id="installation"></a>
## Installation

<a name="repositories" id="repositories"></a>
### Repositories
To clone a repository, you'll first need [Git](https://git-scm.com). Within your system terminal or a separate shell (like Git Bash), type:

`git clone https://github.com/furkleindustries/twine-package-manager`

If you want to check out a non-master branch (e.g. if you're contributing to the development or documentation branches), navigate to the cloned directory and run:

`git checkout $MY_BRANCH`

where `$MY_BRANCH` is replaced by the name of the branch you wish to check out.

To clone the subrepositories that make up the bulk of the TwinePM code, you can either enter the cloned superrepository and run:

`./cloneRepositories`

or you can follow the steps outlined in [Virtualization](#virtualization).

<a name="virtualization" id="virtualization"></a>
### Virtualization
In order to guarantee the particulars of the development environment do not vary, TwinePM has configuration to build a virtual machine which installs all dependencies and spins up all containers. An additional advantage of this is not needing to install Docker and its filesystem dependencies on your computer. This virtualization occurs through the use of [Vagrant](https://www.vagrantup.com), a tool which allows building, provisioning, and management of VirtualBox virtual machines.

If you don't already have the Vagrant package, there are a couple options to install it. If you're on a Linux system which uses apt-get, run:

`apt-get install vagrant`

Otherwise, go to the previous link and follow the installation instruction.

When Vagrant is installed, navigate to the superrepository directory and run:

`./buildVm`

This will download the Ubuntu Xenial image for the virtual machine and provision the VM. Once this is complete, assuming there are no errors, run in the superproject directory:

`vagrant ssh`

If you modify the build system and want to rebuild the virtual machine to reflect this, run:

`./rebuildVm`

Note that this will delete all files on the VM and reproduce only the ones contained in the remote repositories or created by the Vagrantfile.

To remove all files created by the virtual machine, run:

`./destroyVm`

If you are editing a repository outside the VM, e.g. from your personal computer's OS, it is both easier and faster to ssh onto the VM and pull those changes, rather than reprovisioning the entire VM.

<a name="containerization" id="containerization"></a>
### Containerization

While the containers are built and run automatically in the development environment as a part of the provisioning of the virtual machine, you will still need to stop, remove, rebuild, etc. them as you make changes. There are several scripts in the [scripts subrepository](https://github.com/furkleindustries/twine-package-manager-scripts) which abstract these processes.

<a name="subrepository-links" id="subrepository-links"></a>
## Subrepository Links

Each subrepository, save for the build scripting repository, corresponds to a single container.

* [Front-end](https://github.com/furkleindustries/twine-package-manager-client)
* [Back-end business logic and request routing](https://github.com/furkleindustries/twine-package-manager-logic)
* [Back-end web server](https://github.com/furkleindustries/twine-package-manager-web)
* [On-disk permanent storage database configuration](https://github.com/furkleindustries/twine-package-manager-diskdb)
* [In-memory ephemeral storage database configuration](https://github.com/furkleindustries/twine-package-manager-memorydb)
* [Non-virtualization build scripting](https://github.com/furkleindustries/twine-package-manager-scripts)

<a name="structure" id="structure"></a>
## Structure

TwinePM is composed of four back-end servers, which manage data logic, persistence, and serving JSON and HTML endpoints; and a front-end server, which provides a rich HTML abstraction of nearly all the back-end's endpoints and the primary path for user-driven browser requests.

<a name="front-end" id="front-end"></a>
### Front-end
There are 6 discrete layers of the front-end.

1. Containerization - A single node:8.6 container is used for all front-end related services and dependencies.
2. Testing - Jest is used for unit tests. Integration tests are planned. Tests are run through the container on the CI server in order to standardize the testing environment.
3. Building - npm scripting is used to automate bundling a Node codebase through webpack, with transpilation performed by Babel, and additionally to perform testing of Javascript code.
4. HTTP - Content is served to the internet or a public-facing, reverse-proxying webserver using an Express server.
5. Rendering - The app is rendered into an initial, human-and-crawler-readable version by next.js.
6. Logic/View - The application itself is a React/Redux progressive web app using ES modules.

<a name="back-end" id="back-end"></a>
### Back-end
There are 7 discrete layers of the back-end.

1. Building - Written in Python 3.7, the top-level build scripting primarily uses the argparse and subprocess modules.
2. Containerization - Services in TwinePM are isolated and networked within Docker containers. This does not apply to Redis or PostgreSQL servers, which are currently slated to be hosted as Heroku add-ons. This provides file isolation, security increases, and automated building. Containerization is also planned to support clustering as the site scales, either through Docker Swarm (likely), Kubernetes (less likely), or Mesos (unlikely).
3. Testing - PHPUnit is used for unit tests. Integration tests are planned. As with the front-end testing, tests are run through the container on the CI server.
4. HTTP - nginx, contained within a Debian Stretch slim filesystem, is used to serve back-end requests. All requests (save for those for the front-end client, which are reverse-proxied to the Express server if they are sharing a server) are routed to a single file used for further logic routing.
5. Rendering - [Twig](https://twig.symfony.com/) is used to convert templates into HTML views.
6. Logic - A PHP-FPM server interpreting PHP 7.1, contained within a Debian Stretch slim filesystem, is used to intermediate between endpoint requests and persistent data stores. The logic layer is also responsible for authorization and maintaining information security. For requests that query PostgreSQL, the OAuth 2 implicit grant is used with JWT bearer tokens for API authentication and RSA-2048/RSA-SHA256 are used for encryption/message authentication. For requests that query Redis, AES-256-CTR/SHA-256 is used for encryption/message authentication. The PHP function random_bytes is used globally for cryptographically random request keys and salts. With the exception of the microclient endpoints, which emit HTML, every machine-readable endpoint is an idempotent REST interface using [CRUD](https://en.wikipedia.org/wiki/Create,_read,_update_and_delete) interfaces (POST, GET, PUT, and DELETE, respectively) and emits JSON. Each REST endpoint will soon possess an OPTIONS method displaying metadata about the endpoint and the service at large. All backend requests run through a single PHP file using the Slim microframework to route requests and pass a dependency injection container to Endpoint objects.
7. Persistence - Facilitated through two database servers, one for storing permanent or timed content stored on disk, and the other for caching ephemeral state in RAM. The former is a PostgreSQL server contained within a Debian Jessie filesystem, and the latter is a Redis server contained within a Debian Jessie slim filesystem.
