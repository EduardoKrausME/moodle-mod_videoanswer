# mod_videoanswer

Moodle activity for very short video responses recorded directly with the learner's webcam or phone camera.

## Main features

- Teacher writes a prompt such as “Explain this concept in up to 60 seconds”.
- Recording limits: 30, 60, or 120 seconds.
- Browser recording through `getUserMedia` + `MediaRecorder`.
- Preview before submission.
- Optional re-recording/replacement after submission.
- One current submission per learner.
- Teacher report with learner, duration, submission time, and inline video playback.
- Moodle File API storage with capability checks and authenticated `pluginfile.php` delivery.
- Privacy API implementation.

## Requirements

- Moodle 4.5 or newer.
- HTTPS is required by modern browsers for camera/microphone access, except on localhost.
- A browser with `MediaRecorder` support.

## Installation

Install the folder as `mod/videoanswer` or upload the ZIP through Moodle plugin installation.
