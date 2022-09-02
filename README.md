# ProjectKB
A PHP knowledge base project and testing ground.

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

**Responsive Design** (in progress)  
-The page border shrinks when the browser window shrinks horizontally.  
-I am working with FontAwesome and Fontello currently to replace the navigation menu text with icon fonts when the browser window is even smaller (not live yet).  
