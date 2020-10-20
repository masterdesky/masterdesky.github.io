---
layout: post
author: Balázs Pál
title : Installing and running GADGET-2 on Linux - 2020
date: 2020-10-12T00:10:00Z+02:00
featured-image: ../assets/images/posts/gadget2/millennium.jpg
featured-image-alt: The famous Millennium Simulation of GADGET2 from 2005
---
<b>
The GADGET-2 (<i>GAlaxies with Dark matter and Gas intEracT</i> - 2) is the name given to the second iteration of the software written by Volker Springel for cosmological N-body and SPH simulations. Here I'll summarize the steps, how to download, install and run the software on a Linux computer.
</b>

## I. Prerequisites
### Download necessary files
First of all, if you want to work with the GADGET-2 software, I advise you to consider using a computer running under Linux or MacOS. However it is possible to install GADGET-2 on Windows (somehow), it is much easier to do it on Linux, or Mac. Also in this tutorial, all the steps will be done on a Linux computer. Since there are numerous different Linux distributions out there, and because I'm particularly running Kali Linux, there is a slight chance for errors and anomalies to occur on systems other than mine. This tutorial is completely optimized to run on my system in particular, but $99.9\%$ that it will run on other Linux systems too. Also I'll only go into details of the manual installation from sources, without using Homebrew, or other similar package managers (except for HDF5).

As it is noted in the [User guide](https://wwwmpa.mpa-garching.mpg.de/gadget/users-guide.pdf) of GADGET-2, there are some non-standard libraries, which you'll need to install first to compile GADGET-2 successfully. Download the following libraries and softwares:
1. [Gadget 2.0.7](https://wwwmpa.mpa-garching.mpg.de/gadget/gadget-2.0.7.tar.gz) (Latest version)
2. [MPICH >1.0](http://www.mpich.org/static/downloads/3.3.2/mpich-3.3.2.tar.gz) (3.3.2 here, latest version currently)
3. [GSL 1.9](ftp://ftp.gnu.org/gnu/gsl/gsl-1.9.tar.gz) (Didn't tested later versions)
4. [FFTW 2.1.5](http://www.fftw.org/fftw-2.1.5.tar.gz) (Needed for MPI capability)
5. HDF5 >5.0 (Downloaded and installed from terminal, detailed in the next section)

### Install prerequisites
First you'll need to extract all the (currently only 4) downloaded zipped files. Considering, that you've downloaded them in the directory `~/Downloads/`, you can unzip all packages by the following commands after opening a terminal there:
```bash
user@hostname:~/Downloads/$ tar -xzf gadget-2.0.7.tar.gz
user@hostname:~/Downloads/$ tar -xzf mpich-3.3.2.tar.gz
user@hostname:~/Downloads/$ tar -xzf gsl-1.9.tar.gz
user@hostname:~/Downloads/$ tar -xzf fftw-2.1.5.tar.gz
```

After you unzipped all packages, you can delete the `.tar.gz` files for now, they won't needed anymore.

#### a) Building MPI 3.3.2
First you need to build MPICH, the original software implementation of the Message Passing Interface (MPI) standard. It is needed for the compilation both of the FFTW library, and for the GADGET-2 as well. You can follow the [Install guide](https://www.mpich.org/static/downloads/3.3/mpich-3.3-installguide.pdf) provided at the official website of the MPICH software, but here I'll summarize all the essential steps you need to install MPICH on your computer.

First go inside your unzipped `mpich-3.3.2` directory, which should be situated inside the `~/Downloads/` folder after the previous steps:

```bash
user@hostname:~/Downloads/$ cd mpich-3.3.2
user@hostname:~/Downloads/mpich-3.3.2/$ 
```
After that, you need to run the configuration file of the package and write the output into a file named `c.txt` to make diagnostics easier in the case of an error.

```bash
user@hostname:~/Downloads/mpich-3.3.2/$ ./configure |& tee c.txt
```
If you want to specify an arbitrary installation location for MPICH, you can do this by adding the `-prefix` tag to the configuration line in the following way:

```bash
user@hostname:~/Downloads/mpich-3.3.2/$ ./configure -prefix=/path/to/mpich-install |& tee c.txt
```
Throughout the tutorial I'll use the default install location for MPICH. For other configuration options refer to the Install guide above.

After the configuration you can finally build MPICH with `make` and write the terminal logs to a file called `m.txt`, again, for the purpose of diagnostics in case of build errors:

```bash
user@hostname:~/Downloads/mpich-3.3.2/$ make |& tee m.txt
```
If you change your mind before installation and want to alter the configuration before install, you can always revert the effect of this command first by typing 

```bash
user@hostname:~/Downloads/mpich-3.3.2/$ make clean
```
If the build was successful and you want to finalize the installation (and also saving the terminal logs), you can run the following command:

```bash
user@hostname:~/Downloads/mpich-3.3.2/$ sudo make install |& tee mi.txt
[sudo] password:
```

You should now add MPICH the your `$PATH` variable to get recognized by the system anywhere on your computer. On Linux the default location for MPICH should be `/usr/local/`, which means if you followed the tutorial without specifying an installation location, you should add MPICH to `$PATH` as the following:

```bash
user@hostname:~/Downloads/mpich-3.3.2/$ export PATH=/usr/local/bin:$PATH
```
Else you should change `/usr/local/` in the code above to the arbitrary installation location chosen by you:

```bash
user@hostname:~/Downloads/mpich-3.3.2/$ export PATH=/path/to/mpich-install/bin:$PATH
```
You can now test the success of the installation by the typing the following two commands (the expected outputs are also displayed here):

```bash
user@hostname:~/Downloads/mpich-3.3.2/$ which mpicc
/path/to/mpich-install/bin/mpicc
user@hostname:~/Downloads/mpich-3.3.2/$ which mpiexec
/path/to/mpich-install/bin/mpiexec
```
You can now exit the `mpich-3.3.2` directory by typing

```bash
user@hostname:~/Downloads/mpich-3.3.2/$ cd ..
user@hostname:~/Downloads/$ 
```

#### b) Building GSL 1.9
Next the GNU Scientific Library (GSL) needs to be installed, which provides myriads of useful functions for `C/C++` programmers in scientific environment. Because GSL is not part of the `C/C++` standard, and because GADGET-2 also uses functions from this library, it needs to be installed beforehand compiling GADGET-2.

First open the unzipped library `gsl-1.9`:

```bash
user@hostname:~/Downloads/$ cd gsl-1.9
user@hostname:~/Downloads/gsl-1.9/$ 
```
Next - similarly to the MPICH installation - we run the configuration file (optionally select installing location), build the package with `make`, then install it.

```bash
user@hostname:~/Downloads/gsl-1.9/$ ./configure |& tee c.txt
```
or adding a specific install location:

```bash
user@hostname:~/Downloads/gsl-1.9/$ ./configure -prefix=/path/to/gsl-install |& tee c.txt
```
Next build the library:

```bash
user@hostname:~/Downloads/gsl-1.9/$ make |& tee m.txt
```
If the build finished without any errors you can finally install it:

```bash
user@hostname:~/Downloads/gsl-1.9/$ sudo make install |& tee mi.txt
[sudo] password:
```
If the installation was successful, you can finally exit the directory.

#### c) Building FFTW 2.1.5
The so-called "<i>Fastest Fourier Transform in the West</i>"" (FFTW) is a high-profile `C` subroutine library designed to compute discrete Fourier transform as efficiently as possible. To properly build the library to be used by GADGET-2, there are some extra configuration options, which need to be passed to the initial configuration routine of FFTW.

First open the `fftw.2.1.5` directory:

```bash
user@hostname:~/Downloads/$ cd fftw.2.1.5
user@hostname:~/Downloads/fftw.2.1.5/$ 
```
Similarly to the MPICH and GSL installation, first you should run the configuration file, and optionally adding the `-prefix` switch to it to specify place of installation.

```bash
user@hostname:~/Downloads/fftw-2.1.5/$ ./configure -prefix=/path/to/install-fftw\
                                 --enable-mpi --enable-type-prefix --enable-float |& tee c.txt
```
Omit the `-prefix` switch to install FFTW to the default location. Here the `\` marker means, this code is actually a single line, but it would overflow the margin of the page without splitting it into two rows. Also, FFTW is needed to use the `--enable-mpi` switch during the configuration, that's one of the reasons, why that should be installed first.

Again, similarly to the previous installations, build the FFTW library with `make` and then install it as `sudo`:

```bash
user@hostname:~/Downloads/fftw-2.1.5/$ make |& tee m.txt
user@hostname:~/Downloads/fftw-2.1.5/$ sudo make install |& tee mi.txt
[sudo] password:
```
If the installation finished successfully, you can quit this directory too.

#### d) Installing HDF5

Installing HDF5 on Debian-based distributions (including Ubuntu) could be done by the following command from terminal:

```bash
user@hostname:~/ sudo apt-get install libhdf5-dev
[sudo] password:
user@hostname:~/ sudo apt-get update
[sudo] password:
```

## II. Installing GADGET-2
After completing the following part of the tutorial, GADGET-2 will be installed inside the already unzipped `Gadget-2.0.7` directory. If you want to install it outside the `~/Downloads` folder, this is the best time to move it to its final location. Create a new directory eg. in your home directory named `GADGET-2` and copy all the contents of `Gadget-2.0.7` into this folder. After that, open the `GADGET2/Gadget2` folder in the terminal.

```bash
user@hostname:~/ mkdir GADGET2
user@hostname:~/ cp Downloads/Gadget-2.0.7/* GADGET2/
user@hostname:~/ cd GADGET2/Gadget2
```
GADGET-2 itself has a lot of compilation parameters, which needs to be edited first to build the software correctly. Open the `Makefile`, situated here with an editor tool of your choice (vim/nano/gedit/emacs/etc.). You'll need to focus on the first part of this file, since there are the compilation parameters and switches situated. This first part could be split into two smaller pieces. The first one is where you can set the options used by this particular GADGET-2 build in your upcoming simulations. Edit your `Makefile` by activating/deactivating options with `#` symbols, to make it look like the table below. You need to add the line

```make
OPT   +=  -DH5_USE_16_API
```
to the table manually, because it is not present in the `Makefile` by default. It is sometimes needed to omit errors, which arises from the lack of backward compatibility in newer HDF5 builds. The table which you need to replicate is the following:

```make
#----------------------------------------------------------------------
# From the list below, please activate/deactivate the options that     
# apply to your run. If you modify any of these options, make sure     
# that you recompile the whole code by typing "make clean; make".      
#                                                                      
# Look at end of file for a brief guide to the compile-time options.   
#----------------------------------------------------------------------


#--------------------------------------- Basic operation mode of code
#OPT   +=  -DPERIODIC 
OPT   +=  -DUNEQUALSOFTENINGS


#--------------------------------------- Things that are always recommended
OPT   +=  -DPEANOHILBERT
OPT   +=  -DWALLCLOCK   


#--------------------------------------- TreePM Options
#OPT   +=  -DPMGRID=128
#OPT   +=  -DPLACEHIGHRESREGION=3
#OPT   +=  -DENLARGEREGION=1.2
#OPT   +=  -DASMTH=1.25
#OPT   +=  -DRCUT=4.5


#--------------------------------------- Single/Double Precision
#OPT   +=  -DDOUBLEPRECISION      
#OPT   +=  -DDOUBLEPRECISION_FFTW      


#--------------------------------------- Time integration options
OPT   +=  -DSYNCHRONIZATION
#OPT   +=  -DFLEXSTEPS
#OPT   +=  -DPSEUDOSYMMETRIC
#OPT   +=  -DNOSTOP_WHEN_BELOW_MINTIMESTEP
#OPT   +=  -DNOPMSTEPADJUSTMENT


#--------------------------------------- Output 
OPT   +=  -DHAVE_HDF5  
OPT   +=  -DH5_USE_16_API 
#OPT   +=  -DOUTPUTPOTENTIAL
#OPT   +=  -DOUTPUTACCELERATION
#OPT   +=  -DOUTPUTCHANGEOFENTROPY
#OPT   +=  -DOUTPUTTIMESTEP


#--------------------------------------- Things for special behaviour
#OPT   +=  -DNOGRAVITY     
#OPT   +=  -DNOTREERND 
#OPT   +=  -DNOTYPEPREFIX_FFTW        
#OPT   +=  -DLONG_X=60
#OPT   +=  -DLONG_Y=5
#OPT   +=  -DLONG_Z=0.2
#OPT   +=  -DTWODIMS
#OPT   +=  -DSPH_BND_PARTICLES
#OPT   +=  -DNOVISCOSITYLIMITER
#OPT   +=  -DCOMPUTE_POTENTIAL_ENERGY
#OPT   +=  -DLONGIDS
#OPT   +=  -DISOTHERM_EQS
#OPT   +=  -DADAPTIVE_GRAVSOFT_FORGAS
#OPT   +=  -DSELECTIVE_NO_GRAVITY=2+4+8+16

#--------------------------------------- Testing and Debugging options
#OPT   +=  -DFORCETEST=0.1


#--------------------------------------- Glass making
#OPT   +=  -DMAKEGLASS=262144
```

The next part of the file consist of information about compilation parameters and includes. Here you'll need to tell the compiler, where did you install the GSL, FFTW and HDF5 libraries to include them in the build. An example for the final look of the table can be seen below. In the `Select target computer` block, feel free to add a new entry with an arbitrary name and deactivate all other active `SYSTYPE` variables. I denoted this as

```make
SYSTYPE="mycomputer"
```
but it can be named anything else. Next, using the other entries as templates, you should create a new entry with the `SYSTYPE` changed to `mycomputer` in `Adjust settings for target computer` block like this:

```make
ifeq ($(SYSTYPE),"mycomputer")

(...)

endif
```
In this section you have to tell the compiler the absolute path to all the libraries above, which you've downloaded. If you installed all these libraries to their default location, you should find them in similar places, as they're occur in the table below. The exact locations may vary from system to system, but these are the absolute paths on my Kali Linux 2020.3 build.

```make
#----------------------------------------------------------------------
# Here, select compile environment for the target machine. This may need 
# adjustment, depending on your local system. Follow the examples to add
# additional target platforms, and to get things properly compiled.
#----------------------------------------------------------------------

#--------------------------------------- Select some defaults

CC       =  mpicc               # sets the C-compiler
OPTIMIZE =  -O2 -Wall -g        # sets optimization and warning flags
MPICHLIB =  -lmpich


#--------------------------------------- Select target computer

SYSTYPE="mycomputer"
#SYSTYPE="MPA"
#SYSTYPE="Mako"
#SYSTYPE="Regatta"
#SYSTYPE="RZG_LinuxCluster"
#SYSTYPE="RZG_LinuxCluster-gcc"
#SYSTYPE="OpteronMPA"
#SYSTYPE="OPA-Cluster32"
#SYSTYPE="OPA-Cluster64"


#--------------------------------------- Adjust settings for target computer

ifeq ($(SYSTYPE),"mycomputer")
CC       =  mpicc   
OPTIMIZE =  -O3 -Wall
GSL_INCL =  -I/usr/local/include
GSL_LIBS =  -L/usr/local/lib
FFTW_INCL=  -I/usr/local/include
FFTW_LIBS=  -L/usr/local/lib
MPICHLIB =  -L/usr/local/lib
HDF5INCL =  -I/usr/lib/x86_64-linux-gnu/hdf5/serial/include
HDF5LIB  =  -L/usr/lib/x86_64-linux-gnu/hdf5/serial/lib -lhdf5 -lz
endif


ifeq ($(SYSTYPE),"MPA")
CC       =  mpicc   
OPTIMIZE =  -O3 -Wall
GSL_INCL =  -I/usr/common/pdsoft/include
GSL_LIBS =  -L/usr/common/pdsoft/lib  -Wl,"-R /usr/common/pdsoft/lib"
FFTW_INCL= 
FFTW_LIBS= 
MPICHLIB =
HDF5INCL =  
HDF5LIB  =  -lhdf5 -lz 
endif
```
If you made sure that you set all the parameters correctly in the `Makefile`, you can save and close it. Now GADGET-2 could be built by `make` by running it in the `GADGET2/Gadget2` directory, same as we did above:

```bash
user@hostname:~/GADGET2/Gadget2/$ make |& tee m.txt
```

If everything went as it was supposed to be, then `make` will automatically create an executable called `Gadget2` in the same directory, which can be finally used to run simulations.

## III. Run GADGET-2 simulations
There are three components you need for a standard GADGET-2 simulation. The first one is the executable compiled above. The second one is a so-called "parameter file", ending with `.param`, which stores the necessary hyperparameters of a simulation. The third one stores the initial conditions for the scene/problem which needs to be simulated. If you want to create your own simulations, you need to create the latter two out of the three mentioned files for that. Fortunately there are some pre-defined examples shipped with the GADGET-2 download, which can be started immediately after the build of GADGET-2. Let's see how can we do that?

Just to maintain order, I advice you to create a folder named `Simulations` directly inside the `GADGET2` directory. Let us consider the case, that we'll want to execute the galaxy collision standard example. Doing so, inside the directory `Simulations` create another folder named `galaxy`. Into this folder you'll need to copy the `Gadget2` executable, and the corresponding parameter file, particularly the `galaxy.param` one found in `./GADGET2/Gadget2/parameterfiles/`:

```bash
user@hostname:~/GADGET2/$ cp Gadget2/Gadget2 /Simulations/galaxy/
user@hostname:~/GADGET2/$ cp Gadget2/parameterfiles/galaxy.param /Simulations/galaxy/
```
You can now edit the `galaxy.param` file inside the `/Simulations/galaxy` directory. Before running the simulation, there are some rows which needs to be changed first. The first two lines after the initial comment line should be changed to represent absolute locations of the IC input file and the output directory:

```bash
%  Relevant files

InitCondFile  	   /path/to/GADGET2/ICs/galaxy_littleendian.dat
OutputDir          /path/to/GADGET2/Simulations/galaxy/
```
Search for the `Code options` block and change the `SnapFormat` variables value `1` to `3`:

```bash
% Code options

ICFormat                 1
SnapFormat               3
ComovingIntegrationOn    0
```
This will tell the simulation to save snapshot files in `.hdf5` format which can be handled with Python later.

GADGET-2 works by taking a "snapshot" of the simulation at certain intervals, and saving the coordinates and velocities of the simulated particles into a file. The shorter this interval is, the better and more continuous our sample will be. The length of this can be changed in the line

```bash
% Output frequency

TimeBetSnapshot        0.01
```
Which I set to $0.01$ for my simulation. Also I increased the `TimeMax` value to $8.0$ to create longer a run in the simulations.

```bash
TimeMax	            8.0        % End of the simulation
```
After all parameters were set, you can run the code with the following command, by invoking the `Gadget2` executable in your directory with the parameter file copied into:

```bash
user@hostname:~/GADGET2/Simulations/galaxy/$ mpirun -np 4 ./Gadget2 galaxy.param
```
The number after the `-np` switch defines on how many CPU cores GADGET-2 will use for the simulation. Since my CPU has 4 cores, I set the values for the `-np` switch accordingly. GADGET-2 will create a lot of debug and helper files in the same directory, along all of the snapshot files.

With these values the final snapshot files were loaded in `Python` with the `h5py` library and was animated using `matplotlib`'s built-in animation functionality. The final animation can be seen on this YouTube video:


<iframe src="https://www.youtube.com/embed/KW0yIkPPymI" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>