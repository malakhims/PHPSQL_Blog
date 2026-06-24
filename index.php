<!DOCTYPE html>
<html>
<head>    
  <meta charset="UTF-8">  
  <meta name="viewport" content="width=device-width, initial-scale=0.5">
  <title>MALA'S BLOG SCRIPT</title>
  <meta name="description" content="become girlblogger">
  <meta name="twitter:image" content="https://ophanimkei.com/you/tools/blog/examples/image.png">
  <meta name="twitter:card" content="https://ophanimkei.com/you/tools/blog/examples/image.png">
  <link rel="icon" href="https://mal.ophanimkei.com/widget/usakumya1-2.png">
  <script src="https://kit.fontawesome.com/f936906ae0.js" crossorigin="anonymous"></script>
  <link href="/style.css" rel="stylesheet" type="text/css" media="all"> 
  <base target="_parent">

        <style>


            html, body {
              background-image: url(/images/skybg.jpg);
              background-size: cover;
              color: rgb(0, 0, 0);
              font-family: Alice;
              font-size: large;
              background-color: rgb(0, 0, 0);
              margin:0;
              padding:0;
            }


            @font-face {
                font-family: Alice;
                src: url("/fonts/Alice-Regular.ttf");
            }

            p {
              color: black;
              font-family: Alice;
            }

            h1 {
              color: black;
              font-family: Alice;
              font-size: xx-large;
            }

            
            h2 {
              color: black;
              font-size:x-large;
            }

            
             @font-face {
                font-family: Alice;
                src: url(/fonts/Alice-Regular.ttf);
            }

            .display {
              color:purple;
            }
            
            .display1 {
              color:white;
            }

            a .widget {
            color:purple;
            }

            a .widget :visited {
            color:purple;
            }

            a .display {
            color:purple;
            }

            a .display :visited {
            color:purple;
            }
            

            .box {
            width:80%; /* 幅 */
            margin: auto;
            border-width:8px;
            border-style:solid;
            border-image: url("/images/sozai/orangelace.png") 8 fill round;
            min-height: 100vh;
            padding: 20px;
            }

                    /* Collapsible container */
        .tutorial-section {
            width: 100%;
            margin-bottom: 15px;
            background-color: white;
            border-radius: 8px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
            overflow: hidden;
            color: black;
            transition: all 0.3s ease;
        }
        
        /* Section header (clickable) */
        .section-header {
            padding: 15px 20px;
            color:purple;
            cursor: pointer;
            font-weight: bold;
            font-size: 1.1em;
            display: flex;
            justify-content: space-between;
            align-items: center;
            user-select: none;
            transition: background-color 0.2s;
        }
        
        .section-header:hover {
        }
        
        /* Arrow indicator */
        .arrow {
            display: inline-block;
            width: 0;
            height: 0;
            border-left: 6px solid transparent;
            border-right: 6px solid transparent;
            border-top: 6px solid white;
            transition: transform 0.3s;
        }
        
        .arrow.open {
            transform: rotate(180deg);
        }
        
        /* Section content */
        .section-content {
            padding: 0;
            max-height: 0;
            overflow: hidden;
            transition: max-height 0.3s ease, padding 0.3s ease;
        }
        
        .section-content.open {
            padding: 20px;
            max-height: 5000px; /* Adjust based on your content needs */
        }
        
        /* Content styling */
        .section-content p {
            margin-top: 0;
            margin-bottom: 15px;
        }
        
        .section-content p:last-child {
            margin-bottom: 0;
        }
    </style>

    </head>
    <body>


    <div  class = "box"> 

    <center>
      <img src="examples/image.png" width="44%">
      <img src="examples/image2.png" width="44%">
    </center>

    
      <?php include $_SERVER['DOCUMENT_ROOT'] . '/you/tools/includes/returnhome.php'; ?>


    <h1>Mala Loves 2 Blog</h1>
      <p>This is my blog script. <a href="https://ophanimkei.com/personal/diary/">I use it for my own blog.</a> You are not obligated to use it.&nbsp; It has it's flaws though my hopes are to fix it's issues over time, but because the blog is suited for me, I am not in a rush. If you know PHP, you are free to contribute.</p>
      <p>You might be wondering: why choose this over other solutions? Well, this is actually meant for a specific kind of person (me), but I will explain and perhaps it will appeal to you too</p>
      <h3><strong>Currently Features:</strong></h3>
      <ul>
      <li><strong>Easy Setup:&nbsp;</strong>This is made to be very simple, especially if you have access to a webpanel (ie Cpanel, Cloudpanel, Webmin, etc)</li>
      <li><strong>Highly customizable:&nbsp;</strong> This blog was made with neocities/tumblrina customizing sensibitilities. Things can easily be added by anyone with basic HTML/CSS knowledge such as pagedolls, status cafe widgets, embeds, CSS animations, javascript widgets,&nbsp; etc. You can really rip it apart and make it something else entirely.</li>
      <li><strong>Easy to backup:</strong> The files are stored in databases. Can be exported with SQL or in a spreadsheet.</li>
      <li><strong>Date and tagging system: Blogs have a date and tagging system. These generate links automatically and allow you to filter out posts</strong></li>
      <li><strong>WYIWYG Editor (Optional):</strong> Includes a What You See is What You Get Editor. You don't have to use it if you don't want to. It is pleasant to me though</li>
      <li><strong>Very Easy to Update:</strong> Changes will be made to this script over time to Bulk it up - it's relatively young in its lifespan. Changes are easy to put into place</li>
      <li><strong><span style="color: #33cccc;">NEW:</span> Draft System:</strong> Now you have drafts for your posts!!</li>
      </ul>
      <p>If the blog in its current form appeals to you, great! Let's keep going.If not, maybe see you later?</p>
      <h3>Instructions</h3>
      <p>The blog requires basic set up. These instructions will be Cloudpanel/PHPMyAdmin oriented though anything that gives you access phpMyAdmin (or equivalents) works. If you have basic SQL knowledge, you also don't need to be worried.</p>
      <p>&nbsp;</p>
      <ol>
      <li>&nbsp;Download the files. You can download by navigating to Github or Purchasing on itchio.
      <ol>
      <li><a href="https://github.com/malakhims/PHPSQL_Blog/releases">Download on Github</a></li>
      </ol>
      </li>
      <li>In your panel, navigate to databases. It will take you to a very ugly page (in Cloudpanel, it's under Databases. In Cpanel, it's under PHPMyAdmin)<img src="examples/uglyasspage.png" alt="" width="40%" />
      <ol>
      <li>Make a new database.&nbsp;Different panels handle this differently.</li>
      <li>In Cloudpanel, you make a database and it gives you the user automatically. You can regenerate the password credentials if needed</li>
      <li>In cPanel, you make the databases and users/passwords seperately. You make your user under "Manage my Databases"</li>
      <li>After making your database and user, click the name of that database (under PHPMYAdmin, Recent/Favorites).&nbsp; That will select the table. It will put up a table screen with inputs. Just ignore that.</li>
      <li>Navigate to SQL and paste content of logs.sql into the SQL tab, then click go (bottom right). You're not done with PHPMyAdmin</li>
      <li>You're done with that now!</li>
      </ol>
      </li>
      <li>Go to your config.php file. Update that with your database credentials (the database name is the name of the table you made- not logs.sql)</li>
      <li>Go to auth.php. Update that with your credentials</li>
      <li>Connect tinyCME (optional)&nbsp;</li>
      <li>Tiny CME is the API I use for the file editor. I included simpleadmin too if you don't want to use TinyCME for whatever reason. You can also get your own editor and replace the WYSIWG too. There's others. TinyCME is simply the best and is the reason I enjoy updating my blog, but people who aren't as image heavy / decorative as me don't really need it.
      <ol>
      <li>You just replace the API key in tinycme.js. It's free also. You make an account on tinycloud, get your key from here, and connect your domain under domains.</li>
      </ol>
      </li>
      </ol>
      <p>Everything should be working now. Here is a basic explanation of files.</p>
      <h3>File Structure / Instruction</h3>
      <ul>
      <li><strong>Directory Name</strong>
      <ul>
      <li>Change name of PHPSQL_Blog direcotry to something prettier please please please</li>
      </ul>
      </li>
      <li><strong>Security</strong>
      <ul>
      <li>Recommend hashing your passwords. Rather than storing them in plaintext on your server (just set password = $hashedpasswordhere) you can hash them and store the hash instead. Lots of programs for hashing.</li>
      </ul>
      </li>
      <li><strong>Avoid Touching (Esp After Setup)</strong>
      <ul>
      <li>config.php - never need to touch this unless updating database credentials for whatever reason.</li>
      <li>upload.php - handles uploads for tinyCME</li>
      <li>simpleupload.php - handles uploads for simple editor</li>
      </ul>
      </li>
      <li><strong>Code You Don't have to touch but Can</strong>
      <ul>
      <li>admin.php - can edit styling of if you want it to be a bit prettier. can add more fields too if you want really. but would avoid if you are afraid of the database. defaults redirecting to "index.php" but can easily be changed</li>
      <li>simpleadmin.php - same as above</li>
      </ul>
      </li>
      <li><strong>Templates</strong>
      <ul>
      <li>i made some templates and they're all included in the github file. while i made this to be highly customizable, templates do make things a lot easier in the end.</li>
      <li>i recommend<strong> changing the name of the template you use to use to index.php</strong> so that your blog's default page is that template.&nbsp;</li>
      <li><strong>deleting unused templates recommended</strong> or moving elsewhere. you can also frankenstein them if you want</li>
      <li>previews:&nbsp;
      <ul>
      <li><a href="https://ophanimkei.com/you/tools/blog/rectangle">rectangle.php / rectangle.css </a></li>
      <li>&nbsp;<a href="https://ophanimkei.com/you/tools/blog/blank">blank.php / blank.css</a></li>
      <li><a href="https://ophanimkei.com/you/tools/blog/journal">journal.php / journal.css</a></li>
      <li><a href="https://ophanimkei.com/you/tools/blog/compact">compact.php / compact.css </a></li>
      </ul>
      </li>
      <li>you're free to donate CSS too if you want</li>
      </ul>
      </li>
      </ul>
      <h3>That's All Folks</h3>
      <p>That's everything. I consider this project to be FOSS so I don't mind if anyone wants to contribute.</p>
      <p><a href="https://github.com/malakhims/PHPSQL_Blog">This is github page.</a>&nbsp;You're free to also tell me bugs/issues you encounter there.</p>
      <p>And if you like this, please consider buying me a <a href="https://ko-fi.com/ophanimkei">ko-fi</a>!!!</p>
    </body>
    </html>