<?php

    include "config.php";
    include "env.php";

    header('Content-Type: application/json; charset=utf-8');

    if(empty($_POST)) {
        jError("Critical server error, no data was sent to the server.");
    }

    #####################################################################################
    #
    #   CHECK USER INPUT
    #
    #####################################################################################
    $url = filter_input(INPUT_POST, 'url', FILTER_SANITIZE_URL);
    $download_option = filter_input(INPUT_POST, 'download_option', FILTER_SANITIZE_URL);

    if(empty($url) || empty($download_option)) {
        jError("No URL or download option select.");
    }

    $folder = uniqid();
    mkdir("downloads/$folder");

    if($download_option == "original") {
        $options = "-f bestvideo[ext=mp4]+bestaudio[ext=m4a]/best[ext=mp4]/best";
    } elseif($download_option == "4k") {
        $options = "-f bestvideo[height=2160]+bestaudio/best[height=2160]/best";
    } elseif($download_option == "2k") {
        $options = "-f bestvideo[height=1440]+bestaudio/best[height=1440]/best";
    } elseif($download_option == "1080") {
        $options = "-f bestvideo[height=1080]+bestaudio/best[height=1080]/best";
    } elseif($download_option == "webm") {
        $options = "-f bestvideo[ext=webm]+bestaudio[ext=m4a]/best[ext=webm]";
    } elseif($download_option == "mkv") {
        if($recode_video == true) {
            $options = "-f bestvideo[ext=mp4]+bestaudio[ext=m4a]/best[ext=mp4]/best --recode-video mkv";
        } else {
            $options = "-f bestvideo[ext=mp4]+bestaudio[ext=m4a]/best[ext=mp4]/best";
        }
    } elseif($download_option == "avi") {
        $options = "-f bestvideo[ext=mp4]+bestaudio[ext=m4a]/best[ext=mp4]/best";
    } elseif($download_option == "playlist") {
        $options = "--yes-playlist -f bestvideo[ext=mp4]+bestaudio[ext=m4a]/best[ext=mp4]/best";
    } elseif($download_option == "mp3-playlist") {
        $options = "--yes-playlist --extract-audio --audio-format mp3";
    } elseif($download_option == "mp3-audio") {
        $options = "--extract-audio --audio-format mp3";
    } elseif($download_option == "wav-audio") {
        $options = "--extract-audio --audio-format wav";
    } elseif($download_option == "flac-audio") {
        $options = "--extract-audio --audio-format flac";
    } else {
        $options = "";
    }

    #####################################################################################
    #
    #   DOWNLOAD VIDEO
    #
    #####################################################################################
    $file = downloadVideo($download_option, $url, $folder, $options);

    if(file_exists($file)) {
        $data['link'] = "$file";
        echo json_encode($data);
    } else {
        $data['error'] = "";
        jError("Couldn't find video. Try choosing a different format.");
    } 

    function downloadVideo($download_option, $url, $folder, $options = "")
    {
        $youtube_title = runCommand("$youtube_dlp --get-filename -o %(title)s $url");
        
        // Download non-playlist files.
        if($download_option !== "playlist" && $download_option !== "mp3-playlist" && $download_option !== "mp3-playlist" && $download_option !== "third-party-playlist") {
            runCommand("$youtube_dlp $options -o downloads/$folder/file.%(ext)s $url");
        }

        // Download playlists and save to zip.
        if($download_option == "playlist" || $download_option == "mp3-playlist" || $download_option == "third-party-playlist") {
            // Check if playlist exists first.
            $playlist_title = runCommand("$youtube_dlp --get-filename -o %(playlist_title)s $url");

            if($playlist_title == "NA") {
                jError("Couldn't download playlist.");
            } elseif($playlist_title !== "NA" && $download_option == "third-party-playlist") {
                $youtube_title = $playlist_title;
            }

            // Download playlist.
            runCommand("$youtube_dlp $options -o downloads/$folder/%(title)s.%(ext)s $url");

            $zip = new ZipArchive();
            $filename = "files.zip";

            // Zip up files.
            $zip->open("downloads/$folder/$filename", ZipArchive::CREATE);
                $files = scandir("downloads/$folder");
                unset($files[0]);
                unset($files[1]);
    
                foreach ($files as $file) {
                    $zip->addFile("downloads/$folder/$file", $file);
                }
            $zip->close();
        }

        // Re-encode, check, and rename files.
        if(file_exists("downloads/$folder/file.mp3")) {

            rename("downloads/$folder/file.mp3", "downloads/$folder/$youtube_title.mp3");
            return "downloads/$folder/$youtube_title.mp3";

        } elseif($download_option == "wav-audio") {

            if(file_exists("downloads/$folder/file.mp4")) {
                runCommand("$ffmpeg -i downloads/$folder/file.mp4 downloads/$folder/file.wav");
                rename("downloads/$folder/file.wav", "downloads/$folder/$youtube_title.wav");
            } elseif(file_exists("downloads/$folder/file.webm")) {
                runCommand("$ffmpeg -i downloads/$folder/file.webm downloads/$folder/file.wav");
                rename("downloads/$folder/file.wav", "downloads/$folder/$youtube_title.wav");
            }
            return "downloads/$folder/$youtube_title.wav";

        } elseif(file_exists("downloads/$folder/file.flac")) {

            rename("downloads/$folder/file.flac", "downloads/$folder/$youtube_title.flac");
            return "downloads/$folder/$youtube_title.flac";

        }  elseif(file_exists("downloads/$folder/files.zip") && $download_option !== "third-party") {

            rename("downloads/$folder/files.zip", "downloads/$folder/$youtube_title.zip");
            return "downloads/$folder/$youtube_title.zip";

        }  elseif(file_exists("downloads/$folder/files.zip") && $download_option == "third-party") {

            rename("downloads/$folder/files.zip", "downloads/$folder/$youtube_title.zip");
            return "downloads/$folder/$youtube_title.zip";

        } elseif ($download_option == "avi") {

            if(file_exists("downloads/$folder/file.mp4")) {
                runCommand("$ffmpeg -i downloads/$folder/file.mp4 -c:v copy -c:a copy downloads/$folder/file.avi");
            } elseif(file_exists("downloads/$folder/file.webm")) {
                runCommand("$ffmpeg -i downloads/$folder/file.webm -c:v copy -c:a copy downloads/$folder/file.avi");
            }
            rename("downloads/$folder/file.avi", "downloads/$folder/$youtube_title.avi");
            return "downloads/$folder/$youtube_title.avi";

        } elseif($download_option == "mkv" && $recode_video == false) {

            if(file_exists("downloads/$folder/file.mp4")) {
                rename("downloads/$folder/file.mp4", "downloads/$folder/$youtube_title.mkv");
            } elseif(file_exists("downloads/$folder/file.webm")) {
                rename("downloads/$folder/file.webm", "downloads/$folder/$youtube_title.mkv");
            }
            return "downloads/$folder/$youtube_title.mkv";

        }elseif(file_exists("downloads/$folder/file.mkv")) {

            rename("downloads/$folder/file.mkv", "downloads/$folder/$youtube_title.mkv");
            return "downloads/$folder/$youtube_title.mkv";

        } elseif(file_exists("downloads/$folder/file.webm")) {

            rename("downloads/$folder/file.webm", "downloads/$folder/$youtube_title.webm");
            return "downloads/$folder/$youtube_title.webm";

        } elseif(file_exists("downloads/$folder/file.mp4")) {

            rename("downloads/$folder/file.mp4", "downloads/$folder/$youtube_title.mp4");
            return "downloads/$folder/$youtube_title.mp4";

        }   else {

            return "";

        }
    }

    #####################################################################################
    #
    #   HELPER FUNCTIONS
    #
    #####################################################################################
    function runCommand($command) {
        exec($command." 2>&1", $output, $result);

        if($result) {
            jError("System Error: ".$output[0]);
        }
    }

    // Sends app error back to user.
    function jError($message) {
        $data['error'] = $message;
        echo json_encode("App Error: ".$data);
        die();
    }

    // Clean up temp folder.
    function cleanUp($folder) {
        $files = scandir("temp/$folder");
        unset($files[0]);
        unset($files[1]);
    
        foreach ($files as $file) {
            if($file !== "animated.mp4" && $file !== "animated.gif" && $file !== "animated.png" && $file !== "animated.webp") {
                //unlink("temp/$folder/$file");
            }
        }
    }