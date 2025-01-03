# FreshRSS Star To Raindrop
====================

A [FreshRSS](https://freshrss.org/) extension that automatically sends the article to Raindrop whenever you star it in FreshRSS.

This enables the following usecase:
- Starring an article on phone (using your RSS reader of choice, I use  [Readrops](https://github.com/readrops/Readrops))
- This extension runs when the star event hits FreshRSS
- The article is saved to your Raindrop account

The existing Raindrop sharing functionality of FreshRSS always opens a new tab which you have to manually close after sharing. You also have to stay logged in into Raindrop.

With this extension you can simply star an article to share it with Raindrop. Everything else happens in the background while you can continue reading articles without any further interruptions.

## Download and setup

1. Clone the repository to your FreshRSS installations extensions folder
    1. Then, extract and upload it to the `./extensions` folder of your FreshRSS installatio 
    2. Go to [Raindrop's Integration Settings]("https://app.raindrop.io/settings/integrations") 
    3. Create a new Application and make note of the Client Secret and Client Id Values
    4. Enter the Client Secret, Client Id and Callback URI values and hit "Connect to Raindrop"
    5. Authorize your newly created application

## Raindrop API Error codes

If you get errors while trying to connect to Pocket, please check the [Raindrop developer documentation](https://developer.raindrop.io/v1/authentication) for detailed error code descriptions.

## Translations

If you'd like to translate the extension to another language please file a pull request. I'd be happy to merge it!

# Thanks

Thanks to [@JWPepper](https://github.com/JWPepper) for the original starToPocket extension from which this repo is forked. 

## `//TODO:`
- Add automatic token access token refresh
- Add notifications when articles successfully saved to Raindrop
