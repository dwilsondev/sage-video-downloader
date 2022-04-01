# Sage Video Downloader
A simple PHP Webapp for downloading videos from across the Web.

<img src="https://cdn.drewilson.dev/public/for_github/sage-logo.png" style="width:10%; margin: auto; margin-bottom: 2%; display: block; " alt="logo">

![screenshot](https://cdn.dretech.me/public/for_github/sage-video-downloader-screenshot.png)

# About
Sage Video Downloader (SVD) is a simple PHP Webapp for download videos from YouTube and many other websites. It uses YouTube DLP, so anything YouTube DLP and YouTube DL can download, so can SVD.

# Features
* Download videos from YouTube at 4K, 2K, 1080p, and 720p.
* Download video formats in MP4, MKV, WebM, AVI.
* Download audio formats in MP3, WAV, and FLAC.
* Download YouTube Playlists as videos or MP3s.
* Download from all sites supported by YouTube DL and YouTube DLP.
* Download video as HEVC h.265.
* Mobile friendly.

# Installation
SVD requires a Webserver with PHP, and [YouTube DLP](https://github.com/yt-dlp/yt-dlp). Regular YouTube DL is also supported.

Download and extract SVD ZIP and place it on your Webserver.

If you don't have youtube-dlp installed on your system, you can install it locally for SVD by downloading it and place it in the app/bin folder named `youtube-dlp`.

If YouTube DLP is installed on your system already, set the `$youtube_dlp_env` variable to an empty string in app/config.php.

And that's it! You're ready to start downloading!

# Installation With Composer
Make sure you have Composer installed and run the following in your terminal.

`composer require dwilsondev/sage-video-downloader:dev-main`

## FFmpeg
[ffmpeg](https://github.com/FFmpeg/FFmpeg) is required for AVI, WAV, and FLAC downloads. 

If you don't have ffmpeg installed on your system, you can install it locally for SVD by downloading it and place it in the app/bin folder named `ffmpeg`.

If FFmpeg is installed on your system, set the `$ffmpeg_env` variable to an empty string in app/config.php.

## YouTube DL
If you prefer to use YouTube DL instead of YouTube DLP, simply modify app/env.php and change the values `$youtube_dlp` to `youtube-dl` like this:

```
if($youtube_dlp_env == "bin") {
    $youtube_dlp = realpath("bin") . DIRECTORY_SEPARATOR . "youtube-dl";
} else {
    $youtube_dlp = "youtube-dl";
}
```

Note: SVD has not been officially tested with YouTube DL.

# Configure PHP (recommended)
You may want to increase `max_execution_time` in your `php.ini`. WebM, recoding, and encoding HEVC can take a long time to process depending on the download.

# Video Downloads
You can download videos from YouTube in various resolutions if they're available to the public.

You can also download from a large selection of third party video sites supported by YouTube DL and YouTube DLP. You can see a list of supported sites [here](https://github.com/yt-dlp/yt-dlp/blob/master/supportedsites.md)

# Audio Downloads
You can download audio as MP3, WAV, or FLAC.

Note: WAV and FLAC requires FFmpeg.

# Playlist Downloads
SVD supports downloading YouTube playlists and YouTube MP3 playlists.

You can also download playlists from third party sites if available.

# HEVC Download
SVD can be configured to encode MP4, MKV, AVI, and WebM downloads as HEVC h.265. HEVC can greatly reduce the size of downloads while maintaining high quality.

# Configuration
You can change SVD options in the config.php file inside the app folder.

## Default Download Option
Set the default download option in the Web form.

Can be `original`, `4k`, `2k`, `1080`, `720`, `webm`, `mkv`, `avi`, `playlists`, `mp3-playlist`, `mp3-audio`, `wav-audio`, `flac-audio`, `third-party`, `third-party-playlist`.

## Download Options
Enable or disable what options are displayed in the Web form. Set these to either `enabled` or `disabled`

## Recode Video
Sets whether SVD should force re-encode WebM, MKV, and AVI downloads.

Set to either `true` or `false`.

Note: Enabling this will drastically increase processing and download times.

## Recode HEVC H.265 (requires FFmpeg)
Sets whether SVD should re-encode MP4, WebM, MKV, and AVI as HEVC MP4.

Set to either `true` or `false`

Note: Enabling this will drastically increase processing and download times.

## H.265 Options (requires FFmpeg)
Customize FFmpeg options for HEVC encoding. Please refer to [FFmpeg](https://trac.ffmpeg.org/wiki/Encode/H.265) doc for more info.

## YouTube DLP/FFmpeg Environment
Tell SVD which YouTube DLP and FFmpeg binaries to use. Set to `bin` to make SVD use the binaries in app/bin. Set to "" or empty to use the binaries installed on your system.

# Webapp
SVD can be turned into a Webapp for desktop and mobile. A service worker is set up. All you have to do is change the src and start_url in the sage.webmanifest file located in app/assets/js to the domain of your site. You can use relative or absolute paths. Then uncomment this line in index.php:

`<!-- service worker <link rel="manifest" href="app/assets/js/sage.webmanifest"> -->`

You must have a secure website with SSL setup in order for the service worker to work.

# Known Bugs
* If YouTube DLP and system permissions aren't set up properly, SVD will issue false positive download links.

# Note
* This was yet another fun project. I do not recommend using this for a commercial site unless you want to rewrite some of the code.
* If the Recode or Recode H265 options are enabled, processing will take a very long time depending on video. You should increase the max execution time for PHP. DO NOT enable both options as doing will double processing time and may degrade the quality of the video.
* I have not written any lock checks for this app yet. So once a downloads and recoding starts, they will continue processing on the server even if the user closes the tab. Also, multiple process can run at the same time. This can be bad if you have tons of requests.
* Has not been tested with Linux or Mac.
  
# Created In
* PHP
* Sass
* JavaScript

# Created With
* [youtube-dlp](https://github.com/yt-dlp/yt-dlp/blob/master/supportedsites.md)
* [ffmpeg](https://www.ffmpeg.org/)