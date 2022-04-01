<?php

    if($youtube_dlp_env == "bin") {
        $youtube_dlp = realpath("bin") . DIRECTORY_SEPARATOR . "youtube-dlp";
    } else {
        $youtube_dlp = "youtube-dlp";
    }

    if($ffmpeg_env == "bin") {
        $ffmpeg = realpath("bin") . DIRECTORY_SEPARATOR . "ffmpeg";
    } else {
        $ffmpeg = "ffmpeg";
    }