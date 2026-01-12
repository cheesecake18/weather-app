# Weather App (Starter)

This is a small static starter for a weather app using the OpenWeatherMap API.

- Open `src/index.html` in your browser, or serve the folder with a static server.
- Replace `YOUR_API_KEY` in `src/app.js` with your OpenWeatherMap API key (https://openweathermap.org/api).

Quick run options (PowerShell):

```powershell
# 1) Open directly (double-click index.html) or
# 2) Serve with Python (if installed):
python -m http.server 5500; Start-Process "http://localhost:5500/src/index.html"

# 3) Or serve with Node (if you have `npx`):
npx serve .
```

Next steps:
- Add API key storage (.env) or a backend proxy to keep the key secret.
- Improve UI and error handling.
