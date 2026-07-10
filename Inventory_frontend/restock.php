<?php
?>
<!DOCTYPE html>
<html lang="en">
<head>
<title>Relapse</title>

<style>

body {
    margin: 0;
    height: 100vh;
    display: flex;
    justify-content: center;
    align-items: center;
    background: #111;
    color: white;
    font-family: Arial, sans-serif;
    overflow: hidden;
}

.lyrics-container {
    text-align: center;
    font-size: 2rem;
    width: 80%;
    max-width: 900px;
    line-height: 1.8;
}

.line {
    opacity: 0;
    transform: translateY(10px);
    transition: 0.4s;
    margin: 10px 0;
}

.line.show {
    opacity: 1;
    transform: translateY(0);
}

.cursor {
    display: inline-block;
    animation: blink 0.8s infinite;
}

@keyframes blink {
    50% {
        opacity: 0;
    }
}
</style>
</head>

<body>

<div class="lyrics-container" id="lyrics"></div>

<audio id="bgMusic" loop>
    <source src="../Images/relapse.mp3" type="audio/mpeg">
</audio>

<script>

const audio = document.getElementById("bgMusic");

audio.volume = 0.2;

const lyrics = [
    ["Oh, ooh", 4600, 490],
    ["Mamamatay akong nakangiti", 1220, 130],
    ["Kapag ikaw ang nasa aking tabi", 850, 130],
    ["Mabubuhay akong nagsisisi", 1000, 120],
    ["Kapag isang araw hindi kita mapapangiti", 1130, 70],
    ["Kalapastangan ang 'di ka ibigin", 1130, 100],
    ["Kalokohan ang 'di ka isipin", 1130, 100],
    ["Kung ang mundo ay biglang gugunawin", 1120, 100],
    ["Ikaw ang una kong hahanapin", 1120, 100],
];

const container = document.getElementById("lyrics");

function sleep(ms) {
    return new Promise(resolve => setTimeout(resolve, ms));
}

async function typeLine(text, lineDelay, charDelay) {

    const line = document.createElement("div");

    line.className = "line show";
    container.appendChild(line);

    let currentText = "";

    for (const char of text) {
        currentText += char;
        line.innerHTML =
            currentText + '<span class="cursor">|</span>';
        await sleep(charDelay);
    }
    line.innerHTML = currentText;
    await sleep(lineDelay);
}

async function playLyrics() {

    for (const lyric of lyrics) {
        await typeLine(...lyric);
    }
    await sleep(1500);
    window.location.href = "projects.html";
}

async function startExperience() {

    try {
        await audio.play();
        playLyrics();
    } catch (err) {
        document.body.addEventListener("click", async () => {
            await audio.play();
            playLyrics();
        }, { once: true });
    }
}

startExperience();

</script>

</body>
</html>
