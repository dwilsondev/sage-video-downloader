<?php

    // Set the default download option in the Web form.
    // Set to `original`, `4k`, `2k`, `1080`, `720`, 
    // `webm`, `mkv`, `avi`, `playlists`, `mp3-playlist`, 
    // `mp3-audio`, `wav-audio`, `flac-audio`, `third-party`, 
    // `third-party-playlist`
    $default_download_option = "original";

    // Enable or disable download options. Set to
    // `enabled` or `disabled`.
    $download_options = [
        "original" => "enabled",
        "4k" => "enabled",
        "2k" => "enabled",
        "1080" => "enabled",
        "720" => "enabled",
        "webm" => "enabled",
        "mkv" => "enabled",
        "avi" => "disabled",
        "playlist" => "enabled",
        "mp3-playlist" => "enabled",
        "mp3-audio" => "enabled",
        "wav-audio" => "disabled",
        "flac-audio" => "disabled",
        "third-party" => "disabled",
        "third-party-playlist" => "disabled",
    ];

    // Set whether to recode MKV, WebM, and AVI downloads.
    $recode_video = false;

    // Set whether to re-encode MP4, MKV, AVI, and WebM as HEVC MP4.
    // Set to `true` or `false`.
    // DO NOT enable this with $recode_video. Doing so will double
    // processing and download times and may degrade download quality.
    $recode_h265 = false;

    // Set HEVC options for FFmpeg.
    $h265_options = "-c:v libx265 -crf 21 -preset fast -c:a aac -b:a 320k";
    
    // Set YouTube DLP and FFmpeg binary environments. Set to `bin` to
    // use local binaries in the app/bin folder. Set to "" or empty,
    // to use system installed binaries.
    $youtube_dlp_env = "bin";
    $ffmpeg_env = "bin";

    