# Twine Package Manager (TwinePM, TPM)
A package manager for modules, styles, and themes useful in writing hypertext fiction. Provides a communal space for the sharing and usage of code.

## Table of contents

* [Installation](#installation)
    * [Repositories](#repositories)
    * [Virtualization](#virtualization)
* [Structure](#structure)
    * [Front-end](#front-end)
    * [Back-end](#back-end)

<a name="installation" id="installation"></a>
## Installation

<a name="repositories" id="repositories"></a>
### Repositories
To clone a repository, you'll first need [Git](https://git-scm.com/). Within your system terminal or Git Bash, type:

`git clone --recursive https://github.com/furkleindustries/twine-package-manager`

One doesn't typically use `--recursive` when cloning projects. In this case, that usage is necessary because the top-level TwinePM superproject contains six submodules, and submodules are not cloned by default.

If you find the cloning of the superproject takes too long, try:

`git clone --recursive -j7 https://github.com/furkleindustries/twine-package-manager`

This will parallelize the cloning process.

<a name="virtualization" id="virtualization"></a>
### Virtualization
In order to guarantee the particulars of the development environment, TwinePM has configuration to build a virtual machine which installs all dependencies and spins up all containers. An additional advantage of this is not needing to install Docker and its filesystem dependencies on your computer. This virtualization occurs through the use of Vagrant, a tool which allows building and provisioning of VirtualBox.

<a name="structure" id="structure"></a>
## Structure

TwinePM is composed of four back-end servers, which manage data logic, persistence, and an HTML utility microclient; and a front-end server, which provides a rich web abstraction of nearly all the back-end's endpoints and the primary path for user-driven browser requests.

<a name="front-end" id="front-end"></a>
### Front-end
There are 4 discrete layers of the front-end. All are contained within a single Node container.

1. Building - npm scripting is used to automate bundling a Node codebase through webpack, with transpilation performed by Babel. Testing is performed on this level but is only applied to the logic level.
2. HTTP - Content is served to the internet or a public-facing, reverse-proxy webserver using an Express server.
3. Rendering - The app is rendered into a starting, human-readable version by next.js.
4. Logic/View - The application itself is a React/Redux progressive web app using ES modules. Testing is performed with Jest. Integration tests are planned, probably in PhantomJS.

<a name="back-end" id="back-end"></a>
### Back-end
There are 6 discrete layers of the back-end. The first two are only planned to be present in the development environment.

1. Building - Written in Python 3.7, the top-level build scripting primarily uses the argparse and subprocess modules. Testing is initiated at this level but is only performed on the logic layer.
2. Virtualization - Virtualizes the entire development (or, less usefully and currently unused, production) environment, using a Vagrantfile (written in Ruby) Ubuntu 16.04 image to creating a VirtualBox virtual machine through.
3. Containerization - Services in TwinePM is isolated and networked within Docker containers. This does not apply to Redis or PostgreSQL servers, which are currently slated to be hosted as Heroku add-ons. This provides file isolation, security increases, and automated building. Containerization is also planned to support clustering as the site scales, either through Docker Swarm (likely), Kubernetes (less likely), or Mesos (unlikely).
4. HTTP - nginx, contained within a Debian Stretch slim filesystem, is used to serve back-end requests. All requests (save for those for the front-end client, which are reverse-proxied to the Express server if they are sharing a server) are routed to a single file used for further logic routing.
5. Logic - A PHP-FPM server interpreting PHP 7.1, contained within a Debian Stretch slim filesystem, is used to intermediate between endpoint requests and persistent data stores. The logic layer is also responsible for authorization and maintaining information security. For requests that query PostgreSQL, the OAuth 2 implicit grant is used with JWT bearer tokens for API authentication and RSA-2048/RSA-SHA256 are used for encryption/message authentication. For requests that query Redis, AES-256-CTR/SHA-256 is used for encryption/message authentication. The PHP function random_bytes is used globally for cryptographically random request keys and salts. With the exception of the microclient endpoints, which emit HTML, every machine-readable endpoint is an idempotent REST interface using CRUD (POST, GET, PUT, and DELETE, respectively) and emits JSON. Each REST endpoint will soon possess an OPTIONS method displaying metadata about the endpoint and the service at large. All backend requests run through a single PHP file using the Slim microframework to route requests and pass a dependency injection container to Endpoint objects. Testing is performed with phpunit.
6. Persistence - Facilitated through two database servers, one for storing permanent or timed content stored on disk, and the other for caching ephemeral state in RAM. The former is a PostgreSQL server contained within a Debian Jessie filesystem, and the latter is a Redis server contained within a Debian Jessie slim filesystem.
