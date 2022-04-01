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

    if(!is_dir("../downloads")) {
        mkdir("../downloads");
    }
    
    $folder = uniqid();
    mkdir("../downloads/".$folder);

    #####################################################################################
    #
    #   SET YOUTUBE DLP OPTIONS
    #
    #####################################################################################
    if($download_option == "original") {
        $options = "-f bestvideo[ext=mp4]+bestaudio[ext=m4a]/best[ext=mp4]/best";
    } elseif($download_option == "4k") {
        $options = "-f bestvideo[height=2160]+bestaudio/best[height=2160]/best";
    } elseif($download_option == "2k") {
        $options = "-f bestvideo[height=1440]+bestaudio/best[height=1440]/best";
    } elseif($download_option == "1080") {
        $options = "-f bestvideo[height=1080]+bestaudio/best[height=1080]/best";
    } elseif($download_option == "720") {
        $options = "-f bestvideo[height=720]+bestaudio/best[height=720]/best";
    } elseif($download_option == "webm") {
        if($recode_video == true) {
            $options = "-f bestvideo[ext=mp4]+bestaudio[ext=m4a]/best[ext=mp4]/best --recode webm";
        } else {
            $options = "-f bestvideo[ext=webm]+bestaudio[ext=m4a]/best[ext=webm]";
        }
    } elseif($download_option == "mkv") {
        if($recode_video == true) {
            $options = "-f bestvideo[ext=mp4]+bestaudio[ext=m4a]/best[ext=mp4]/best --recode-video mkv";
        } else {
            $options = "-f bestvideo[ext=mp4]+bestaudio[ext=m4a]/best[ext=mp4]/best";
        }
    } elseif($download_option == "avi") {
        if($recode_video == true) {
            $options = "-f bestvideo[ext=mp4]+bestaudio[ext=m4a]/best[ext=mp4]/best --recode-video avi";
        } else {
            $options = "-f bestvideo[ext=mp4]+bestaudio[ext=m4a]/best[ext=mp4]/best"; 
        }
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

    #####################################################################################
    #
    #   RECODE H265
    #
    #####################################################################################
    $ext = strtolower(pathinfo($file, PATHINFO_EXTENSION));
    if($recode_h265 == true && ($ext == "mp4" || $ext == "webm" || $ext == "mkv" || $ext == "avi")) {
        if($ext == "mp4" || $ext == "avi" || $ext == "mkv") {
            $filename = substr($file, 0, -4);
        } elseif($ext == "webm") {
            $filename = substr($file, 0, -5);
        }

        exec("$ffmpeg -i ../downloads/$folder/$file $h265_options ../downloads/$folder/$filename"."_hevc.mkv");

        unlink("../downloads/$folder/$file");
        $file = $filename."_hevc.mkv";
    }

    #####################################################################################
    #
    #   GENERATE DOWNLOAD LINK
    #
    #####################################################################################
    if(file_exists("../downloads/$folder/$file")) {
        $filename = str_replace("-", " ", $file);
        rename("../downloads/$folder/$file", "../downloads/$folder/$filename");

        $data['link'] = "downloads/$folder/$filename";
        echo json_encode($data);
    } else {
        $data['error'] = "";
        jError("Couldn't find video. Try choosing a different format.");
    } 
    
    /**
     * downloadVideo
     * 
     * Download YouTube videos, YouTube playlists, and videos
     * from third party websites. 
     *
     * @param  mixed $download_option
     * @param  mixed $url
     * @param  mixed $folder
     * @param  mixed $options
     * @return void
     */
    function downloadVideo($download_option, $url, $folder, $options = "")
    {
        include "config.php";
        include "env.php";
        
        $youtube_title = exec("$youtube_dlp --get-filename -o %(title)s $url");

        // Download single video.
        if($download_option !== "playlist" && $download_option !== "mp3-playlist" && $download_option !== "mp3-playlist" && $download_option !== "third-party-playlist") {
            exec("$youtube_dlp $options -o ../downloads/$folder/file.%(ext)s $url");
        }

        // Download playlists.
        if($download_option == "playlist" || $download_option == "mp3-playlist" || $download_option == "third-party-playlist") {
            $playlist_title = exec("$youtube_dlp --get-filename -o %(playlist_title)s $url");

            if($playlist_title == "NA") {
                jError("Couldn't download playlist.");
            } else {
                $youtube_title = $playlist_title;
            }

            exec("$youtube_dlp $options -o ../downloads/$folder/%(title)s.%(ext)s $url");
        }

        $youtube_title = str_replace(" ", "-", $youtube_title); // Prevent space in file name errors.

        // Check and name single file download.
        if($download_option !== "playlist" && $download_option !== "mp3-playlist" && $download_option !== "third-party-playlist") {
            $files = scandir("../downloads/$folder");
            unset($files[0]);
            unset($files[1]);
            
            if(!isset($files[2])) {
                jError("Couldn't download file.");
            }
            
            $ext = strtolower(pathinfo($files[2], PATHINFO_EXTENSION));
            rename("../downloads/$folder/$files[2]", "../downloads/$folder/$youtube_title.$ext");
            $file_download = "$youtube_title.$ext";              
        }

        // Convert and/or rename files.
        if($download_option == "original" || $download_option == "4k" || $download_option == "2k" || $download_option == "1080" || $download_option == "720" || $download_option == "third-party") {

            return $file_download;

        } elseif($download_option == "webm") {

            rename("../downloads/$folder/$file_download", "../downloads/$folder/$youtube_title.webm");
            cleanUp($folder, "webm");

            return "$youtube_title.webm";
            
        } elseif($download_option == "mkv") {

            rename("../downloads/$folder/$file_download", "../downloads/$folder/$youtube_title.mkv");
            cleanUp($folder, "mkv");

            return "$youtube_title.mkv";

        } elseif($download_option == "avi") {

            exec("$ffmpeg -i ../downloads/$folder/$file_download -c:v copy -c:a copy ../downloads/$folder/$youtube_title.avi");
            cleanUp($folder, "avi");

            return "$youtube_title.avi";

        } elseif($download_option == "mp3-audio") {

            cleanUp($folder, "mp3");

            return $file_download;

        } elseif($download_option == "wav-audio") {

            exec("$ffmpeg -i ../downloads/$folder/$file_download ../downloads/$folder/$youtube_title.wav");
            cleanUp($folder, "wav");

            return "$youtube_title.wav";

        } elseif($download_option == "flac-audio") {

            exec("$ffmpeg -i -i ../downloads/$folder/$file_download ../downloads/$folder/$youtube_title.flac");
            cleanUp($folder, "flac");

            return "$youtube_title.flac";

        } elseif($download_option == "playlist" || $download_option == "mp3-playlist" ||$download_option == "third-party-playlist") {

            $zip = new ZipArchive();
            $filename = "files.zip";

            // Zip up files.
            $zip->open("../downloads/$folder/$filename", ZipArchive::CREATE);
                $files = scandir("../downloads/$folder");
                unset($files[0]);
                unset($files[1]);
    
                foreach ($files as $file) {
                    $zip->addFile("../downloads/$folder/$file", $file);
                }
            $zip->close();

            cleanUp($folder, "zip");

            rename("../downloads/$folder/files.zip", "../downloads/$folder/$youtube_title.zip");
            return "$youtube_title.zip";

        } else {

            return "";

        }
    }

    #####################################################################################
    #
    #   HELPER FUNCTIONS
    #
    #####################################################################################    
    /**
     * jError
     * 
     * Sends app error back to user.
     *
     * @param  mixed $message
     * @return void
     */
    function jError($message) {
        $data['error'] = $message;
        echo json_encode($data);
        die();
    }
    
    /**
     * cleanUp
     * 
     * Delete temp files.
     *
     * @param  mixed $folder
     * @param  mixed $keep_ext
     * @return void
     */
    function cleanUp($folder, $keep_ext) {
        $files = scandir("../downloads/$folder");
        unset($files[0]);
        unset($files[1]);
    
        if(!empty($files)) {
            foreach ($files as $file) {
                $ext = strtolower(pathinfo($file, PATHINFO_EXTENSION));

                if($ext !== $keep_ext) {
                    unlink("../downloads/$folder/$file");
                }
            }            
        }
    }