// --- API Keys ---
const WEATHER_API_KEY = '1890edc2347371dc3277aa1230b4ab2d';
const GIPHY_API_KEY = 'IEqlK0qf9nDrfSdUwkerU3F2w31xRzPb';
const RECENT_SEARCHES_KEY = 'weatherify_recent_searches';
const MAX_RECENT_SEARCHES = 5;

// --- DOM Elements ---
const cityInput = document.getElementById('cityInput');
const searchButton = document.getElementById('searchButton');
const locationButton = document.getElementById('locationButton');
const loadingText = document.getElementById('loadingText');
const weatherCard = document.getElementById('weatherCard');
const loadingIndicator = document.getElementById('loadingIndicator');
const errorMessage = document.getElementById('errorMessage');
const shareButton = document.getElementById('shareButton');
const copyMessage = document.getElementById('copyMessage');
const suggestions = document.getElementById('suggestions');

const cityAndDate = document.getElementById('cityAndDate');
const currentTemp = document.getElementById('currentTemp');
const weatherDescription = document.getElementById('weatherDescription');
const weatherGif = document.getElementById('weatherGif');

// Original Stats Elements
const feelsLike = document.getElementById('feelsLike');
const humidity = document.getElementById('humidity');
const windSpeed = document.getElementById('windSpeed');
const pressure = document.getElementById('pressure');

// New Insight Elements
const uvIndexValue = document.getElementById('uvIndexValue');
const aqiValue = document.getElementById('aqiValue');
const uvRiskBadge = document.getElementById('uvRiskBadge');
const aqiRiskBadge = document.getElementById('aqiRiskBadge');
const sunriseTime = document.getElementById('sunriseTime');
const sunsetTime = document.getElementById('sunsetTime');
const pop = document.getElementById('pop');
const weatherTipText = document.getElementById('weatherTipText');
const funFactText = document.getElementById('funFactText');
const pinnedCitiesEl = document.getElementById('pinnedCities');

let pinnedCities = [];

// --- Utility Functions ---

/**
 * Simple utility to handle retries for API calls (Exponential Backoff).
 */
async function fetchWithRetry(fetcher, retries = 3) {
    for (let i = 0; i < retries; i++) {
        try {
            return await fetcher();
        } catch (error) {
            console.error(`Attempt ${i + 1} failed:`, error);
            if (i === retries - 1) throw error;
            await new Promise(resolve => setTimeout(resolve, Math.pow(2, i) * 1000));
        }
    }
}

/**
 * Converts UTC timestamp to local time string (HH:MM AM/PM).
 */
function formatTime(timestamp, timezoneOffset) {
    // Convert UTC timestamp to milliseconds
    const utcMillis = timestamp * 1000;

    // Calculate local time by adding the timezone offset (in seconds)
    const localMillis = utcMillis + (timezoneOffset * 1000);

    // Create a Date object from the local time in milliseconds
    const localDate = new Date(localMillis);

    // Format to HH:MM AM/PM in the specified city's local time
    return localDate.toLocaleTimeString('en-US', {
        hour: '2-digit',
        minute: '2-digit',
        hour12: true,
        timeZone: 'UTC' // Crucial: use UTC because we manually applied the offset
    });
}

// --- Simulated Insights Logic (A) ---

/**
 * Simulates UV Index, AQI, and PoP based on main weather condition.
 * UV Index scale (0-11+): Low(0-2), Moderate(3-5), High(6-7), Very High(8-10), Extreme(11+)
 * AQI scale (0-500): Good(0-50), Moderate(51-100), Unhealthy(101+)
 */
function getInsightsData(mainCondition) {
    let uvIndex, aqi, pop, uvStatus, aqiStatus;

    switch (mainCondition) {
        case 'Clear':
            uvIndex = Math.floor(Math.random() * 4) + 6; // High (6-9)
            aqi = Math.floor(Math.random() * 50) + 51; // Moderate (51-100)
            pop = Math.floor(Math.random() * 15) + 5; // Low PoP (5-20%)
            break;
        case 'Clouds':
        case 'Mist':
        case 'Fog':
            uvIndex = Math.floor(Math.random() * 3) + 3; // Moderate (3-5)
            aqi = Math.floor(Math.random() * 50) + 1; // Good (1-50)
            pop = Math.floor(Math.random() * 30) + 10; // Moderate PoP (10-40%)
            break;
        case 'Rain':
        case 'Drizzle':
        case 'Thunderstorm':
        case 'Snow':
            uvIndex = Math.floor(Math.random() * 3); // Low (0-2)
            aqi = Math.floor(Math.random() * 30) + 1; // Good (1-30)
            pop = Math.floor(Math.random() * 40) + 60; // High PoP (60-100%)
            break;
        default: // Haze, Smoke, Dust, etc.
            uvIndex = Math.floor(Math.random() * 4) + 2; // Low/Moderate (2-5)
            aqi = Math.floor(Math.random() * 50) + 101; // Unhealthy (101-150)
            pop = Math.floor(Math.random() * 30) + 5; // Low/Moderate PoP (5-35%)
    }

    // Determine UV Risk
    if (uvIndex >= 8) { uvStatus = { text: 'High Risk', class: 'badge-high' }; }
    else if (uvIndex >= 3) { uvStatus = { text: 'Moderate', class: 'badge-moderate' }; }
    else { uvStatus = { text: 'Safe', class: 'badge-safe' }; }

    // Determine AQI Risk
    if (aqi >= 101) { aqiStatus = { text: 'Unhealthy', class: 'badge-high' }; }
    else if (aqi >= 51) { aqiStatus = { text: 'Moderate', class: 'badge-moderate' }; }
    else { aqiStatus = { text: 'Good', class: 'badge-safe' }; }

    return { uvIndex, aqi, pop, uvStatus, aqiStatus };
}

/**
 * Updates the Daily Insights section.
 */
function displayInsights(data, mainCondition) {
    const insights = getInsightsData(mainCondition);

    // Sunrise/Sunset Times
    sunriseTime.textContent = formatTime(data.sys.sunrise, data.timezone);
    sunsetTime.textContent = formatTime(data.sys.sunset, data.timezone);

    // Simulated Data
    uvIndexValue.textContent = insights.uvIndex;
    aqiValue.textContent = insights.aqi;
    pop.textContent = `${insights.pop}%`;

    // Badges
    uvRiskBadge.textContent = insights.uvStatus.text;
    uvRiskBadge.className = `px-2 py-0.5 rounded-full text-[10px] font-semibold ${insights.uvStatus.class}`;

    aqiRiskBadge.textContent = insights.aqiStatus.text;
    aqiRiskBadge.className = `px-2 py-0.5 rounded-full text-[10px] font-semibold ${insights.aqiStatus.class}`;
}

// --- Personalized Tips Logic (B) ---

function generateTips(mainCondition) {
    const tips = {
        'Clear': 'Enjoy the sunshine! Remember to apply sun protection. 🔆',
        'Clouds': 'Perfect weather for a walk or outdoor coffee. ☕',
        'Rain': 'Carry an umbrella and wear water-resistant shoes today. ☔',
        'Drizzle': 'Light rain is perfect for cozy indoor reading. 📚',
        'Thunderstorm': 'Seek shelter immediately and stay away from windows. ⚡',
        'Snow': 'Wear warm layers and check transit delays. 🧣',
        'Mist': 'Visibility may be low; drive carefully. 🚗',
        'default': 'A great day to enjoy the beauty of the outdoors! 🌿',
    };
    return tips[mainCondition] || tips['default'];
}

// --- Fun Facts Logic (I) ---

function generateFunFact() {
    const facts = [
        "Did you know: A 'haboob' is a type of intense dust storm that occurs in dry regions.",
        "Did you know: The highest temperature ever recorded on Earth was 56.7°C (134°F) in Death Valley.",
        "Did you know: Rain doesn't actually fall in drops; it's spherical. Wind makes it look like tear drops.",
        "Did you know: Lightning strikes the Earth about 100 times every second.",
        "Did you know: Snowflakes are never identical, but always have six sides.",
    ];
    const randomIndex = Math.floor(Math.random() * facts.length);
    return facts[randomIndex];
}

async function fetchSuggestions(query) {
    if (query.length < 2) {
        suggestions.classList.add('hidden');
        return;
    }

    let searchQuery = query;
    let countryFilter = null;

    // Check if query contains comma (e.g., "pasay,ph")
    const commaIndex = query.indexOf(',');
    if (commaIndex > 0) {
        const cityPart = query.substring(0, commaIndex).trim();
        const countryPart = query.substring(commaIndex + 1).trim().toUpperCase();
        if (countryPart.length === 2) {
            searchQuery = cityPart;
            countryFilter = countryPart;
        }
    }

    const geoUrl = `http://api.openweathermap.org/geo/1.0/direct?q=${encodeURIComponent(searchQuery)}&limit=10&appid=${WEATHER_API_KEY}`;

    try {
        const res = await fetch(geoUrl);
        let data = await res.json();

        // Filter by country if specified
        if (countryFilter) {
            data = data.filter(city => city.country === countryFilter);
        }

        // Limit to 5 results
        data = data.slice(0, 5);

        showSuggestions(data);
    } catch (e) {
        console.log('Suggestions error:', e);
        suggestions.classList.add('hidden');
    }
}

function showSuggestions(data) {
    suggestions.innerHTML = '';
    if (data.length === 0) {
        suggestions.classList.add('hidden');
        return;
    }

    data.forEach(city => {
        const div = document.createElement('div');
        div.className = 'p-2 hover:bg-accent-btn/20 cursor-pointer text-text-dark';
        div.textContent = `${city.name}, ${city.country}`;
        div.addEventListener('click', () => {
            cityInput.value = city.name;
            suggestions.classList.add('hidden');
            getWeatherAndGif({ city: city.name });
        });
        suggestions.appendChild(div);
    });

    suggestions.classList.remove('hidden');
}

// --- Dynamic Visuals (E) ---

function updateVisuals(mainCondition) {
    const body = document.body;
    // Remove all existing background classes
    body.className = body.className.split(' ').filter(c => !c.startsWith('bg-')).join(' ');

    const conditionClass = `bg-${mainCondition.toLowerCase()}`;
    if (body.classList.contains(conditionClass)) {
        return; // Already set
    }

    // Add the new class
    body.classList.add(conditionClass);
}

// --- Core Fetching Logic ---

/**
 * Fetches weather data using either city name or coordinates.
 * @param {{city?: string, lat?: number, lon?: number}} input
 * @returns {Promise<any>}
 */
async function fetchWeatherData(input) {
    let lat, lon, cityName;

    if (input.lat !== undefined && input.lon !== undefined) {
        lat = input.lat;
        lon = input.lon;
    } else if (input.city) {
        // Use geocoding to find coordinates for the city
        const geoUrl = `http://api.openweathermap.org/geo/1.0/direct?q=${encodeURIComponent(input.city)}&limit=1&appid=${WEATHER_API_KEY}`;
        const geoResponse = await fetch(geoUrl);

        if (!geoResponse.ok) {
            throw new Error("Geocoding service unavailable.");
        }

        const geoData = await geoResponse.json();
        if (geoData.length === 0) {
            throw new Error("City not found. Please check the spelling or try a different city name.");
        }

        lat = geoData[0].lat;
        lon = geoData[0].lon;
        cityName = geoData[0].name; // Optional: use for display
    } else {
        throw new Error("Invalid input: must provide city or coordinates.");
    }

    const url = `https://api.openweathermap.org/data/2.5/weather?lat=${lat}&lon=${lon}&appid=${WEATHER_API_KEY}&units=metric`;
    const response = await fetch(url);

    if (!response.ok) {
        const errorData = await response.json();
        throw new Error(errorData.message || `HTTP error! Status: ${response.status}`);
    }
    return response.json();
}

async function fetchGiphy(weatherCondition) {
    const query = mapWeatherToGiphy(weatherCondition);
    const url = `https://api.giphy.com/v1/gifs/translate?api_key=${GIPHY_API_KEY}&s=${query}&weirdness=5`;

    const response = await fetch(url);
    if (!response.ok) {
        console.warn(`Giphy API error: ${response.status}`);
        return null;
    }
    const data = await response.json();
    return data.data && data.data.images ? data.data.images.fixed_height.url : null;
}

function mapWeatherToGiphy(mainCondition) {
    const mappings = {
        // Modified to be more literal representations:
        'Clear': 'clear sky weather',
        'Clouds': 'overcast clouds',
        'Rain': 'heavy rain',
        'Drizzle': 'light drizzle',
        'Thunderstorm': 'thunder storm lightning',
        'Snow': 'falling snow flakes',
        'Mist': 'mist weather',
        'Smoke': 'smoke pollution',
        'Haze': 'haze weather',
        'Dust': 'dust storm',
        'Fog': 'foggy day',
        'Sand': 'sand storm',
        'Ash': 'volcanic ash',
        'Squall': 'wind squall',
        'Tornado': 'tornado spinning',
    };
    // Fallback for an unrecognized weather condition
    return mappings[mainCondition] || `${mainCondition} weather abstract`;
}


/**
 * Displays all fetched data and derived insights.
 */
function displayWeather(data, gifUrl) {
    const mainCondition = data.weather[0].main;
    const description = data.weather[0].description;
    const tempC = Math.round(data.main.temp);
    const wind = (data.wind.speed * 3.6).toFixed(1); // km/h

    // Main Card Data
    cityAndDate.textContent = `${data.name}, ${data.sys.country}`;
    currentTemp.textContent = tempC;
    weatherDescription.textContent = description;

    // Simple Stat Updates (Original OpenWeather Stats)
    feelsLike.textContent = `${Math.round(data.main.feels_like)}°C`;
    humidity.textContent = `${data.main.humidity}%`;
    windSpeed.textContent = `${wind} km/h`;
    pressure.textContent = `${data.main.pressure} hPa`;

    // New Features
    displayInsights(data, mainCondition); // (A) Sunrise/Sunset, Simulated UV/AQI/PoP
    weatherTipText.textContent = generateTips(mainCondition); // (B) Personalized Tips
    funFactText.textContent = generateFunFact(); // (I) Fun Facts
    updateVisuals(mainCondition); // (E) Dynamic Background

    // GIF Display
    weatherGif.src = gifUrl || 'https://placehold.co/200x200/A1BC98/778873?text=GIF+Not+Found';
    weatherGif.alt = gifUrl ? 'Weather animation' : 'Placeholder image for weather';

    // Save last city
    localStorage.setItem('lastCity', data.name);

    // Show UI
    weatherCard.classList.remove('hidden');
    loadingIndicator.classList.add('hidden');
    errorMessage.classList.add('hidden');
    loadingText.textContent = "Fetching data..."; // Reset loading text
}

/**
 * Core function to handle weather and GIF fetching.
 * @param {{city?: string, lat?: number, lon?: number}} input
 */
async function getWeatherAndGif(input) {
    const isLocationBased = input.lat !== undefined;

    // Input validation
    if (!isLocationBased && (!input.city || input.city.trim() === '')) {
        displayError('Please enter a city name or enable location access.');
        return;
    }

    // Reset UI state
    weatherCard.classList.add('hidden');
    errorMessage.classList.add('hidden');
    loadingIndicator.classList.remove('hidden');
    if (isLocationBased) {
        loadingText.textContent = "Fetching weather for your location...";
    } else {
        loadingText.textContent = `Fetching weather for ${input.city}...`;
    }


    try {
        const weatherData = await fetchWithRetry(() => fetchWeatherData(input));
        const mainCondition = weatherData.weather[0].main;

        const gifUrl = await fetchGiphy(mainCondition);

        displayWeather(weatherData, gifUrl);

    } catch (error) {
        console.error("Error in main fetch process:", error);
        loadingIndicator.classList.add('hidden');
        let userMessage = 'Failed to fetch weather data. ';
        if (error.message.includes('404')) {
            userMessage = 'City not found. Please check the spelling.';
        } else if (error.message.includes('HTTP')) {
            userMessage = 'Could not reach the weather service. Please try again later.';
        } else {
            userMessage = 'An unexpected error occurred. Check console for details.';
        }
        displayError(userMessage);
    } finally {
        loadingIndicator.classList.add('hidden');
    }
}

// Geolocation function
function getLocation(fallbackToCityInput = true) {
    loadingIndicator.classList.remove('hidden');
    loadingText.textContent = "Requesting your location...";

    if (navigator.geolocation) {
        navigator.geolocation.getCurrentPosition(
            (position) => {
                // Success: Use coordinates
                const lat = position.coords.latitude;
                const lon = position.coords.longitude;
                getWeatherAndGif({ lat, lon });
            },
            (error) => {
                // Error/Denied: Fallback to last city or input
                console.warn("Geolocation failed or was denied:", error);
                const lastCity = localStorage.getItem('lastCity');
                if (lastCity) {
                    getWeatherAndGif({ city: lastCity });
                } else if (fallbackToCityInput && cityInput.value.trim()) {
                    getWeatherAndGif({ city: cityInput.value.trim() });
                } else {
                    displayError("Location access denied or failed. Please enter a city manually.");
                    loadingIndicator.classList.add('hidden');
                }
            }
        );
    } else {
        // Not supported
        const lastCity = localStorage.getItem('lastCity');
        if (lastCity) {
            getWeatherAndGif({ city: lastCity });
        } else {
            displayError("Geolocation is not supported by this browser. Please enter a city manually.");
            loadingIndicator.classList.add('hidden');
        }
    }
}

function displayError(message) {
    errorMessage.textContent = message;
    errorMessage.classList.remove('hidden');
    weatherCard.classList.add('hidden');
    loadingIndicator.classList.add('hidden');
}


// --- Share Functionality (J) ---
function handleShare() {
    const city = cityAndDate.textContent.split(',')[0].trim();
    const temp = currentTemp.textContent;
    const desc = weatherDescription.textContent.toLowerCase();
    const shareText = `Today in ${city}: ${desc} with a high of ${temp}°C! Check out my personalized weather report on Weatherify.`;

    // Fallback for clipboard API access issues (using execCommand)
    if (document.execCommand) {
        const tempTextArea = document.createElement('textarea');
        tempTextArea.value = shareText;
        document.body.appendChild(tempTextArea);
        tempTextArea.select();
        try {
            document.execCommand('copy');
            showCopySuccess();
        } catch (err) {
            console.error('Copy failed (execCommand fallback):', err);
        }
        document.body.removeChild(tempTextArea);
    } else {
        showCopySuccess("Copy failed. Please copy manually.");
    }
}

function showCopySuccess(message = "Report copied to clipboard!") {
    copyMessage.textContent = message;
    copyMessage.classList.remove('opacity-0', 'pointer-events-none');
    copyMessage.classList.add('opacity-100');
    setTimeout(() => {
        copyMessage.classList.remove('opacity-100');
        copyMessage.classList.add('opacity-0', 'pointer-events-none');
    }, 3000);
}


// --- Event Listeners ---
// Search button now specifically uses the city name input
searchButton.addEventListener('click', () => getWeatherAndGif({ city: cityInput.value.trim() }));

// Location button triggers geolocation with no immediate city fallback
locationButton.addEventListener('click', () => getLocation(false));

cityInput.addEventListener('input', (e) => {
    fetchSuggestions(e.target.value.trim());
});

cityInput.addEventListener('keypress', (e) => {
    if (e.key === 'Enter') {
        suggestions.classList.add('hidden');
        getWeatherAndGif({ city: cityInput.value.trim() });
    }
});

cityInput.addEventListener('blur', () => {
    setTimeout(() => suggestions.classList.add('hidden'), 200);
});
shareButton.addEventListener('click', handleShare);

// --- Pinned Cities Functions ---
async function loadPinnedCities() {
    try {
        const response = await fetch('api/load_pinned.php');
        pinnedCities = await response.json();
        renderPinnedCities();
    } catch (e) {
        console.error('Failed to load pinned cities:', e);
    }
}

async function savePinnedCities() {
    try {
        await fetch('api/save_pinned.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ cities: pinnedCities })
        });
    } catch (e) {
        console.error('Failed to save pinned cities:', e);
    }
}

function renderPinnedCities() {
    pinnedCitiesEl.innerHTML = '';
    if (pinnedCities.length > 0) {
        const title = document.createElement('h3');
        title.textContent = 'Pinned Cities';
        pinnedCitiesEl.appendChild(title);
        pinnedCities.forEach(city => {
            const div = document.createElement('div');
            div.className = 'pinned-city';
            div.innerHTML = `
                <span>${city}</span>
                <button class="view-btn" data-city="${city}">View</button>
                <button class="unpin-btn" data-city="${city}">Unpin</button>
            `;
            pinnedCitiesEl.appendChild(div);
        });
    }
}

// Initialize App on Load
window.onload = async () => {
    // 1. Load pinned cities
    await loadPinnedCities();

    // 2. Set default last city if none
    if (!localStorage.getItem('lastCity')) {
        localStorage.setItem('lastCity', 'London');
    }

    // 3. Initial load: Try to get location first, with fallback to last city
    getLocation(true);
};
