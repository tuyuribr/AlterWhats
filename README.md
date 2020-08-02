# AlterWhats
A simple php project to change whatsapp messages, corrupt backups/logs and remove any evidence of the original message

## Install

( I'm hopping that you will use a Windows machine for this )

**Windows**

Install a php server in the computer that will connect to the cellphone  
I recommend using [XAMPP](https://www.apachefriends.org/download.html) and the following install tutorial will be arround this solution  
To install Xampp download the latest version (php number higher), and follow the instalation. You only need to install the php+apache Service  
After installing xampp Clone the repository (download as zip and unzip it);  
And put the content in the htdocs folder of XAMPP ( if you installed it in C: it will be C:/xampp/htdocs/ )  
It's recomended to extract the content to a folder called "alterWhats" to make configuration easy  
  
Open XAMPP CP and start the apache service now go to the "CONFIGURATION" part  
  
**Linux**  
If you are in linux you have to install a php server ( preferably with apache ) and install ADB and link it in the config.php  

## Emulator
If you want to use an Emulator to use the software I recomended using [BlueStacks Emulator](https://www.bluestacks.com/) because it comes with root.  
Make a google drive backup of your current whatsapp install whatsapp in the emulator [Whatsapp Apk](https://www.whatsapp.com/android/)   download the backup change your messages them make another google drive backup and download the backup in your phone.  

## Configuration
Connect your phone to your computer (or start your emulator)  

You will need to open a ADB shell to your phone first, to do so open your adb location in cmd (There is a copy of adb in alterWhats/adb, if you installed it in C:/xampp/htdocs/alterWhats just open a cmd in the adb folder or execute "cd C:/xampp/htdocs/alterWhats/adb/" in cmd)  
Now type "adb shell", the adb daemon will start and you should get a command line inside your phone (If you can't connect to your phone try confirm auth in the phone and install your phone drivers, google is your friend here)  
  
After getting the shell open, you should get root privileges, if you only need to type "su" and press enter to get root privilege you have to set the **"$CFG['customSuPath']"** var in "config.php" to **FALSE**.  
If to get root you need a customCommand you should set the var to the command, for example the [BlueStacks Emulator](https://www.bluestacks.com/) have a custom path and should set **"$CFG['customSuPath']"** var in "config.php" to **/system/xbin/bstk/su**.  

After getting root you should locate your whatsapp location, usually it is in /data/data/com.whatsapp/ , so try "ls -l /data/data/com.whatsapp/" if your device have whatsapp in this location you should get something like :  
```
1|OnePlus5:/ # ls -l /data/data/com.whatsapp/
total 32
drwxrwx--x 2 u0_a59 u0_a59 4096 2020-07-21 13:55 app_minidumps
drwxrwx--x 8 u0_a59 u0_a59 4096 2020-08-02 14:15 cache
drwxrwx--x 2 u0_a59 u0_a59 4096 2020-07-21 13:53 code_cache
drwxrwx--x 2 u0_a59 u0_a59 4096 2020-07-28 20:00 databases
drwxrwx--x 9 u0_a59 u0_a59 4096 2020-08-02 14:15 files
lrwxrwxrwx 1 root   root     32 2020-08-02 14:15 lib -> /data/app/com.whatsapp-1/lib/arm
drwx------ 2 u0_a59 u0_a59 4096 2020-07-21 13:55 lib-main
drwxrwx--x 2 u0_a59 u0_a59 4096 2020-07-21 13:55 no_backup
drwxrwx--x 2 u0_a59 u0_a59 4096 2020-08-02 14:15 shared_prefs
```  
Here you should get the User and Group to edit the **$CFG['chownUser']** and **$CFG['chownGroup']** vars, In my case the chownUser and chownGroup are the same: **u0_a59** the left column (the third collumn) is the User and the right column (forth column) is the Group, don't forget to set **$CFG['chown']** to **true**  
if it didn't locate try other locations like /data/user/0/com.whatsapp/ and change in "config.php" the **$CFG['remoteWhatsappDir']** var to match your dir.  
Now let's find the Media and Databases backup dir, It is in the sdcard folder and this whatsapp folder is called "Whatsapp". Usually it is in **/sdcard/Whatsapp/** if it isn't there change the **$CFG['whatsappSdcardLoc']** var.  
To make Pull and Push requests to the device we need a low privilage location to hold the files while we transfer it to the paths that require root, the var **$CFG['indirectPullPushPath']** handle this, you can set it to any path that you can pull/push via adb. If you don't know what you do, take the path from $CFG\['whatsappSdcardLoc'\] and change "Whatsapp" to "Whatsapp_Hold" and put in the $CFG\['indirectPullPushPath'\] var.  
  
Now lets config the rest of the vars just to make sure that everything is alright. You can close the Adb shell now  
  
```
/* start flush */
If You Using Xampp skyp this, if you aren't, delete every thing between /* start flush */ and /* end flush */
/* end flush */
```
  
**$CFG['systemPath']** Default: "C:/xampp/htdocs/alterWhats/"; The path to the alterWhats Dir (full path). If you are using xampp and it is in alterWhats folder, no need to worry   
**$CFG['systemUrl']** Default: "http://127.0.0.1/alterWhats/";  The system url (to access via web)   
**$CFG['adbPath']** Default: $CFG['systemPath']."adb/adb.exe";  Don't worry about this if in Xampp  
**$CFG['whatsappDir']** Default: "C:/xampp/htdocs/alterWhats/currentWhatsapp/"; Path to the whatsapp content no need to worry about this if in the same location  
**$CFG['whatsappBackupDir']** Default: "C:/xampp/htdocs/alterWhats/whatsappBackups/"; the local whatsapp backup path ( the software take a backup of the whatsapp before you change anything, if anything goes wrong you can recover your stuff)  
**$CFG['remoteWhatsappDir']** Default: "/data/data/com.whatsapp/";   
**$CFG['indirectPullPush']** Default: true; if you don't need indirect pull/push set this to "false" ( don't recomended )  
**$CFG['tempPath']** Default: "C:/xampp/htdocs/alterWhats/tempMedia/"; path to store contact photos  
**$CFG['tempPathUrl']** Default: "http://127.0.0.1/alterWhats/tempMedia/"; the url to the contact photos  
  
**$CFG['ftsv2']** Default: true; If you have a new whatsapp set this to true, if you can't add or change messages try change it to false (need a new import to not corrupt anything)  


  
## Warnings
You will lose your messages  
  
It's your responsibility to deny access to the software via network (lock your 80,443 ports)  
  
The add message functionality isn't 100%  
  
## Dependencies  
[ADB](https://developer.android.com/studio/terms) - APACHE  
[Jquery](https://jquery.org/license/) - MIT  
[Bootstrap](https://getbootstrap.com/docs/4.0/about/license/) - MIT  
[PHP](https://www.php.net/license/) - PHP  