<?php
    include "app/config.php";
?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Sage Video Downloader</title>

        <link rel="stylesheet" href="app/assets/css/style.css">
        <script src="app/assets/js/app.js"></script>

        <!-- min 
        <link rel="stylesheet" href="app/assets/css/style.min.css">
        <script src="app/assets/js/app.min.js"></script>
        -->

        <!-- service worker <link rel="manifest" href="app/assets/js/app.webmanifest"> -->
    </head>
    <body>
        <header>
            <h1><a href="./">Sage Video Downloader</a></h1>
        </header>

        <main>
            <div class="content">

                <a id="download_link" href="" download>Download</a>
                
                <span id="wait_message">Please Wait...</span>

                <span id="error_message"></span>

                <?php if($youtube_dlp_env == "bin" && (PHP_OS_FAMILY == "Windows" && (!is_executable("app/bin/youtube-dlp.exe") && !is_executable("app/bin/youtube-dl.exe"))) || (PHP_OS_FAMILY == "Linux" && (!is_executable("app/bin/youtube-dlp") && !is_executable("app/bin/youtube-dl")))) : ?>
                <div id="no-youtube-dlp">
                    <h2>youtube-dlp or ffmpeg wasn't found. Please download <a href="https://github.com/yt-dlp/yt-dlp" target="_blank">youtube-dlp</a> and <a href="https://www.ffmpeg.org/download.html" target="_blank">ffmpeg</a>, and place it in the app/bin folder. <br> If you have them installed on your system, set the youtube-dlp and ffmpeg option to empty in app/config.php</h2>
                </div>
            <?php else : ?>
                <form id="download_video" method="POST" action="./download/" enctype="multipart/form-data">
                    <label>Video URL</label>
                    <span>Enter YouTube or site URL.</span>
                    <input id="url" type="url" placeholder="" required>

                    <label>Download Format</label>
                    <span>Select video download option.</span>
                    <select id="download_option">
                        <?php if($download_options['original'] == "enabled") : ?>
                        <option value="original" <?php if($default_download_option == "auto") { echo "selected"; } ?>>Original</option>
                        <?php endif; ?>
                        
                        <?php if($download_options['4k'] == "enabled") : ?>
                            <option value="4k" <?php if($default_download_option == "4k") { echo "selected"; } ?>>4K</option>
                        <?php endif; ?>

                        <?php if($download_options['2k'] == "enabled") : ?>
                            <option value="2k" <?php if($default_download_option == "2k") { echo "selected"; } ?>>2K</option>
                        <?php endif; ?>

                        <?php if($download_options['1080'] == "enabled") : ?>
                            <option value="1080" <?php if($default_download_option == "1080") { echo "selected"; } ?>>1080p</option>
                        <?php endif; ?>

                        <?php if($download_options['720'] == "enabled") : ?>
                            <option value="720" <?php if($default_download_option == "720") { echo "selected"; } ?>>720p</option>
                        <?php endif; ?>

                        <?php if($download_options['webm'] == "enabled") : ?>
                            <option value="webm" <?php if($default_download_option == "webm") { echo "selected"; } ?>>WebM</option>
                        <?php endif; ?>

                        <?php if($download_options['mkv'] == "enabled") : ?>
                            <option value="mkv" <?php if($default_download_option == "mkv") { echo "selected"; } ?>>MKV</option>
                        <?php endif; ?>

                        <?php if($download_options['avi'] == "enabled") : ?>
                            <option value="avi" <?php if($default_download_option == "avi") { echo "selected"; } ?>>AVI</option>
                        <?php endif; ?>

                        <?php if($download_options['playlist'] == "enabled") : ?>
                            <option value="playlist" <?php if($default_download_option == "playlist") { echo "selected"; } ?>>YouTube Playlist</option>
                        <?php endif; ?>

                        <?php if($download_options['mp3-playlist'] == "enabled") : ?>
                            <option value="mp3-playlist" <?php if($default_download_option == "mp3-playlist") { echo "selected"; } ?>>YouTube MP3 Playlist</option>
                        <?php endif; ?>

                        <?php if($download_options['mp3-audio'] == "enabled") : ?>
                            <option value="mp3-audio" <?php if($default_download_option == "mp3-playlist") { echo "selected"; } ?>>MP3 Audio</option>
                        <?php endif; ?>

                        <?php if($download_options['wav-audio'] == "enabled") : ?>
                            <option value="wav-audio" <?php if($default_download_option == "wav-playlist") { echo "selected"; } ?>>WAV Audio</option>
                        <?php endif; ?>

                        <?php if($download_options['flac-audio'] == "enabled") : ?>
                            <option value="flac-audio" <?php if($default_download_option == "flac-playlist") { echo "selected"; } ?>>FLAC Audio</option>
                        <?php endif; ?>

                        <?php if($download_options['third-party'] == "enabled") : ?>
                            <option value="third-party" <?php if($default_download_option == "third-party") { echo "selected"; } ?>>3rd Party Site</option>
                        <?php endif; ?>

                        <?php if($download_options['third-party-playlist'] == "enabled") : ?>
                            <option value="third-party-playlist" <?php if($default_download_option == "third-party-playlist") { echo "selected"; } ?>>3rd Party Playlist</option>
                        <?php endif; ?>
                    </select>

                    <span id="submit" onclick="downloadVideo();">Download</span>
                </form>
            <?php endif; ?>
            </div>
        </main>

        <footer>
            <p>v1.0 Created by <a href="https://drewilson.dev" target="_blank">Dre Wilson</a></p>
        </footer>
    </body>
</html>