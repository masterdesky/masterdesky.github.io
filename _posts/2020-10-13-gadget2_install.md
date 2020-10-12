---
layout: post
author: Balázs Pál
title : Intalling and running GADGET-2 - 2020
date: 2020-10-12T00:10:00Z
featured-image: ../assets/images/posts/gadget2/millennium.jpg
featured-image-alt: The famous Millennium Simulation of GADGET2 from 2005
---
<b>
The GADGET-2 (<i>GAlaxies with Dark matter and Gas intEracT</i> - 2) is the name given to the second iteration of the software written by Volker Springel for cosmoligical N-body and SPH simulations. Here I'll summarize the steps, how to download, install and run the software on a Linux computer.
</b>

## I. Prequisites
### Download necessary files
First of all, if you want to work with the GADGET-2 software, I advise you to consider using a computer with Linux or MacOS installed on it. However it is possible to install it on Windows (somehow), it is much easier to do it on Linux/Mac. Also in this turial, all the steps will be done on a Linux computer.

If you run Linux or UNIX, you already have all the necessary `C` compilers needed for the task. Since there are numerous different Linux distributions out there, and because I'm particularly running Kali Linux, there is a slight chance for errors and anomalies to occure on other systems than mine. This tutorial is completely optimized to run on my system, but $99.9\%$ that it will run on other Linux systems too. Also I'll only go into details of the manual installation, without using Homebrew, or other similar package managers.

As it was noted in user guide of GADGET-2, there are some non-standard libraries, which you'll need to install first to compile GADGET-2 successfully. Download the following softwares:
1. [Gadget 2.0.7](https://wwwmpa.mpa-garching.mpg.de/gadget/gadget-2.0.7.tar.gz) (latest version)
2. [MPICH >1.0 (3.3.2)](http://www.mpich.org/static/downloads/3.3.2/mpich-3.3.2.tar.gz)
3. [GSL 1.9](ftp://ftp.gnu.org/gnu/gsl/gsl-1.9.tar.gz) (Didn't tested later versions)
4. [FFTW 2.15](http://www.fftw.org/fftw-2.1.5.tar.gz) (Needed for MPI capability)
5. HDF5 >5.0 (Installed from terminal, detailed in the next section)

### Install prequisites
First you'll need to extract all the (yet only 4) downloaded softwares.
```sh

```