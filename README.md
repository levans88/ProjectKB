# ProjectKB
A website to centralize documentation of my projects and learning experiences.

I will record my pending bug fixes and feature requests under "Issues" in this repo. 

My intention in creating this project is to experiment with and learn different web technologies while building a functional knowledge base that I will use for all of my projects.  I will track things like a project's "to do" items, things to remember, what project a "note" belongs to, related web links and articles, etc.

The end goal is to make the code as "generic" as possible such that the scope of what is tracked could be very broad.  This would allow the site to function as a blog as well.

Setup
-----
The schema to create the database using MySQL is available as well as the model (from which the schema can be made).  The files are under the "db" folder as:  ProjectKB.sql and ProjectKB.mwb.  The model can be opened using MySQL Workbench.

This code requires a "constants.php" present in \includes to define the database connection as follows:

// database name
define("DATABASE", "...");

// database password
define("PASSWORD", "...");

// database server
define("SERVER", "...");

// database username
define("USERNAME", "...");

I am currently using the virtual machine supplied with Harvard's cs50 class for testing and working on this project.  Problem set 7 from that course explains how to set permissions and make the required change in /etc/hosts so that the website is accessible within the VM (appliance).

To access the site outside the appliance I had to duplicate the change in the Windows hosts file and make changes to the appliance's firewall.  The required changes for network access to the web page and appliance in general will be different depending on how the VM's network is configured in VMWare Player.

The link below is for cs50's 2013 course but the relevant documentation for permissions and /etc/hosts is the same:  http://d2o9nyf4hwsci4.cloudfront.net/2013/fall/psets/7/pset7/pset7.html

