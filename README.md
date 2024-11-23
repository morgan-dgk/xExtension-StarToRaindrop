# FreshRSS Star To Pocket
====================

A [FreshRSS](https://freshrss.org/) extension that automatically sends the article to pocket whenever you star it in FreshRSS.

This enables my usecase of: 
- Starring an article on my phone (using [Read You](https://github.com/Ashinch/ReadYou))
- This extension runs when the star event hits FreshRSS
- The article is saved to my Pocket account (maybe later including tags and such as well)
- I can then access the article on my Kobo E-Reader for a better reading experience.
- I also get a "free backup" of all my starred articles.

The existing Pocket sharing functionality of FreshRSS always opens a new tab which you have to manually close after sharing. You also have to stay logged in into Pocket.

With this extension you can simply star an article to share it with Pocket. Everything else happens in the background while you can continue reading articles without any further interruptions.

## Download and setup

1. Clone the repository to your FreshRSS installations extensions folder
    1. You can also download the [latest release](https://github.com/huffstler/star-to-pocket/tags)
    1. Then, extract and upload it to the `./extensions` folder of your FreshRSS installation
1. Go to [Pocket's Developer Portal](https://getpocket.com/developer/apps/)
1. Create a new application with at least the `add` permission
1. Enter your Consumer Key in the `Star To Pocket` extension settings
1. Press "Connect to Pocket"
1. Authorize your just created application

## Pocket API Error codes

If you get errors while trying to connect to Pocket, please check the [Pocket developer documentation](https://getpocket.com/developer/docs/authentication) for detailed error code descriptions.

## Translations

If you'd like to translate the extension to another language please file a pull request. I'd be happy to merge it!

# Thanks

Super thanks to [@christian-putzke](https://github.com/christian-putzke) for writing 99% of the code necessary for this extension before I ever had the idea.

## `//TODO:`

Once this is working I don't expect to do much with it. It _would_ be nice to have tags passed to Pocket, or integration with other tools (Wallabag, etc), but it's not necessary for me. At minimum I expect to merge upstream changes from `@christian-putzke` _if_ they touch functionality that this extension uses. But that probably won't be a fast process. Just so you know if you're reading this.
