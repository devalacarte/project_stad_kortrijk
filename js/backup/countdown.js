var remainingSeconds; // remaining seconds
var resetCount; // id number of current resetcount

function calcRemainingSeconds(startTime, durationInMins, currentTime) {
    /* Calcs remaining seconds of the timer */

    var endTime = startTime + (durationInMins * 60);
    // set the amounts of seconds that are remaining
    setRemainingSeconds(endTime - currentTime);
}

function setRemainingSeconds(secs) {
    /* Sets the amounts of seconds that are given in the parameter */

    remainingSeconds = secs;
}

function setResetcount(numberOfResets) {
    /* Sets the resetcount */

    resetCount = numberOfResets;
}

function startTimer() {
    /* Starts the countdown */

    // do a tick
    countdownTick();
    // start the interval (each second)
    setInterval('countdownTick()', 1000);
}

function countdownTick() {
    /* Executed each second */

    var hours = Math.floor(remainingSeconds / 3600); // hours
    var minutes = Math.floor((remainingSeconds - (hours * 3600)) / 60); // minutes
    var seconds = Math.floor((remainingSeconds - (hours * 3600) - (minutes * 60))); // seconds

    var h1, h2, m1, m2, s1, s2;

    h1 = hours % 100 / 10 | 0;
    h2 = hours % 10;
    m1 = minutes % 100 / 10 | 0;
    m2 = minutes % 10;
    s1 = seconds % 100 / 10 | 0;
    s2 = seconds % 10;

    $('#tmr-h1').html(h1);
    $('#tmr-h2').html(h2);
    $('#tmr-m1').html(m1);
    $('#tmr-m2').html(m2);
    $('#tmr-s1').html(s1);
    $('#tmr-s2').html(s2);

    // check if remaining seconds is less or equal to 0
    if (remainingSeconds <= 0) {
        // show waiting text...
        $('#tmr-h1').html('m');
        $('#tmr-h2').html('o');
        $('#tmr-m1').html('m');
        $('#tmr-m2').html('e');
        $('#tmr-s1').html('n');
        $('#tmr-s2').html('t');
        // ajax load update (with resetCount)
        $.get("update.php?resetCount=" + resetCount);
    } else {
        // decrease remaining seconds with 1
        remainingSeconds--;
    }
}