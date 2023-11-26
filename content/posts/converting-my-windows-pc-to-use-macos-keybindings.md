---
view: layout.post
title: Converting my Windows PC to use MacOS keybindings
description: Using two free tools, switch your Ctrl and Alt keys while keeping Alt Tab working on your Windows computer to operate more like a MacOS machine
categories: productivity
published: Aug 19 2020
excerpt: A few months ago I put together a pretty decent Windows PC, mostly for gaming. Although of course I started tinkering here and there with programming on it, and eventually decided to go full in on making it a web dev machine. The timing couldn't be perfect either, as Windows released WSL 2 recently and its performance with Docker Desktop has been incredible.
---

tl;dr:

- Download and install the latest `.msi` of [SharpKeys](https://github.com/randyrants/sharpkeys/releases)
- Map `Left Ctrl` to `Left Alt` (and vice versa) in SharpKeys
- Download and install [AutoHotKey](https://www.autohotkey.com/)
- Save [this script](https://gist.github.com/aschmelyun/296ce38449aca705ead68ac76cdaa316) somewhere on your machine
- Double-click the downloaded script to activate it

**Want a more detailed walk-through? Keep reading!**

A few months ago I put together a pretty decent Windows PC, mostly for gaming. Although of course I started tinkering here and there with programming on it, and eventually decided to go full in on making it a web dev machine. The timing couldn't be perfect either, as Windows released [WSL 2](https://docs.microsoft.com/en-us/windows/wsl/wsl2-index) recently and its performance with Docker Desktop has been incredible.

The downside though is that my day-to-day work is on a MacBook, and (although I feel alone in this) I absolutely *love* using `Alt` instead of `Ctrl` for things like copying and pasting. I figured there had to be others like me out there who have solved this, so I went searching for a solution.

There's a few different methods out there, but I found a combination of two things that were easy to set up using free software, and have performed consistently well since I added them a few weeks ago. With that being said, **let's get started!**

### Step #1 - Switching Alt and Ctrl

The first part of this will be dedicated to swapping out our `Ctrl` key for the `Alt` key, which mimics the Apple keyboard `Cmd` key placement. For this, you'll need to download and install a program called **SharpKeys**. The app is entirely open source, and you'll need to visit the [releases GitHub page](https://github.com/randyrants/sharpkeys/releases) downloading the latest `.msi` file available.

Once you have it installed, open it up and click the **Add** button toward the lower right. You're looking to map both the Left Ctrl key to Left Alt, and the Left Alt key to Left Ctrl. After you do that, your program's main screen should look like this:

![Screenshot of the SharpKeys program for Windows remapping the Left Ctrl key to Left Alt along with the reverse](https://dev-to-uploads.s3.amazonaws.com/i/0bgyr30d977qdkn8svix.png)

If everything looks right, click the **Write to Registry** button towards the lower right to save your changes. Then, exit the application.

You can now try it out and see that your Alt and Ctrl keys have been switched! Instead of using **Ctrl+C** for copying, you'll now be using **Alt+C**. However, there's a small caveat. Because all we did was map the keys differently, keyboard shortcuts that made use of `Alt` now expect to use `Ctrl`.

If you're like me and use **Alt+Tab** to switch between windows a lot, that's where you'll notice this problem the most. Let's fix that!

### Step 2 - Remapping Alt Tab

For this next step we'll need another free program, [AutoHotKey](https://www.autohotkey.com/). Go ahead and download the program from that link and install it on your machine. Skip through any tutorial or help requests if you'd like, we're only going to be using it with a single (and fairly short) script.

After it's been installed successfully, download the following script and save it somewhere convenient on your machine, making sure that it contains a `.ahk` extension:

{% gist https://gist.github.com/aschmelyun/296ce38449aca705ead68ac76cdaa316 %}

From there, double-clicking on the file will silently start the AutoHotKey script, re-enabling your **Alt+Tab** window switching! ✨

**Note:** The above script also adds in some helpful MacOS-related keybindings for `Alt+Arrow` combinations.

### Extra Step - Running AutoHotKey Script on Startup

Using the method above, you'll need to double-click on the script each time your computer turns on in order for your **Alt+Tab** fix to start working. There's a [simple workaround](https://www.autohotkey.com/docs/FAQ.htm#Startup) for this though, that will get the script starting as soon as the computer turns on.

Just follow these three simple steps:

1. Press **Win+R** to open up the Run dialog.
2. Type in `shell:startup` and press Enter, this should open up a folder window.
3. Copy and paste your saved `.ahk` script file into this window.

As soon as your computer boots up, your AutoHotKey script and SharpKeys modifications will start running, essentially swapping out your Windows keybindings for those on a MacOS system. 🔥

**That's it!** If you have any questions about the above, any issues with what was outlined, or additions that might be helpful to the AutoHotKey script I provided, please feel free to let me know. You can leave a comment below, or message me on [Twitter](https://twitter.com/aschmelyun).

Feel free to follow me on there as well, if you'd like to see fairly regular, shorter posts on web development and MacOS. You'll also see updates when I publish new video tutorials and finish writing articles.