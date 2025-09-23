
<img src="https://ophanimkei.com/tools/blog/examples/image.png"> <img src="https://ophanimkei.com/tools/blog/examples/image2.png">

# Mala Loves 2 Blog

This is my blog script. [I use it for my own blog.](#) You are not obligated to use it.  It has it's flaws though my hopes are to fix it's issues over time, but because the blog is suited for me, I am not in a rush. If you know PHP, you are free to contribute.

You might be wondering: why choose this over other solutions? Well, this is actually meant for a specific kind of person (me), but I will explain and perhaps it will appeal to you too

### **Currently Features:**
- **Easy Setup:** This is made to be very simple, especially if you have access to a webpanel (ie Cpanel, Cloudpanel, Webmin, etc)
- **Highly customizable:** This blog was made with neocities/tumblrina customizing sensibitilities. Things can easily be added by anyone with basic HTML/CSS knowledge such as pagedolls, status cafe widgets, embeds, CSS animations, javascript widgets,  etc.  You can really rip it apart and make it something else entirely.
- **Easy to backup:** The files are stored in databases. Can be exported with SQL or in a spreadsheet.
- **Date and tagging system**: Blogs have a date and tagging system. These generate links automatically and allow you to filter out posts
- **WYIWYG Editor (Optional):** Includes a What You See is What You Get Editor. You don't have to use it if you don't want to. It is pleasant to me though
- **Very Easy to Update:** Changes will be made to this script over time to Bulk it up - it's relatively young in its lifespan. Changes are easy to put into place

### **Things Are Missing But Are Planned:**
- **Editing of Old Posts:** Old posts in the database can only currently be directly edited in the database. A bit of a pain, but something I plan to fix anyways.
- **Drafts:** This editor doesn't have drafts though it is planned (in an update with editing of old posts). The pro of it being a WYIWYG editor is that you can really just copy and paste files directly into the editor if you

If the blog in its current form appeals to you, great! Let's keep going.If not, maybe see you later?

### Instructions
The blog requires basic set up. These instructions will be Cloudpanel/PHPMyAdmin oriented though anything that gives you access phpMyAdmin (or equivalents) works. If you have basic SQL knowledge, you also don't need to be worried.

1.  Download the files. You can download by clicking this link or navigating to the GitHub page (more download options later) 
   - [Download Link](https://github.com/malakhims/PHPSQL_Blog/archive/refs/heads/main.zip)  
   - Github Page.
2. In your panel, navigate to databases. It will take you to a very ugly page (in Cloudpanel, it's under Databases. In Cpanel, it's under PHPMyAdmin)

   <center>
   <img src="https://ophanimkei.com/tools/blog/examples/uglyasspage.png" width="40%">
   </center> 

3. Make a new database. Different panels handle this differently.
   - In Cloudpanel, you make a database and it gives you the user automatically. You can regenerate the password credentials if needed
   - In cPanel, you make the databases and users/passwords seperately. You make your user under "Manage my Databases"
4. After making your database and user, click the name of that database (under PHPMYAdmin, Recent/Favorites).  That will select the table. It will put up a table screen with inputs. Just ignore that.
5. Navigate to SQL and paste content of logs.sql into the SQL tab, then click go (bottom right). You're not done with PHPMyAdmin
6. You're done with that now!
7. Go to your config.php file. Update that with your database credentials (the database name is the name of the table you made- not logs.sql)
8. Go to auth.php. Update that with your credentials
9. Connect tinyCME (optional) 
   - Tiny CME is the API I use for the file editor. I included simpleadmin too if you don't want to use TinyCME for whatever reason. You can also get your own editor and replace the WYSIWG too. There's others. TinyCME is simply the best and is the reason I enjoy updating my blog, but people who aren't as image heavy / decorative as me don't really need it.
   - You just replace the API key in tinycme.js. It's free also. You make an account on tinycloud, get your key from here, and connect your domain under domains.

Everything should be working now. Here is a basic explanation of files.

### File Structure / Instruction
- **Directory Name**
  - Change name of PHPSQL_Blog direcotry to something prettier please please please

- **Security**
  - Recommend hashing your passwords. Rather than storing them in plaintext on your server (just set password = $hashedpasswordhere) you can hash them and store the hash instead. Lots of programs for hashing.

- **Avoid Touching (Esp After Setup)**
  - config.php - never need to touch this unless updating database credentials for whatever reason.
  - upload.php - handles uploads for tinyCME
  - simpleupload.php - handles uploads for simple editor

- **Code You Don't have to touch but Can**
  - admin.php - can edit styling of if you want it to be a bit prettier. can add more fields too if you want really. but would avoid if you are afraid of the database. defaults redirecting to "index.php" but can easily be changed
  - simpleadmin.php - same as above

- **Templates**
  - i made some templates and they're all included in the github file. while i made this to be highly customizable, templates do make things a lot easier in the end.
  - i recommend **changing the name of the template you use to use to index.php** so that your blog's default page is that template. 
  - **deleting unused templates recommended** or moving elsewhere. you can also frankenstein them if you want
  - previews: 
    - [rectangle.php / rectangle.css](https://ophanimkei.com/you/tools/blog/rectangle)
    - [blank.php / blank.css](https://ophanimkei.com/you/tools/blog/blank)
    - [journal.php / journal.css](https://ophanimkei.com/you/tools/blog/journal)
    - [compact.php / compact.css](https://ophanimkei.com/you/tools/blog/compact)
  - you're free to donate CSS too if you want

### That's All Folks
That's everything. I consider this project to be FOSS so I don't mind if anyone wants to contribute.

[This is github page.](https://github.com/malakhims/PHPSQL_Blog) You're free to also tell me bugs/issues you encounter there.  

And if you like this, please consider buying me a [ko-fi](https://ko-fi.com/ophanimkei)!!!
