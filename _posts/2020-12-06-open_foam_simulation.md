---
layout: post
author: Balázs Pál
title : Creating a water droplet simulation in OpenFOAM v8
date: 2020-12-06T09:22:00Z+02:00
featured-image: /assets/images/posts/openfoam/openfoam_thmbnl.webp
featured-image-alt: A screenshot from a water droplet simulation
categories: OpenFOAM
---
<b>
OpenFOAM (<i>Open-source Field Operation And Manipulation</i>) is an open source software, designed primarily to solve problems in three-dimensional continuum mechanics, especially in computational fluid dynamics, but the possibilities of OpenFOAM goes much beyond this. It also has several forks and three main lines of development. Here I'll talk about my project for the course Computer-Aided Modeling Laboratory @ ELTE using the official OpenFOAM build.
</b>

## I. Prerequisites and installing OpenFOAM on Linux
For my project I've used the OpenFOAM stable build v8 released in June, 2020. This version of OpenFOAM is packaged for Ubuntu versions `16.04LTS`, `18.04LTS` and `20.04LTS`, but can be installed on other 64 bit distributions of Linux too using [Docker](https://www.docker.com/). The tested and supported Linux versions along with the full installation tutorial can be found at the [official page](https://openfoam.org/download/8-linux/) of the OpenFOAM project. Since I'm using Kali Linux, which is built on top of Debian's "bullseye" ("testing") release, I'll detail the installation method for Debian-based operating systems.

#### Step 1. Installing Docker
OpenFOAM v8 is currently required to be installed on Debian version 8 or above. Dependencies for other Linux distributions can be found in the official installation guide linked above. As I mentioned, on Linux distributions other than Ubuntu we need to run OpenFOAM in a Docker environment. I'm using Docker version `19.03.13` for this project, which itself requires to be installed on an OS with Linux kernel version at least `3.10` or above. The current dependencies can be always checked in Docker's [documentations](https://docs.docker.com/engine/install/binaries/). The kernel version can be easily looked up with the terminal command

```console
user@hostname:~$ uname -r
```
If we made sure, that we have a supported Linux kernel version, then we can install Docker on Debian using the following commands:

```console
user@hostname:~$ sudo apt-get -y update
user@hostname:~$ curl -fsSL https://get.docker.com/ | sh
```
#### Step 2. Configure docker for user
Docker runs as <b>root</b> by default. If you want to enable it to a non-root user, we need to add the user to the `docker` group. It is a good safety practice to run our personal computer as a non-root user, so if that is the case, we need to execute the following command to include our user in the `docker` group and be able to use Docker:

```console
user@hostname:~$ sudo usermod -aG docker $(whoami)
```
Where the variable `$(whoami)` can be replaced with the appropriate username, which we want to permit the the usage of the docker to (eg. our own username).

#### Step 3. Installing OpenFOAM

OpenFOAM 8 can be downloaded using `wget` from the official [OpenFOAM download site](http://dl.openfoam.org/). For convenient execution we can add it to the user's `PATH` environment variable. The following commands do everything for us: install OpenFOAM to the `/usr/bin/` system-wide directory and give execution permission to its launcher script `openfoam8-linux`:

```console
user@hostname:~$ sudo sh -c "wget http://dl.openfoam.org/docker/openfoam8-linux -O /usr/bin/openfoam8-linux"
user@hostname:~$ sudo chmod 755 /usr/bin/openfoam8-linux
```

#### Step 4. Launching OpenFOAM
