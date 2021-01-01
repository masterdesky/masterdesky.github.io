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
For my project I've used the OpenFOAM stable build v8 released in June, 2020. This version of OpenFOAM is packaged for Ubuntu versions `16.04LTS`, `18.04LTS` and `20.04LTS`, but can be installed on other 64 bit distributions of Linux too using <a target="_blank" rel="noopener noreferrer" href="https://www.docker.com/">Docker</a>. The tested and supported Linux versions along with the full installation tutorial can be found on the <a target="_blank" rel="noopener noreferrer" href="https://openfoam.org/download/8-linux/">official page</a> of the OpenFOAM project. Since I'm using Kali Linux, which is built on top of Debian's "bullseye" ("testing") release, here I'll detail the installation method for Debian-based operating systems.

#### Step 1. Installing Docker
OpenFOAM v8 is currently required to be installed on Debian version 8 or above. Dependencies for other Linux distributions can be found in the official installation guide linked above. As I mentioned, on Linux distributions other than Ubuntu we need to run OpenFOAM in a Docker environment. I'm using Docker version `19.03.13` for this project, which itself requires to be installed on an OS with Linux kernel version at least `3.10` or above. The current dependencies can be always checked in Docker's <a target="_blank" rel="noopener noreferrer" href="https://docs.docker.com/engine/install/binaries/">documentations</a>. The kernel version can be easily looked up with the terminal command

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

OpenFOAM 8 can be downloaded using `wget` from the official <a target="_blank" rel="noopener noreferrer" href="http://dl.openfoam.org/">OpenFOAM download site</a>. For convenient execution from anywhere on the computer we can add it to the user's `PATH` environment variable. The following commands do everything for us: install OpenFOAM to the `/usr/bin/` system-wide directory and give execution permission to its launcher script `openfoam8-linux`:

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

My choice was to simulate a water droplet, falling into a vessel with still water at the bottom. The phenomenon is such a regular and everyday process, that there are probably no one, who is not familiar with it. We expect the water at the bottom of the vessel to splash back when the droplet hits the water surface, creating circular waves afterwards, which then bounce between walls. The droplet hitting the water, creates a hollow on the surface of the water for a small amount of time by pushing away the water in every direction from the impact location. This irregularity is filled back quickly due to the surface tension. The rapid and circularly symmetric movement of water particles makes them collide in the center of the impact location as surface tension pulls the pushed away water back to the middle. This collision pushes some water upwards in a violent (but a really small-scale) "explosion".

<div id="fig_1" class="post-image">
  <label>
    <input type="checkbox">
    <img src="/assets/images/posts/openfoam/hollow.webp">
    <figcaption>
      Fig. 1. An OpenFOAM simulation of a water droplet after hitting the surface of the water in a vessel. The size of the created hollow on the surface depends of a number of factors, like the velocity of the droplet or the viscosity of the fluid in the vessel and in the droplet. (The borders of the vessel in this image is rendered invisible.)
    </figcaption>
  </label>
</div>

My sole goal was to setup the simulation in a way that it produces an aesthetic, good-looking and realistic simulation of the described phenomenon and then visualize it in a clear and meaningful manner.

For this type of simulation a number of configuration had to be done beforehand. Namely the <i>mesh definition and generation</i>, the so-called <i>field generation</i> and its setup and tuning the general parameters of the simulation. In the following section I'll describe how the corresponding configuration files (so-called <i>dictionary files</i> in OpenFOAM) were set up for my project work.

## III. Create a mesh for a water droplet simulation in OpenFOAM
OpenFOAM offers the `blockMesh` utility for mesh generation. It is a versatile tool capable of the creation of meshes with arbitrary grading, curved edges and more as stated in the official <a target="_blank" rel="noopener noreferrer" href="https://cfd.direct/openfoam/user-guide/v6-blockmesh/">user guide</a>. The mesh for any simulation needs to be defined in the dictionary file named `blockMeshDict`. When `blockMesh` is executed it reads data from this file and then creates the appropriate mesh files called `points`, `faces`, `cells` and `boundary` in the same directory as `blockMeshDict`. In OpenFOAM there is nothing unusual in defining a mesh compared to other regular methods and techniques. However it offers us a wide variety of tools and pre-defined routines to easily fine-tune our mesh at will and to utilize OpenFOAM in plenty of complex use cases.

<div id="fig_2" class="post-image">
  <label>
    <input type="checkbox">
    <img src="/assets/images/posts/openfoam/coord_sys.webp">
    <figcaption>
      Fig. 2. A basic, possible mesh for an OpenFOAM simulation along with the vertex indeces. The basis of the 3D Cartesian coordinate system are also marked on the figure to help with orientation. This example is shown in the second section of OpenFOAM's official tutorial, describing the <a target="_blank" rel="noopener noreferrer" href="https://cfd.direct/openfoam/user-guide/v8-cavity/">cavity example</a>.
    </figcaption>
  </label>
</div>

As it was already mentioned, the `blockMeshDict` is one of the dictionary files used for the configuration of OpenFOAM simulations and it operates using just a handful of keywords. The mesh definition itself simply consist of giving appropriate values to the data tables denoted by each of these keywords.

### III.1. Keywords for the *blockMeshDict*
#### III.1.1. *convertToMeters*
The first keyword is `convertToMeters`, which is the scaling factor for the coordinate system. This keyword defines the length of a unit vector in meters. In my simulation I tried to explore a fairly small-scale phenomenon (compared to eg. the size of humans), therefore I defined the unit length as

```c++
convertToMeters                   0.025;
```
#### III.1.2. *vertices*
The second keyword is `vertices`, where one can define the 3 dimensional $(x,y,z)$ coordinates of the mesh's vertices as an array of vectors. OpenFOAM will only simulate points, sprites and physical quantities on the inside of the defined mesh. Everything leaving this mesh during the simulation, disappears.

For the water droplet project I defined a very simple, square-shaped vessel, with an open top side and some atmosphere over it. The atmosphere needs to be specified, because OpenFOAM will simulate particles only inside the defined mesh, even if a block of it is simply air. The mesh is a rectangular prism with height $4$-times longer than its base. The vessel height is resides in the bottom $1/4$ quarter of the mesh, while the atmosphere takes up the upper $3/4$ of it.

My `vertices` array in this setup was the following:

```c++
vertices
(
    // Bottom plane
    (0 0 0)  // 0
    (4 0 0)  // 1
    (4 0 4)  // 2
    (0 0 4)  // 3
    // Middle plane
    (0 4 0)  // 4 over 0
    (4 4 0)  // 5 over 1
    (4 4 4)  // 6 over 2
    (0 4 4)  // 7 over 3
    // Upper plane
    (0 16 0) // 8 over 4
    (4 16 0) // 9 over 5
    (4 16 4) // 10 over 6
    (0 16 4) // 11 over 7
);
```
The <i>"Bottom plane"</i> and <i>"Middle plane"</i> refers to the bottom and the top side of the vessel (where the top is completely open), while <i>"Upper plane"</i> refers to the top plane of the prism, which the simulation runs in.

#### III.1.3. *blocks*
The third keyword `blocks` is responsible for the definition of resolution and grading of an arbitrary volume in the mesh. In the context of OpenFOAM a <i>"block"</i> is always a hexahedra and its vertices are defined using the mesh vertices, detailed in the previous section. (There is a simple method how a block with less than 8 distinct vertices can be defined, but now I won't detail it here.)

An arbitrary number of blocks can be defined and each of the blocks needs to specified using the following three parameters:

<dl>
  <dt>Vertex numbering</dt>
  <dd>The list of vertex indeces, defining the hexahedra. This is always denoted with the `hex` keyword, followed by the list of $8$ appropriate vertex indeces inside a parenthesis. The order of indeces given should be always counter-clockwise. The defined block will be enclosed between two sides of the mesh, corresponding respectively to the first $4$ and the second $4$ vertices given for this array.</dd>
  <dt>Number of cells</dt>
  <dd>This entry corresponds to the resolution of the block in each directions. Values given as $(x, y, z)$ denotes the number of cells inside the block in the $x$, $y$ and $z$ directions respectively.</dd>
  <dt>Cell expansion ratios</dt>
  <dd>The expansion ratio enables the mesh to be graded, or refined, in specified directions. The ratio is that of the width of the end cell $\delta_{e}$ along one edge of a block to the width of the start cell $\delta_{s}$ along that edge as shown on Fig. 4.5 in official <a target="_blank" rel="noopener noreferrer" href="https://cfd.direct/openfoam/user-guide/v6-blockmesh/">user guide</a>. Two keywords can be used here, namely `simpleGrading` and `edgeGrading`, which specify one of the two grading specifications available in blockMesh.</dd>
</dl>

In my project two separate blocks were defined. The first one represents the square-shaped vessel with the open lid at the bottom of the mesh, while the second one is the free, open region over the vessel. The resolution of the mesh was completely uniform, and was $70 \times 70$ cells in the horizontal direction. Since the height of the mesh is 4-times its width, I set to have 280 cells along the vertical axis for the distribution of cells to be uniform. The definitions of my blocks can be seen below.
```c++
blocks
(
    hex (0 1 5 4 3 2 6 7) (70 70 70) simpleGrading (1 1 1)
    hex (4 5 9 8 7 6 10 11) (70 210 70) simpleGrading (1 1 1)
);
```

#### III.1.4. *boundary*
The `boundary` keyword is used to identify the patch faces of the mesh, define their types and order similar faces under the same, arbitrary keyword. The definition of a patch-group is made by specifying a user-selected name for the group and the type of the patches, and finally identify the faces for the patch group. This can be made similarly as the definition of `blocks` above.

Faces can be only defined between mesh vertices (same as for `blocks`). To define a mesh face we have to give the indeces of the corresponding mesh vertices in a counter-clockwise order. (Corresponding mesh vertices here means the vertices of the face itself.) For a given patch-group an arbitrary large number of faces can be assigned to, which really helps in grouping mesh faces of the same types.

My final list of patches for the water droplet project can be seen below. The keyword `lowerSideWalls` denotes the side walls of the vessel itself, while `upperSideWalls` stands for the borders of the upper $3/4$ of the mesh, namely the free, open region. Keyword `lowerWall` represents the bottom of the vessel, while the `atmosphere` defines the top plane of the mesh.

```c++
boundary
(
    lowerSideWalls
    {
        type wall;
        faces
        (
            (0 1 5 4)
            (1 2 6 5)
            (2 3 7 6)
            (3 0 4 7)
        );
    }
    upperSideWalls
    {
        type wall;
        faces
        (
            (4 5 9 8)
            (5 6 10 9)
            (6 7 11 10)
            (7 4 8 11)
        );
    }
    lowerWall
    {
        type wall;
        faces
        (
            (0 1 2 3)
        );
    }
    atmosphere
    {
        type patch;
        faces
        (
            (8 9 10 11)
        );
    }
);
```

#### III.1.5 Other keywords (*edges* and *mergePatches*)
These two other keywords can be used to fine-tune the mesh generation of any OpenFOAM simulation and they're offering a number of unique tools for the user. However because I didn't use them for my project, I won't detail the usage of them here. Refer to the official <a target="_blank" rel="noopener noreferrer" href="https://cfd.direct/openfoam/user-guide/v6-blockmesh/">user guide</a> for further information.

### III.2. Generating the mesh
After successfully defining the mesh, the appropriate (real) mesh files can be generated by using OpenFOAM's built-in utility, executed in the root of the project's folder:

```console
[session ID]: project_water> blockMesh
```

## IV. The configuration of other dictionary files for the simulation
### IV.1. *setFieldsDict*
The second most important configuration file is the `setFieldsDict` dictionary. This file is responsible to place specific materials, or set specific physical quantities in an arbitrary region inside the created mesh. Therefore this dictionary is probably the heart of a simulation in OpenFOAM, since this is responsible for the definition of the actually simulated quantities and fluids.

My setup for `setFieldsDict` can be seen below. First I've filled the whole mesh with the scalar field `alpha.water` set to $0$ everywhere. This ensured, that by default there are absolutely none water inside the mesh. Second I've defined two regions filled with water:
<dl>
  <dt>First region (Water in vessel)</dt>
  <dd>This region was definied using OpenFOAM's <code>boxToCell</code> keyword, which creates a rectangular water block in the bottom of the vessel with a base area of $0.1\,\mathrm{m} \times 0.1\,\mathrm{m}$ and with height of $0.04\,\mathrm{m}$.</dd>
  <dt>Second region (Water droplet)</dt>
  <dd>This region was definied using the <code>sphereToCell</code> keyword, which creates a shperical block of water over the center of the rectangular water surface. The center of the sphere is $0.38\,\mathrm{m}$ high, measured from the bottom of the vessel and its radius is $0.004\,\mathrm{m}$. The water droplet also has an initial velocity of $-2\,\mathrm{m}/\mathrm{s}$ along the vertical axis.</dd>
</dl>

```c++
defaultFieldValues
(
    volScalarFieldValue alpha.water 0
);

regions
(
    boxToCell
    {
        box (0 0 0) (0.1 0.04 0.1);
        fieldValues
        (
            volScalarFieldValue alpha.water 1
        );
    }
    sphereToCell
    {
        centre (0.05 0.38 0.05);
        radius 0.004;
        fieldValues
        (
            volScalarFieldValue alpha.water 1
            volVectorFieldValue U (0 -2 0)
        );
    }
);
```
Finishing up the `setFieldsDict` configuration file, after generating the mesh, the fields can also be generated by executing the following command in the root of the project's directory:

```console
[session ID]: project_water> setFields
```
<a target="_blank" rel="noopener noreferrer" href=""></a>
### IV.2. Other dictionary files
There are $4$ other dictionary files besides `blockMeshDict` and `setFieldsDict`: `controlDict`, `decomposeParDict`, `fvSchemes` and `fvSolution`.
<dl>
  <dt><a target="_blank" rel="noopener noreferrer" href="https://cfd.direct/openfoam/user-guide/v6-controldict/"><code>controlDict</code></a></dt>
  <dd>This dictionary file controls the interval of the I/O and the length of the simulation. As stated in the official <a target="_blank" rel="noopener noreferrer" href="https://cfd.direct/openfoam/user-guide/v6-controldict/">user guide</a>, only the time control and <code>writeInterval</code> entries are mandatory, every other entry uses its default value if not specified. In my project I've set <code>endTime</code> to $1.5$ and <code>writeInterval</code> to $0.005$. This ensured, that $1.5/0.005 = 300$ checkpoint files were created. Using this database a $10$ seconds long simulation with the frame rate of $30$ FPS can be constructed.</dd>
  <dt><a target="_blank" rel="noopener noreferrer" href="https://cfd.direct/openfoam/user-guide/v6-running-applications-parallel/"><code>decomposeParDict</code></a></dt>
  <dd>This file is responsible to control the parameters needed for an OpenFOAM simulation to be run in parallel. I'll detail its usage in section V.</dd>
  <dt><a target="_blank" rel="noopener noreferrer" href="https://cfd.direct/openfoam/user-guide/v6-fvschemes/"><code>fvSchemes</code></a></dt>
  <dd>This dictionary file sets the numerical schemes for terms and quantities, such as derivatives in equations, that are calculated during a simulation. In my project I'm using the template file found in the <code>interFoam/damBreak</code> tutorial without any changes.</dd>
  <dt><a target="_blank" rel="noopener noreferrer" href="https://cfd.direct/openfoam/user-guide/v6-fvsolution/"><code>fvSolution</code></a></dt>
  <dd>Called as the "Solution and algorithm control", this dictionary specifies the details about the solver methods, tolerances and algorithms used in a simulation. Similarly to <code>fvSchemes</code>, I used the file from the <code>interFoam/damBreak</code> tutorial.</dd>
</dl>

## V. Running an OpenFOAM simulation parallel using MPI
Running simulations in parallel using OpenFOAM is based on the technique of domain decomposition. In this framework the mesh and the simulated fields are broken down into numerous smaller pieces and these parts then are assigned to different processor cores to work with. As detailed in the official <a target="_blank" rel="noopener noreferrer" href="https://cfd.direct/openfoam/user-guide/v6-running-applications-parallel/">user guide</a>, the process of parallel computation involves: decomposition of mesh and fields; running the application in parallel; and, post-processing the decomposed case.

### V.1. Decomposition
The decomposition is performed by the `decomposePar` utility. The slicing of the geometry is specified by the parameters given in the `decomposeParDict` dictionary, By default, an example is presented in the `interFoam/damBreak` tutorial for the user to use as a template. During my project I used this particular configuration file for the water droplet simulation too. The content of the `decomposeParDict` dictionary used by me can be seen down below.

The file below shows, that the original geometry is decomposed into $4$ parts, determined by the `numberOfSubdomains` keyword using the decomposition method `simple`. This means, that the geometry is simply sliced into parts along the cardinal directions. The number of slices in this case is determined by the `simpleCoeffs` structure. Here the value of `n` is `(2 2 1)`, which indicates the geometry is sliced into $2$-$2$ parts along the $x$ and $y$ axis.

The `distributed` keyword determines whether the output files should be scattered on numerous disks or not, where the locations are specified by the `roots()` list. In my case I didn't used this part of the `decomposePar` utility, so I set the value of `distributed` to `no`.

```c++
numberOfSubdomains 4;

method          simple;

simpleCoeffs
{
    n               (2 2 1);
    delta           0.001;
}

distributed     no;

roots           ( );
```

After the dictionary file was appropriately setup, the decomposition can be started by running the utility inside the root of the project folder:

```console
[session ID]: project_water> decomposePar
```

### V.2. Running the simulation in parallel
Running the decomposed simulation on several cores in parallel is done by using a software implementation of the Message Passing Interface (MPI) standard. In my case I've used specifically <i>MPICH</i>, but eg. <i>openMPI</i> or other implementations can be also utilized. Using MPI is cannot be called as straightforward and it also offers us an overwhelmingly wide variety of tools and techniques useful for parallel computation. Detailing the usage of MPI is far beyond the scope of this document, so I'll only mention those details here that I've used during the project too. Any further informations regarding parallel computation in OpenFOAM can be accessed in the official <a target="_blank" rel="noopener noreferrer" href="https://cfd.direct/openfoam/user-guide/v6-running-applications-parallel/">user guide</a>.

Since I've sliced the mesh and fields into $4$ different parts, the simulation has to be allocated equally on $4$ concurrent processor cores. It should be mentioned, that I'm running a simulation on a single computer (while MPI would be also capable of executing it on a computer cluster) and I'm writing the output files on a single SSD. This two factors makes running an OpenFOAM simulation in parallel much easier, because the only aspect I was needed to give my attention to is to choose the correct number of processors and appropriate numerical solver method for my simulation.

As I've mentioned above I need to run the simulation exactly on $4$ processor cores, which can be set by using the `-np` flag running MPI. For the water droplet simulation I've had to use OpenFOAM's <a target="_blank" rel="noopener noreferrer" href="https://develop.openfoam.com/Development/openfoam/-/blob/master/applications/solvers/multiphase/interFoam/">`interFoam`</a> solver method, since I'm working with exactly two incompressible, isothermal immiscible fluids (namely water and air). Running a simulation with the `interFoam` solver in parallel using MPI with $4$ cores on a singular machine and also writing output files on a single SSD can be done by the following command executed in the root of the project's directory:

```console
[session ID]: project_water> mpirun -np 4 interFoam -parallel |& tee project_water.log &
```
In my case the creation of a $10$ seconds long simulation with a frame rate of $30$ FPS taken approximately $16+$ hours to finish. During the simulation, a file named `project_water.log` is also created containing the complete terminal output during the process. The output will be separated into $4$ separate set of time directories, each of the sets named as `processor_{N}`, where `N` denotes the index of the processor core, which processed the dataset found in that folder.

### V.3. Reconstruction
After a case has been run in parallel, it can be now reconstructed. This step is done by merging the sets of time directories from each `processor_{N}` directory into a single set of time directories. The `reconstructPar` utility performs such a reconstruction by executing the command:

```console
[session ID]: project_water> reconstructPar
```
When the data is distributed across several disks, it must be first copied to the local case directory for reconstruction. 

## VI. Results and visualization
It was already mentioned, that by default, OpenFOAM utilizes <i>ParaView</i> to visualize the output checkpoints of a simulation. After a simulation is finished, one can load the checkpoint files in ParaView by starting the utility `paraFoam`. This will open ParaView, and the setup of the visualization can be started.

```console
[session ID]: project_water> paraFoam
```
In the project I've simulated the time evolution of a number of physical quantities, like speed of particles or pressure, but most importantly the already mentioned quantity, denoted by `alpha.water`. This is the so-called <i>phase fraction</i> of water and air and denotes the proportion of water and air in a given location. If $\texttt{alpha.water} = 1$, then the volume in question is filled with water to a $100\,\%$. Similarly, if $\texttt{alpha.water} = 0$, there are no water in that location. Finally, if the value of `alpha.water` is between $0$ and $1$, water and air are mixed in that volume by the given proportion.

On the animation below I've visualized exactly this quantity. There we can see the contour line, where the `alpha.water` field is at least $0.001$. The borders of the vessel is also rendered invisible. The animation can be accessed on YouTube:

<iframe src="https://www.youtube.com/embed/OHyLuwYBTWo" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>