SpotiPull
=========

This is SpotiPull, a simple integration of the Spotify v1 API on top of another API, aiming to provide a larger set of features.

## Installing
Getting the service up and running is a simple operation that can be done in a minute or two.

 - Log into your Spotify account and then visit the [developers dashboard](https://developer.spotify.com/dashboard/).
 - Create an application and take note of the **Client ID** and the **Client Secret** as you'll be needing both.
 - Edit your virtual host file and add these values as environment variables. An example for Apache would look like this:


   <div style="text-align:center"><img src="https://i.imgur.com/DHauDRa.png" /></div>
 - Once you're done, save the file, restart the server and you're good to go!

## Features
The following list provides information about the currently supported features:

 - Get the discography for an artist.

## Usage
The following list aims to document the currently available endpoints and their use cases:
 - `api/v1/albums?band-name={band-name}`- requires: **band-name** (String)

	Pass a **band-name** and retrieve their discography. An example response body is attached below.
    <div style="text-align:center"><img src="https://i.imgur.com/O9kifl8.png" /></div>

## Resources
Give it a try! Import the examples in the **resources** folder and start playing around.

Supported software: Postman

## License
This project is just a quick real-world implementation of the Spotify API, feel free to grab some examples for your own projects.

Licensed under the [MIT license](LICENSE).