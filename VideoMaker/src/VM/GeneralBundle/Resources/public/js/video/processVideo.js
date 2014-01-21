var record = document.getElementById('record');
var stop = document.getElementById('stop');
var deleteFiles = document.getElementById('delete');

var audio = document.querySelector('audio');

var recordVideo = document.getElementById('record-video');
var preview = document.getElementById('preview');

var container = document.getElementById('container');
var recordAudio, recordVideo;

var video_constraints = {
    mandatory: {
        maxHeight: 240,
        maxWidth: 320
    },
    optional: []
};
this.navigator.getUserMedia({
    audio: true,
    video: video_constraints
}, this.onSuccess, onError);

function onSuccess(stream) {
    if (navigator.webkitGetUserMedia || navigator.mozGetUserMedia) {
        preview.src = window.URL.createObjectURL(stream);
        preview.play()
        // var legalBufferValues = [256, 512, 1024, 2048, 4096, 8192, 16384];
        // sample-rates in at least the range 22050 to 96000.
        recordAudio = RecordRTC(stream, {
            //bufferSize: 16384,
            //sampleRate: 45000
        });

        recordVideo = RecordRTC(stream, {
            type: 'video'
        });
    }
    else if (navigator.msGetUserMedia) {
        //future implementation over internet explorer
    }
    else {
        preview.src = stream;
    }
    preview.play();
}

function onError() {
  alert('There has been a problem retrieving the streams - did you allow access?');
}

function renderProgress(elem) {
    canvas1 = document.getElementById(elem + '_timer'),
            ctx1 = canvas1.getContext('2d'),
            sec = $(canvas1).data('seconds') | 0,
            countdown = sec;

    ctx1.lineWidth = 10;
    ctx1.strokeStyle = "#f9f9f9";
    ctx1.fillStyle = '#666666';
    ctx1.textAlign = 'center';
    ctx1.font = '20px Arial';
    var
            startAngle = (-Math.PI / 2),
            time = 0,
            intv = setInterval(function() {

                var endAngle = (Math.PI * time * 2 / sec) - Math.PI / 2;
                ctx1.arc(50, 50, 42, startAngle, endAngle, false);
                startAngle = endAngle;
                ctx1.stroke();

                ctx1.clearRect(20, 30, 55, 70);
                ctx1.fillText(countdown-- + ' s', 50, 58);

                if (++time > sec) {
                    clearInterval(intv);
                }

            }, 1000);
}

function xhr(url, data, progress, callback) {
    var request = new XMLHttpRequest();
    request.onreadystatechange = function() {
        if (request.readyState == 4 && request.status == 200) {
            callback(request.responseText);
        }
    };

    request.onprogress = function(e) {
        if (!progress)
            return;
        if (e.lengthComputable) {
            progress.value = (e.loaded / e.total) * 100;
            progress.textContent = progress.value; // Fallback for unsupported browsers.
        }

        if (progress.value == 100) {
            progress.value = 0;
        }
    };
    request.open('POST', url);
    request.send(data);
}

function stopRecording(fileName) {
    $('#stopButton').hide();
    $('#content_response_timer').hide();
    $('#text').html('<p>Votre vidéo est en cours de traitement.</p>');

    recordAudio.stopRecording();
    PostBlob(recordAudio.getBlob(), 'audio', fileName + '.wav');

    recordVideo.stopRecording();
    PostBlob(recordVideo.getBlob(), 'video', fileName + '.webm');

    preview.src = '';
    deleteFiles.disabled = false;
}

window.onbeforeunload = function() {
    if (!!fileName) {
        deleteAudioVideoFiles();
        return 'It seems that you\'ve not deleted audio/video files from the server.';
    }
};