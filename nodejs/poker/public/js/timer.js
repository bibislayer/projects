var cdown;
function JBCountDown(settings) {
    var glob = settings;

    function deg(deg) {
        return (Math.PI / 180) * deg - (Math.PI / 180) * 90;
    }

    function formatage(nombre) {
        var zero = "";
        if (nombre < 10) {
            zero = "0";
        }
        return zero + nombre;
    }

    glob.total = Math.floor(glob.timeLimit);
    glob.days = Math.floor(glob.timeLimit / 86400);
    glob.hours = 24 - Math.floor(glob.timeLimit % 86400 / 3600);
    glob.minutes = 60 - Math.floor(glob.timeLimit % 86400 % 3600 / 60);
    glob.seconds = 60 - Math.floor(glob.timeLimit % 86400 % 3600 % 60);
    glob.secLeft = Math.floor(glob.timeLimit);
    

    //clear other timer
    $('canvas').each(function(){
       var cSecc = $(this).get(0);
       var ctxx = cSecc.getContext("2d");
       ctxx.clearRect(0, 0, 98, 98);
    });
    if (glob.secLeft == 0) {
        console.log('round table');
        return;
    }

    var clock = {
        set: {
            seconds: function() {
                glob.secLeft--;
                //console.log('#' + glob.selector+glob.id);
                var cSec = $('#' + glob.selector+glob.id).get(0);
                var ctx = cSec.getContext("2d");
                ctx.clearRect(0, 0, cSec.width, cSec.height);
                ctx.beginPath();
                ctx.strokeStyle = glob.secondsColor;

                ctx.shadowBlur = 9;
                ctx.shadowOffsetX = 0;
                ctx.shadowOffsetY = 0;
                ctx.shadowColor = glob.secondsGlow;

                var degs = (360 / Math.floor(glob.timeLimit)) * (Math.floor(glob.timeLimit) - glob.secLeft-1);

                ctx.arc(cSec.width / 2, cSec.height / 2, ((cSec.height + cSec.width) / 2) * 0.43, deg(0), deg(degs));
                ctx.lineWidth = 3;
                ctx.stroke();
                $('.content_time').hide();
                $("#content_time_"+glob.id).show();
                $("#content_time_"+glob.id+" .secs").text(formatage(60 - glob.seconds));
                $("#content_time_"+glob.id+" .mins").text(formatage(60 - glob.minutes));
                $("#content_time_"+glob.id+" .hrs").text(formatage(24 - glob.hours));
                $("#content_time_"+glob.id+" .days").text(formatage(glob.days));

            }
        },
        start: function() {
            /* Seconds */
            cdown = setInterval(function() {
                if (glob.seconds > 59) {
                    if (60 - glob.minutes === 0 && 24 - glob.hours === 0 && glob.days === 0) {
                        
                        glob.next();
                            
                        /* Countdown is complete */
                        return;
                    }
                    glob.seconds = 1;

                    if (glob.minutes > 59) {
                        glob.minutes = 1;

                        if (glob.hours > 23) {
                            glob.hours = 1;
                            if (glob.days > 0) {
                                glob.days--;
                            }
                        } else {
                            glob.hours++;
                        }
                    } else {
                        glob.minutes++;
                    }
                } else {
                    glob.seconds++;
                }
                clock.set.seconds();
            }, 1000);
        }
    };

    clock.set.seconds();
    clock.start();
}