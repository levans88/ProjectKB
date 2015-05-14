# ProjectKB
A website to centralize documentation of my projects and experiences.

I will record my pending bug fixes and feature requests under "Issues" in this repo. 

My intention in creating this project is to experiment with and learn different web technologies while building a functional knowledge base that I will use for all of my projects.  I will track things like a project's "to do" items, things to remember, what project a "note" belongs to, related web links and articles, etc.

The end goal is to make the code as "generic" as possible such that the scope of what is tracked could be very broad.  This would allow the site to function as a blog as well.

Please see http://blog.lennyevans.net for updates and https://github.com/levans88/ProjectKB/issues for current issues (this is an active project).

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

Current Features
----------------
**Tagging/Posting System**  
-Posts can be added, updated, and deleted.  
-All operations are performed in a single page.  
-When logged in, the tagging menu is visible and tags can be selected / deselected.  
-When editing, only the post being edited is displayed.  
-Multiple new tags can be added (separated by a ";") to the database using an input field in the tag menu.  This operation is unaffected by extra space characters.  
-Adding only new tags when making a post is allowed.

**Search**  
-Search functionality is modeled after Google's where "AND" is implied when there are multiple search terms:  https://support.google.com/websearch/answer/2466433?hl=en  
-Tags and post content are both searched.  
-If a post has a tag matching ANY of the search terms, that post will be in the results (regardless of whether the post content contains the term).  
-Literal strings can be searched for by putting quotes around the string.  
-The number of results can be limited or expanded.  

**Filter**  
-Results can be limited to posts having a specific tag by clicking on that tag at the bottom of any post.  
-The un-filtered view can be restored by pressing either "Reset" or the " * " tag in any post.  

**Encryption**  
-Passwords are hashed and salted before being stored in the database.  

**Proper Error Messages**  
-There is an appropriate error message displayed for each of the following scenarios:  Missing username, missing password, missing both username and password, invalid username or password, missing post content, missing tags, missing post content and tags  
-Error messages are always displayed in the same location on the page and there is no element shifting.  

**Automatic Link Formatting**  
-Hyperlinks are auto formatted as clickable links, but currently only for those that start with "http://" or "https://"  

**Responsive Design** (currently in progress)  
-The page border shrinks when the browser window shrinks horizontally.  
-I am working with FontAwesome and Fontello currently to replace the navigation menu text with icon fonts when the browser window is even smaller (not live yet).  
 
