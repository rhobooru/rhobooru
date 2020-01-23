![GitHub release (latest by date)](https://img.shields.io/github/v/release/rhobooru/rhobooru) ![GitHub Workflow Status](https://img.shields.io/github/workflow/status/rhobooru/rhobooru/Run%20PHPUnit) [![codecov](https://codecov.io/gh/rhobooru/rhobooru/branch/master/graph/badge.svg)](https://codecov.io/gh/rhobooru/rhobooru)

📕 [Documentation](https://github.com/rhobooru/rhobooru/wiki)

🔧 [Demo](https://demo.rhobooru.com)

🌍 [Site](https://rhobooru.com) 

# rhobooru

> 🖼 Semantic media organization for the modern age 🎉

rhobooru is a media gallery that uses tags to make finding stuff easy. It's based on the `image board` concept familiar to users of projects such as Danbooru, Gelbooru, and Shimmie. In short, you add highly specific tags to media and use powerful search tools to find just the thing you're looking for.

The rhobooru project has several guiding principles that set it apart from existing solutions:

* **Be a joy to use**. rhobooru should make organizing, searching, and viewing massive media collections fun and easy. Provide the right tools at the right time and then get out of the way. You want a no-frills, full-screen gallery experience? Done. You want enough bells and whistles to power-mod yourself to the moon? Have at it.

* **Don't f%$k with the data**. You're trusting rhobooru with your precious collection and it promises not to scuff your shiny pixels. Files and their metadata are always stored in a way that makes it easy for you to pull that one special image out to share with a friend or to export the entire database and uninstall rhobooru.

* **Be a diplomat**. There's a rich ecosystem of image boards already out there. rhobooru aims to make it reasonable to deal with all their competing standards and incompatible choices. Why should your private collection care that your two favorite image boards use different tags for the exact same thing? You want to call it something else anyways and you're obviously right.

* **Work online and offline**. I get it: you're a digital hoarder and you want to hide your shame away on the NAS in your closet. rhobooru will run contentfully in its offline corner and let you get to it on just your local network. Or maybe you have a hundred thousand pictures of cats meticulously tagged and organized that the world *has* to see. Toss rho on a web server and share it with the world.

* **Don't be broken**. rhobooru aims for complete test coverage of the codebase. No more dread that every patch will introduce a ton of fiddly little bugs. No more features that kind of work but only on Tuesdays. And with both rhobooru and rhovue built on well-supported, modern tech stacks, you won't have to recite eldritch chants just to stand an instance up.

* **Play nice**. Does it grind your gears that rhovue doesn't support your favorite Neon Unicorn Vomit theme? Do you think it'd be way cooler to interact with the API through nothing but the terminal? Can you only sleep at night if your media gallery is a widget on your desktop? Well good news: you're free to make your own front end, library, mobile app, or bot to work with rhobooru the way you want. Everything is modular, open source, and MIT-licensed, so have fun.

***

## rhoverse

**rhobooru**
> This repository. This is the back end and API of a full rhobooru instance. It handles all of the primary business logic.

**[rhovue](https://github.com/rhobooru/rhovue)**
> This is the front end of a full rhobooru instance. While this is the official front end, anything that can talk to the rhobooru API can act as an alternative front end.

**[rhobot](https://github.com/rhobooru/rhobot)**
> This is an optional automation service for a rhobooru back end. It automates tasks like media scraping and tagging.

**[rhodock](https://github.com/rhobooru/rhodock)**
> This is a pre-packaged release of rhobooru, rhovue, and rhobot in a Docker image. It can be used if you're looking to easily run a personal rhobooru instance on something like a home server or NAS.

**[rhoddon](https://github.com/rhobooru/rhoddon)**
> This is a browser add-on/extension for interacting with rhobooru services. It's a collection of helpful tools, such as sending the current web page to be imported by rhobooru.
