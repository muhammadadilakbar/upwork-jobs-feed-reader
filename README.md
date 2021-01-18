# upwork-jobs-feed-reader
UpWork Jobs Feed Reader

**Installation**

1. Copy all the files in this repository to your document root or any subdirectory under document root.
2. Write your DBMS username and password in mysqli_connect.php file.
3. Setup a cron job on Linux OS or create a task using Task Scheduler on Windows OS to execute loadjobs.php script after every one hour or two.

**Usage**

1. Open index.php in your web browser.
2. Click on "Create Files" hyperlink.
3. Fill the form and submit.
4. On index.php webpage, click on "Load Jobs" hyperlink.
5. Check the hyperlink under Feeds heading.
    
**How it works?**

Upwork publishes RSS feeds of job postings. These php scripts read these feeds, parse them and store job postings in the database. The jobs from the database are displayed in the reverse chronological order.

**Screenshot**

![Upwork jobs feed reader screenshot](https://raw.githubusercontent.com/muhammadadilakbar/upwork-jobs-feed-reader/main/screenshot.png )
