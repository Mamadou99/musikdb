This project is under heavy development and far from being ready for everyday usage. I'm primarily developing it to manage my own music collection and to listen to it from other places via the browser.

Since I'm currently studying and working at the same time I can't offer any support.

The following features are currently implemented, some of them may be quite buggy though.

  * Search tracks in database
  * Directory browser
  * Manage playlists
  * Show album cover from folders
  * Crossfading between tracks
  * Transcoding using ffmpeg (path to binary can be defined in config)
  * Database containing ID3 tags, tracks lengths, bitrates, etc.
  * Download directories (ZIP file is created on-the-fly and pushed to the client)
  * Support for multiple users (admin/non-admin)
  * Separated into server/client
  * Support for multiple streaming backends
  * Per-user settings
  * SSL support

Based on the [Yii Framework](http://www.yiiframework.com) and [jPlayer](http://www.happyworm.com/jquery/jplayer/) for audio streaming.