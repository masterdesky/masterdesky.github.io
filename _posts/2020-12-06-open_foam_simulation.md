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
For my project I've used the OpenFOAM stable build v8 released in June, 2020. This version of OpenFOAM is packaged for Ubuntu versions `16.04LTS`, `18.04LTS` and `20.04LTS`, but can be installed on other 64 bit distributions of Linux too using [Docker](https://www.docker.com/). The tested and supported Linux versions along with the full installation tutorial can be found on the [official page](https://openfoam.org/download/8-linux/) of the OpenFOAM project. Since I'm using Kali Linux, which is built on top of Debian's "bullseye" ("testing") release, here I'll detail the installation method for Debian-based operating systems.

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
Docker runs as <b>root</b> by default. If we want to enable its execution for a non-root user, we need to add the user to the `docker` group. It is a good safety practice to run our personal computer as a non-root user, so if that is the case, we need to execute the following command to include our user in the `docker` group and be able to use Docker:

```console
user@hostname:~$ sudo usermod -aG docker ${USER}
```
Where the variable `${USER}`{:.console} can be replaced with the appropriate username, which we want to permit the the usage of the docker to (eg. our own username). For this change to take effect the user should log out and log back in!

#### Step 3. Installing OpenFOAM

OpenFOAM 8 can be downloaded using `wget` from the official [OpenFOAM download site](http://dl.openfoam.org/). For convenient execution from anywhere on the computer we can add it to the user's `PATH` environment variable. The following commands do everything for us: install OpenFOAM to the `/usr/bin/` system-wide directory and give execution permission to its launcher script `openfoam8-linux`:

```console
user@hostname:~$ sudo sh -c "wget http://dl.openfoam.org/docker/openfoam8-linux -O /usr/bin/openfoam8-linux"
user@hostname:~$ sudo chmod 755 /usr/bin/openfoam8-linux
```

#### Step 4. Launching OpenFOAM
In the previous step we installed the Docker version of `openfoam8-linux` executable. When it's launched, it starts inside a Docker container that mounts the directory from where `openfoam8-linux` is launched by default, however mounting the user’s `$HOME` directory isn't allowed. As detailed in the official installation guide, "the mounted directory is represented in the container environment by the `WM_PROJECT_USER_DIR` environment variable, which is set to `$HOME/OpenFOAM/${USER}-8` by default". This means, that it is recommended to start `openfoam8-linux` from inside this `$HOME/OpenFOAM/${USER}-8` directory. Here the variable `${USER}` stands for the executing user's username.

```console
user@hostname:~$ mkdir -p $HOME/OpenFOAM/${USER}-8
user@hostname:~$ cd $HOME/OpenFOAM/${USER}-8
user@hostname:~/OpenFOAM/${USER}-8$ openfoam8-linux
```
The variable `$HOME` can be of course also replaced with the `~` symbol in the commands above.

#### Step 5. Testing OpenFOAM
The official installation guide presents the standard way of testing, whether OpenFOAM was successfully installed or not. All projects (including this test project) should be placed and executed inside the `run` directory, represented with the `$FOAM_RUN` variable. First we need to create this directory:

```console
[session ID]: ~> mkdir -p $FOAM_RUN
```
The `session ID` here refers to the Docker session, which the application is launched in. In every new launch it will be a randomly generated line of alphabetical and numeral characters. It has no deeper meaning.

Next we download a simple test case and place it in its own directory inside the `run` folder. Because every project needs to be placed under the `run` folder it is obviously a good practice to place every project in a new directory under `run`. After this step we're generating the mesh for the simulation using the `blockMesh` routine, run the simulation with `simpleFoam` and finally visualize the series of the created checkpoint files using `paraFoam`:

```console
[session ID]: ~> cd $FOAM_RUN
[session ID]: run> cp -r $FOAM_TUTORIALS/incompressible/simpleFoam/pitzDaily .
[session ID]: run> cd pitzDaily
[session ID]: pitzDaily> blockMesh
[session ID]: pitzDaily> simpleFoam
[session ID]: pitzDaily> paraFoam
```
The routine `paraFoam` opens the ParaView application, which is used as the primary visualization software for OpenFOAM.

#### Step 6. Exit Docker and OpenFOAM
If the steps above executed successfully and every component of OpenFOAM works, we can close OpenFOAM and the container using simply an exit command:

```console
[session ID]: ~> exit
```

## II. Project description for water droplet simulation
OpenFOAM is primarily designed to create simulations fluid dynamics and thus it is optimized to solve numerical problems in this topic. My task during the semester for the Computer-Aided Modeling Laboratory at ELTE was to get known with the application on a basic level and create an arbitrary simulation using it as my project work. Given the fact that OpenFOAM is made for fluid dynamics simulations, I wanted to do a simulation somehow closely related to this topic.

My choice was to simulate a water droplet, falling into a vessel with still water at the bottom. The phenomenon is such a regular and everyday process, that there are probably no one, who is not familiar with it. We expect the water at the bottom of the vessel to splash back, when the droplet hits the water surface, creating circular waves afterwards, which then bounce between walls.

My sole goal was to setup the simulation in a way that it produces is an aesthetic, good-looking and realistic simulation of the described phenomenon and visualize it in a clear and meaningful manner.

## III. Create a mesh for a water droplet simulation in OpenFOAM

## IV. The configuration of other dictionary files for the simulation

## V. Results and visualization