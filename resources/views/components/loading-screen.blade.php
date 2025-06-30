<div id="loading-screen" class="fixed inset-0 flex items-center justify-center bg-black z-50 text-green-400 font-mono"
    style="display: none;">
    <!-- Terminal-Style Loading Text -->
    <div class="relative z-10 text-center text-lg font-bold text-green-400 font-mono">
        <img src="{{ asset('images/itopslogo.png') }}" alt="Your Logo" class="w-40 h-auto mx-auto mb-4">
        <p id="loading-text" class="tracking-wider text-xl font-semibold text-green-300 opacity-0 shadow-lg bg-black bg-opacity-50 px-4 py-2 rounded-lg border border-green-500"></p>
    </div>
</div>

<style>
    @keyframes fadeIn {
        from {
            opacity: 0;
            transform: translateY(10px);
        }

        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    @keyframes fadeOut {
        from {
            opacity: 1;
            transform: translateY(0);
        }

        to {
            opacity: 0;
            transform: translateY(-10px);
        }
    }

    .fade-in {
        animation: fadeIn 0.3s ease-in-out forwards;
    }

    .fade-out {
        animation: fadeOut 0.3s ease-in-out forwards;
    }

    .cursor::after {
        content: "_";
        animation: blink 0.6s infinite alternate;
    }

    @keyframes blink {
        0% {
            opacity: 1;
        }

        50% {
            opacity: 0;
        }
    }

    #loading-screen {
        transition: opacity 0.5s ease-in-out;
    }
</style>

<script>
    function showTextSequentially() {
        const texts = [
            "Initializing System...",
            `Verifying Account... you have {{ Auth::user()->role }} account`,
            `Access Granted. Welcome! {{ Auth::user()->name }}`
        ];

        const element = document.getElementById("loading-text");
        let index = 0;

        function typeText(text, callback) {
            let i = 0;
            element.textContent = "";
            element.classList.add("cursor");
            element.classList.add("fade-in");

            function type() {
                if (i < text.length) {
                    element.textContent += text.charAt(i);
                    i++;
                    setTimeout(type, 40);
                } else {
                    element.classList.remove("cursor");
                    setTimeout(() => {
                        element.classList.add("fade-out");
                        setTimeout(() => {
                            element.textContent = "";
                            element.classList.remove("fade-in", "fade-out");
                            callback();
                        }, 300);
                    }, 700);
                }
            }
            type();
        }

        function nextText() {
            if (index < texts.length) {
                typeText(texts[index], () => {
                    index++;
                    nextText();
                });
            } else {
                setTimeout(() => {
                    document.getElementById("loading-screen").style.opacity = "0";
                    setTimeout(() => {
                        document.getElementById("loading-screen").style.display = "none";
                    }, 500);
                }, 200);
            }
        }

        nextText();
    }

    window.onload = function () {
        const loadingScreen = document.getElementById("loading-screen");

        // Only show loading screen if it hasn't been displayed in this session
        if (!sessionStorage.getItem("loadingScreenShown")) {
            loadingScreen.style.display = "flex";  // Show loading screen
            setTimeout(() => {
                showTextSequentially();
            }, 100); // Small delay to ensure visibility

            sessionStorage.setItem("loadingScreenShown", "true"); // Store session flag
        } else {
            loadingScreen.style.display = "none";
        }
    };
</script>
