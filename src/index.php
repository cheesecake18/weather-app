<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Weatherify | Dynamic Insights</title>
    <!-- Load Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Configure Tailwind with Custom Colors -->
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        'base-bg': '#F1F3E0', // Base background
                        'card-panel': '#D2DCB6', // Cards / Panels
                        'accent-btn': '#A1BC98', // Accent / Buttons
                        'text-dark': '#1C352D', // Text / Icons
                    },
                    fontFamily: {
                        sans: ['Inter', 'sans-serif'],
                        serif: ['Lora', 'serif'],
                    }
                }
            }
        }
    </script>
    <!-- Custom Styles for Typography, GIF Container, and Dynamic Backgrounds -->
    <link rel="stylesheet" href="style.css">
</head>

<body class="flex flex-col items-center p-4 sm:p-8 transition-colors duration-1000">

    <!-- Header and Search Bar -->
    <header class="w-full max-w-4xl mb-8">
        <div class="flex items-center justify-between p-4 bg-card-panel rounded-xl shadow-lg mb-6">
            <h1 class="main-title text-3xl font-bold text-text-dark">
                Weatherify
            </h1>
            <svg class="w-8 h-8 text-accent-btn" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"></path>
            </svg>
        </div>

        <!-- Search Input -->
        <div class="flex gap-2">
            <button onclick="window.location.href='dashboard.php'" style="background-color: #1C352D; color: white;" class="font-semibold px-4 py-3 rounded-xl hover:opacity-80 transition duration-150 shadow-md active:shadow-inner active:translate-y-0.5">
                Dashboard
            </button>
            <div class="relative flex-1">
                <input type="text" id="cityInput" placeholder="Enter city name (e.g., London)"
                       class="w-full p-3 rounded-xl border-2 border-accent-btn/50 focus:outline-none focus:ring-2 focus:ring-accent-btn/80 text-text-dark bg-base-bg placeholder-text-dark/70 transition duration-150 shadow-inner"
                       value=""
                       aria-label="City name input">
                <div id="suggestions" class="absolute top-full left-0 right-0 bg-base-bg border border-accent-btn/50 rounded-xl shadow-lg mt-1 z-10 hidden max-h-60 overflow-y-auto"></div>
            </div>
            <button id="searchButton"
                    style="background-color: #1C352D; color: white;" class="font-semibold p-3 rounded-xl hover:opacity-80 transition duration-150 shadow-md active:shadow-inner active:translate-y-0.5">
                Fetch Weather
            </button>
            <button id="locationButton"
                    title="Use Current Location"
                    style="background-color: #1C352D; color: white;" class="font-semibold p-3 rounded-xl hover:opacity-80 transition duration-150 shadow-md active:shadow-inner active:translate-y-0.5 w-12 flex items-center justify-center">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.828 0L6.343 16.657A8 8 0 1117.657 16.657z"></path>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                </svg>
            </button>
        </div>
    </header>

    <!-- Main Content Area -->
    <main class="w-full max-w-4xl">
        <div id="loadingIndicator" class="hidden text-center text-text-dark/80 p-4">
            <div class="animate-spin inline-block w-8 h-8 border-4 border-t-4 border-text-dark border-t-transparent rounded-full"></div>
            <p id="loadingText" class="mt-2">Fetching data...</p>
        </div>

        <div id="weatherCard" class="hidden bg-card-panel p-6 rounded-2xl shadow-xl transition-all duration-500 ease-out">
            
            <!-- START: Main Split Layout (md:grid-cols-5, 2/5 for weather, 3/5 for dashboard) -->
            <div class="grid grid-cols-1 md:grid-cols-5 gap-6">
                
                <!-- LEFT COLUMN (md:col-span-2: Main Weather & Current Temperature) -->
                <div class="md:col-span-2 flex flex-col">
                    <!-- City, Date, and Main GIF/Description Section (Reused structure) -->
                    <div class="flex flex-col items-center mb-4 border-b md:border-b-0 md:border-r border-accent-btn/30 md:pr-6 pb-4 md:pb-0">
                        
                        <!-- Top: Location and Current Temp -->
                        <h2 id="cityAndDate" class="text-3xl font-serif font-bold mb-1 text-center"></h2>
                        <div class="flex items-start mb-4">
                            <span id="currentTemp" class="text-8xl font-bold font-sans"></span>
                            <span class="text-3xl font-light mt-4">&deg;C</span>
                        </div>
                        
                        <!-- Middle: GIF & Description -->
                        <div class="weather-gif-container mb-4 rounded-xl shadow-inner border border-accent-btn/30">
                            <img id="weatherGif" src="" alt="Weather animation" class="weather-gif">
                        </div>
                        <p id="weatherDescription" class="text-xl font-semibold capitalize text-text-dark/90 mb-4"></p>
                        
                        <!-- Bottom: Share Button -->
                        <button id="shareButton" class="w-full max-w-[200px] text-sm text-base-bg bg-text-dark hover:bg-accent-btn/80 p-2 rounded-lg transition duration-150 shadow-md">
                            Share Report
                        </button>
                    </div>
                </div>

                <!-- RIGHT COLUMN (md:col-span-3: Full 2x2 Dashboard Metrics & Insights) -->
                <div class="md:col-span-3 space-y-6 md:pl-6">
                    
                    <!-- ROW 1: Essential Metrics (Left) and Daily Insights (Right) -->
                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                        
                        <!-- Essential Metrics (Original Stats) -->
                        <div class="p-4 bg-base-bg rounded-xl shadow-md">
                            <h3 class="text-lg font-serif font-bold text-text-dark mb-3 border-b border-accent-btn/50 pb-2">Essential Metrics</h3>
                            
                            <div class="grid grid-cols-2 gap-3 text-sm">
                                
                                <!-- Feels Like -->
                                <div class="flex flex-col items-center p-2 bg-card-panel rounded-lg shadow-inner">
                                    <span class="font-medium text-text-dark/90 text-xs">Feels Like:</span>
                                    <span id="feelsLike" class="font-bold text-accent-btn text-base"></span>
                                </div>
                                
                                <!-- Humidity -->
                                <div class="flex flex-col items-center p-2 bg-card-panel rounded-lg shadow-inner">
                                    <span class="font-medium text-text-dark/90 text-xs">Humidity:</span>
                                    <span id="humidity" class="font-bold text-accent-btn text-base"></span>
                                </div>
                                
                                <!-- Wind Speed -->
                                <div class="flex flex-col items-center p-2 bg-card-panel rounded-lg shadow-inner">
                                    <span class="font-medium text-text-dark/90 text-xs">Wind Speed:</span>
                                    <span id="windSpeed" class="font-bold text-accent-btn text-base"></span>
                                </div>
                                
                                <!-- Pressure -->
                                <div class="flex flex-col items-center p-2 bg-card-panel rounded-lg shadow-inner">
                                    <span class="font-medium text-text-dark/90 text-xs">Pressure 🌡️</span>
                                    <span id="pressure" class="font-bold text-accent-btn text-base"></span>
                                </div>
                            </div>
                        </div>

                        <!-- Daily Insights (A) -->
                        <div class="p-4 bg-base-bg rounded-xl shadow-md">
                            <h3 class="text-lg font-serif font-bold text-text-dark mb-3 border-b border-accent-btn/50 pb-2">Daily Insights</h3>

                            <div class="grid grid-cols-3 gap-3 text-sm">
                                
                                <!-- UV Index -->
                                <div class="flex flex-col items-center p-2 bg-card-panel rounded-lg shadow-inner">
                                    <span class="font-medium text-text-dark/90 text-xs flex items-center justify-center gap-1">UV 🔆</span>
                                    <div class="flex flex-col items-center gap-0.5">
                                        <span id="uvIndexValue" class="font-bold text-accent-btn text-base"></span>
                                        <span id="uvRiskBadge" class="px-2 py-0.5 rounded-full text-[10px] font-semibold"></span>
                                    </div>
                                </div>
                                
                                <!-- Air Quality (AQI) -->
                                <div class="flex flex-col items-center p-2 bg-card-panel rounded-lg shadow-inner">
                                    <span class="font-medium text-text-dark/90 text-xs flex items-center justify-center gap-1">AQI 💨</span>
                                    <div class="flex flex-col items-center gap-0.5">
                                        <span id="aqiValue" class="font-bold text-accent-btn text-base"></span>
                                        <span id="aqiRiskBadge" class="px-2 py-0.5 rounded-full text-[10px] font-semibold"></span>
                                    </div>
                                </div>

                                <!-- Rain Chance (PoP) -->
                                <div class="flex flex-col items-center p-2 bg-card-panel rounded-lg shadow-inner">
                                    <span class="font-medium text-text-dark/90 text-xs">Rain 💧</span>
                                    <span id="pop" class="font-bold text-accent-btn text-base"></span>
                                </div>
                                
                                <!-- Sunrise -->
                                <div class="flex flex-col items-center p-2 bg-card-panel rounded-lg shadow-inner">
                                    <span class="font-medium text-text-dark/90 text-xs">Sunrise 🌅</span>
                                    <span id="sunriseTime" class="font-bold text-accent-btn text-base"></span>
                                </div>
                                
                                <!-- Sunset -->
                                <div class="flex flex-col items-center p-2 bg-card-panel rounded-lg shadow-inner">
                                    <span class="font-medium text-text-dark/90 text-xs">Sunset 🌇</span>
                                    <span id="sunsetTime" class="font-bold text-accent-btn text-base"></span>
                                </div>
                                
                            </div>
                        </div>
                    </div>
                    <!-- END: ROW 1 -->

                    <!-- ROW 2: Personalized Tips (Left) and Fun Facts (Right) -->
                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                        
                        <!-- LEFT COLUMN: Personalized Tips (B) -->
                        <div id="personalizedTipCard" class="bg-base-bg p-4 rounded-xl shadow-md border-l-4 border-accent-btn/80 transition duration-300">
                            <h4 class="font-serif font-semibold text-text-dark mb-1">Today's Tip:</h4>
                            <p id="weatherTipText" class="text-sm text-text-dark/90"></p>
                        </div>
                        
                        <!-- RIGHT COLUMN: Fun Facts (I) -->
                        <div id="funFactCard" class="bg-base-bg p-4 rounded-xl shadow-md border-l-4 border-accent-btn/80 transition duration-300">
                            <h4 class="font-serif font-semibold text-text-dark mb-1">Weather Trivia:</h4>
                            <p id="funFactText" class="text-sm text-text-dark/90 italic"></p>
                        </div>
                        
                    </div>
                    <!-- END: ROW 2 -->
                    
                </div>
                <!-- END: RIGHT COLUMN (Dashboard) -->

            </div>
            <!-- END: Main Split Layout -->
            
        </div>

        <div id="errorMessage" class="hidden mt-4 p-4 bg-red-100 border border-red-400 text-red-700 rounded-lg text-center font-medium"></div>
    </main>
    

    <!-- Footer -->
    <footer class="w-full max-w-4xl mt-10 p-4 text-center text-xs text-text-dark/70">
        <p>Powered by <a href="https://openweathermap.org/" target="_blank" class="hover:text-accent-btn underline transition-colors">OpenWeather</a> & <a href="https://giphy.com/" target="_blank" class="hover:text-accent-btn underline transition-colors">Giphy</a></p>
        <p>Design Theme: The Evergreen Retreat</p>
    </footer>

    <!-- Simple Copy Success Message (Replaces alert()) -->
    <div id="copyMessage" class="fixed bottom-4 right-4 bg-text-dark text-base-bg px-4 py-2 rounded-lg shadow-xl opacity-0 transition-opacity duration-300 pointer-events-none">
        Report copied to clipboard!
    </div>

    <script src="app.js"></script>
</body>
</html>
</body>
</html>