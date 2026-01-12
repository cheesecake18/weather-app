<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gamified Weather App Demo</title>
    <!-- Load Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    
    <!-- Custom Tailwind Configuration for Cute Sage Theme -->
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        'base': '#F1F3E0', // Very light, pale green (Primary BG)
                        'light-sage': '#D2DCB6', // Light grayish-green (Section BG)
                        'sage': '#A1BC98', // Medium muted green (Primary accent)
                        'dark-sage': '#1C352D', // Dark green (Text/Strong accent)
                        
                        // Status/Accent Colors for contrast and "cute" factor
                        'status-success': '#78C47A', // Soft green for success
                        'status-warn': '#F9DF7C', // Soft yellow for warning
                        'status-error': '#E8A39C', // Soft coral for error
                    }
                }
            }
        }
    </script>
    
    <style>
        /* Inter font setup */
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;700;900&display=swap');
        body {
            font-family: 'Inter', sans-serif;
            background-color: #F1F3E0; /* Base color: Very light green */
            color: #1C352D; /* Dark green text */
        }
        .overlay {
            position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: #a1bc98;
    pointer-events: none;
    z-index: 0;
}
        .section-border {
            border: 2px dotted #778873;
        }
        /* Adjusted bounce effect for the soft color scheme */
        .btn-bounce {
            transition: all 0.1s ease-in-out;
            box-shadow: 0 4px 6px -1px rgba(119, 136, 115, 0.2), 0 2px 4px -2px rgba(119, 136, 115, 0.1);
        }
        .btn-bounce:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 10px rgba(119, 136, 115, 0.3);
        }
        .btn-bounce:active {
            transform: translateY(1px);
            box-shadow: none;
        }
        /* Style for draggable elements */
        .forecast-card {
            cursor: grab;
            transition: transform 0.2s, box-shadow 0.2s;
        }
        .forecast-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 10px 15px rgba(0, 0, 0, 0.1);
        }
        .matching-card {
            min-height: 8rem;
            transition: background-color 0.3s, transform 0.1s;
        }
        /* Darker green for flipped cards (contrast) */
        .matching-card.flipped {
            background-color: #778873; /* dark-sage */
            color: #F1F3E0; /* base */
            transform: scale(0.98);
        }
        /* Medium green for matched cards (success) */
        .matching-card.matched {
            background-color: #A1BC98; /* sage */
            color: #778873;
            pointer-events: none;
            opacity: 0.8;
            animation: match-fade 0.5s ease-out;
        }
        @keyframes match-fade {
            0% { transform: scale(1.1); opacity: 1; }
            100% { transform: scale(1); opacity: 0.8; }
        }

    </style>
</head>
<body class="p-4 md:p-8">
<div class="overlay"></div>

    <div id="app" class="max-w-6xl mx-auto" style="position: relative; z-index: 1;">
        
        <!-- Header: App Title and Gamification Stats -->
        <!-- Changed from bg-white to bg-light-sage -->
        <header class="text-center mb-10 p-6 bg-light-sage shadow-2xl rounded-2xl border-b-4 border-sage">
            <h1 class="text-4xl font-black text-dark-sage mb-2">Weather Playbook</h1>
            <p class="text-lg text-dark-sage/80 mb-6">Interactive Forecasts & Daily Challenges</p>
            
            <div class="flex justify-center items-center space-x-6 text-xl font-semibold">
                <button onclick="window.location.href='index.php'" class="btn-bounce bg-sage hover:bg-dark-sage text-base font-bold py-2 px-4 rounded-lg flex items-center space-x-1">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                    </svg>
                    <span>Back</span>
                </button>
                <!-- Points Display -->
                <!-- Changed internal background to base color for contrast -->
                <div class="flex items-center space-x-2 p-3 bg-base rounded-xl shadow-inner">
                    <span class="text-dark-sage">★</span>
                    <span class="text-dark-sage">Points:</span>
                    <span id="points-display" class="text-dark-sage font-extrabold">0</span>
                </div>
                <!-- Streak Display -->
                <!-- Changed internal background to base color for contrast -->
                <div class="flex items-center space-x-2 p-3 bg-base rounded-xl shadow-inner">
                    <span class="text-status-error">🔥</span>
                    <span class="text-dark-sage">Streak:</span>
                    <span id="streak-display" class="text-dark-sage font-extrabold">1 Day</span>
                </div>
            </div>
        </header>

        <!-- Main Grid Layout -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            
            <!-- COLUMN 1: Forecast & Games -->
            <div class="lg:col-span-2 space-y-8">
                
                <!-- 7-Day Forecast (Draggable Cards) -->
                <!-- Changed from bg-white to bg-light-sage -->
                <section class="bg-light-sage p-6 rounded-2xl shadow-xl section-border">
                    <h2 class="text-2xl font-bold text-dark-sage mb-4 flex justify-between items-center">
                        7-Day Forecast <span class="text-sm font-normal text-sage">(Drag & Drop to reorder!)</span>
                    </h2>
                    <div id="forecast-container" class="grid grid-cols-3 sm:grid-cols-7 gap-3">
                        <!-- Forecast cards will be injected here by JS -->
                    </div>
                </section>

                <!-- Weather Quiz -->
                <!-- Changed from bg-white to bg-light-sage -->
                <section class="bg-light-sage p-6 rounded-2xl shadow-xl border-t-4 border-sage section-border">
                    <h2 class="text-2xl font-bold text-dark-sage mb-4">Daily Challenge: Weather Trivia</h2>
                    <div id="quiz-container">
                        <!-- Quiz questions injected here -->
                    </div>
                    <div id="quiz-message" class="mt-4 p-3 rounded-lg text-center font-bold hidden"></div>
                    <button id="next-quiz-btn" class="btn-bounce bg-sage hover:bg-dark-sage text-base font-bold py-2 px-4 rounded-lg mt-4 w-full" onclick="nextQuestion()">Next Question</button>
                </section>

                <!-- Matching Game -->
                <!-- Changed from bg-white to bg-light-sage -->
                <section class="bg-light-sage p-6 rounded-2xl shadow-xl border-t-4 border-light-sage section-border">
                    <h2 class="text-2xl font-bold text-dark-sage mb-4">Quick Game: Match the Weather</h2>
                    <p class="text-sm text-dark-sage/80 mb-4">Match the weather condition to its corresponding placeholder icon/GIF.</p>
                    <div id="matching-game-container" class="grid grid-cols-4 gap-3">
                        <!-- Matching cards injected here -->
                    </div>
                    <div id="matching-message" class="mt-4 p-3 rounded-lg text-center font-bold hidden"></div>
                </section>

            </div>

            <!-- COLUMN 2: API Mockups & Journal -->
            <div class="lg:col-span-1 space-y-8">

                <!-- Journal Logging -->
                <!-- Changed from bg-white to bg-light-sage -->
                <section class="bg-light-sage p-6 rounded-2xl shadow-xl border-t-4 border-dark-sage section-border">
                    <h2 class="text-2xl font-bold text-dark-sage mb-4">Journal Logging</h2>
                    <!-- Input field background is now base color -->
                    <textarea id="journal-input" class="w-full h-32 p-3 border border-sage rounded-lg focus:ring-sage focus:border-sage resize-none bg-base" placeholder="How did today's weather affect your mood/activities?"></textarea>
                    <button id="save-journal-btn" class="btn-bounce bg-dark-sage hover:bg-sage text-base font-bold py-2 px-4 rounded-lg mt-3 w-full" onclick="saveJournal()">Save Entry</button>
                    <div id="journal-message" class="text-sm mt-3 text-dark-sage"></div>
                </section>

                <!-- Optional API Hooks -->
                <section class="bg-light-sage p-6 rounded-2xl shadow-xl section-border">
                    <h2 class="text-2xl font-bold text-dark-sage mb-6 flex items-center">
                        <svg class="w-6 h-6 mr-2 text-sage" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"></path>
                        </svg>
                        Additional Insights
                    </h2>

                    <div class="grid grid-cols-1 gap-4">
                        <!-- News API Mock -->
                        <div class="p-4 bg-base rounded-xl shadow-lg border-l-4 border-sage hover:shadow-xl transition-shadow duration-300">
                            <div class="flex items-center mb-2">
                                <svg class="w-5 h-5 text-sage mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z"></path>
                                </svg>
                                <h3 class="font-semibold text-dark-sage">Local Weather News</h3>
                            </div>
                            <p class="text-sm text-dark-sage/80 italic">"Heavy rainfall expected this weekend across the coast."</p>
                        </div>

                        <!-- Quote API Mock -->
                        <div class="p-4 bg-base rounded-xl shadow-lg border-l-4 border-sage hover:shadow-xl transition-shadow duration-300">
                            <div class="flex items-center mb-2">
                                <svg class="w-5 h-5 text-sage mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
                                </svg>
                                <h3 class="font-semibold text-dark-sage">Quote of the Day</h3>
                            </div>
                            <p class="text-sm text-dark-sage/80 italic">"A single sunbeam is enough to drive away many shadows."</p>
                        </div>

                        <!-- Trivia API Fun Fact Mock -->
                        <div class="p-4 bg-base rounded-xl shadow-lg border-l-4 border-sage hover:shadow-xl transition-shadow duration-300">
                            <div class="flex items-center mb-2">
                                <svg class="w-5 h-5 text-sage mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"></path>
                                </svg>
                                <h3 class="font-semibold text-dark-sage">Fun Fact</h3>
                            </div>
                            <p class="text-sm text-dark-sage/80">"The highest temperature ever recorded on Earth was 134°F (56.7°C) in Death Valley."</p>
                        </div>
                    </div>
                </section>
                
            </div>
            
        </div>
    </div>

    <script>
        // --- Global State Management (In-Memory for Demo) ---
        let gameState = {
            points: 0,
            streak: 1,
            quiz: {
                currentQuestionIndex: 0,
                correctCount: 0,
            },
            matching: {
                cards: [],
                flippedCards: [],
                matchesFound: 0,
                lockBoard: false,
            }
        };

        const POINTS_PER_QUIZ_CORRECT = 10;
        const POINTS_PER_MATCH = 5;

        // --- Mock Data ---

        const mockForecast = [
            { day: 'MON', temp: 68, condition: 'Sunny', icon: '☀️', details: { humidity: 30, wind: 5 } },
            { day: 'TUE', temp: 62, condition: 'Cloudy', icon: '☁️', details: { humidity: 55, wind: 8 } },
            { day: 'WED', temp: 55, condition: 'Rain', icon: '🌧️', details: { humidity: 80, wind: 15 } },
            { day: 'THU', temp: 58, condition: 'Showers', icon: '🌦️', details: { humidity: 70, wind: 10 } },
            { day: 'FRI', temp: 65, condition: 'Partly Cloudy', icon: '⛅', details: { humidity: 40, wind: 6 } },
            { day: 'SAT', temp: 72, condition: 'Clear', icon: '🔥', details: { humidity: 25, wind: 3 } },
            { day: 'SUN', temp: 70, condition: 'Breezy', icon: '💨', details: { humidity: 35, wind: 12 } },
        ];

        const mockQuizQuestions = [
            {
                question: "What instrument is used to measure air pressure?",
                options: ["Anemometer", "Barometer", "Thermometer", "Hygrometer"],
                answer: "Barometer"
            },
            {
                question: "Which type of cloud is associated with thunderstorms?",
                options: ["Cirrus", "Stratus", "Cumulonimbus", "Altocumulus"],
                answer: "Cumulonimbus"
            },
            {
                question: "What gas makes up most of Earth's atmosphere?",
                options: ["Oxygen", "Carbon Dioxide", "Argon", "Nitrogen"],
                answer: "Nitrogen"
            }
        ];

        const mockMatchingPairs = [
            { value: 'Sunny', icon: '☀️' },
            { value: 'Rain', icon: '🌧️' },
            { value: 'Snow', icon: '❄️' },
            { value: 'Fog', icon: '🌫️' },
        ];

        // --- Utility Functions ---

        /** Updates the Points and Streak display in the header. */
        function updateStatsDisplay() {
            document.getElementById('points-display').textContent = gameState.points;
            document.getElementById('streak-display').textContent = `${gameState.streak} Day${gameState.streak !== 1 ? 's' : ''}`;
            // Save to localStorage
            localStorage.setItem('dashboard_points', gameState.points);
            localStorage.setItem('dashboard_streak', gameState.streak);
        }

        /** Simple array shuffling utility (Fisher-Yates) */
        function shuffle(array) {
            for (let i = array.length - 1; i > 0; i--) {
                const j = Math.floor(Math.random() * (i + 1));
                [array[i], array[j]] = [array[j], array[i]];
            }
            return array;
        }


        // --- 1. Forecast Card Rendering and Drag & Drop ---

        /** Renders a single forecast card HTML */
        function createForecastCard(item) {
            // Using bg-base for card background to stand out slightly from section bg
            const card = document.createElement('div');
            card.className = 'forecast-card bg-base p-3 rounded-lg text-center hover:bg-light-sage transition duration-150 relative group';
            card.setAttribute('draggable', true);
            card.setAttribute('data-day', item.day);
            card.innerHTML = `
                <div class="text-xs font-semibold text-dark-sage">${item.day}</div>
                <div class="text-4xl my-1 transition duration-200 group-hover:scale-110 group-hover:rotate-6">${item.icon}</div>
                <div class="font-bold text-lg text-dark-sage">${item.temp}°F</div>
                <div class="text-xs text-dark-sage/80">${item.condition}</div>
                
                <!-- Hover Effect (Interactive Enhancement) -->
                <div class="absolute inset-0 bg-dark-sage bg-opacity-90 text-base p-3 rounded-lg flex flex-col justify-center opacity-0 group-hover:opacity-100 transition-opacity duration-300 pointer-events-none">
                    <div class="font-semibold mb-1">${item.day} Details</div>
                    <div class="text-sm">Humidity: ${item.details.humidity}%</div>
                    <div class="text-sm">Wind: ${item.details.wind} mph</div>
                </div>
            `;
            
            // Add drag event listeners
            card.addEventListener('dragstart', handleDragStart);
            card.addEventListener('dragover', handleDragOver);
            card.addEventListener('drop', handleDrop);
            card.addEventListener('dragenter', handleDragEnter);
            card.addEventListener('dragleave', handleDragLeave);

            return card;
        }

        /** Renders the full 7-day forecast */
        function renderForecast() {
            const container = document.getElementById('forecast-container');
            container.innerHTML = '';
            mockForecast.forEach(item => {
                container.appendChild(createForecastCard(item));
            });
        }

        // Drag & Drop Handlers
        let draggedItem = null;

        function handleDragStart(e) {
            draggedItem = this;
            e.dataTransfer.effectAllowed = 'move';
            e.dataTransfer.setData('text/html', this.outerHTML);
            setTimeout(() => this.classList.add('opacity-50'), 0);
        }

        function handleDragOver(e) {
            e.preventDefault(); // Allows the drop
            e.dataTransfer.dropEffect = 'move';
        }

        function handleDrop(e) {
            e.stopPropagation();
            if (draggedItem !== this) {
                // Determine insertion point
                const targetRect = this.getBoundingClientRect();
                const center = targetRect.left + (targetRect.width / 2);
                
                if (e.clientX > center) {
                    this.parentNode.insertBefore(draggedItem, this.nextSibling);
                } else {
                    this.parentNode.insertBefore(draggedItem, this);
                }
            }
            this.classList.remove('shadow-lg', 'border-2', 'border-dark-sage');
        }

        function handleDragEnter() {
            if (draggedItem !== this) {
                 this.classList.add('shadow-lg', 'border-2', 'border-dark-sage', 'scale-[1.02]');
            }
        }
        
        function handleDragLeave() {
             this.classList.remove('shadow-lg', 'border-2', 'border-dark-sage', 'scale-[1.02]');
        }

        document.addEventListener('dragend', function(e) {
            if (draggedItem) {
                draggedItem.classList.remove('opacity-50');
                // Clean up any lingering drag enter/leave styles
                document.querySelectorAll('.forecast-card').forEach(card => 
                    card.classList.remove('shadow-lg', 'border-2', 'border-dark-sage', 'scale-[1.02]')
                );
            }
            draggedItem = null;
        });


        // --- 2. Weather Trivia Quiz ---

        /** Renders the current quiz question */
        function renderQuiz() {
            const container = document.getElementById('quiz-container');
            const messageBox = document.getElementById('quiz-message');
            const nextButton = document.getElementById('next-quiz-btn');
            const current = mockQuizQuestions[gameState.quiz.currentQuestionIndex];
            
            if (!current) {
                container.innerHTML = `<p class="text-xl font-bold text-status-success">Quiz Complete! You got ${gameState.quiz.correctCount} out of ${mockQuizQuestions.length} correct!</p>`;
                nextButton.style.display = 'none';
                messageBox.classList.add('hidden');
                return;
            }

            messageBox.classList.add('hidden');
            nextButton.textContent = 'Submit Answer';
            nextButton.onclick = checkQuizAnswer;
            nextButton.style.display = 'block';

            let optionsHtml = current.options.map((option, index) => `
                <label class="block mb-2 cursor-pointer p-2 rounded-lg hover:bg-base transition">
                    <input type="radio" name="quiz-option" value="${option}" class="mr-2 text-sage focus:ring-sage quiz-radio">
                    <span class="text-dark-sage">${option}</span>
                </label>
            `).join('');

            container.innerHTML = `
                <p class="text-lg font-semibold mb-3 text-dark-sage">Q${gameState.quiz.currentQuestionIndex + 1}: ${current.question}</p>
                <form id="quiz-form">${optionsHtml}</form>
            `;
        }

        /** Checks the user's quiz answer and updates score/message */
        function checkQuizAnswer() {
            const form = document.getElementById('quiz-form');
            const selected = form.querySelector('input[name="quiz-option"]:checked');
            const messageBox = document.getElementById('quiz-message');
            const current = mockQuizQuestions[gameState.quiz.currentQuestionIndex];
            const nextButton = document.getElementById('next-quiz-btn');

            if (!selected) {
                messageBox.textContent = "Please select an answer first.";
                messageBox.classList.remove('hidden');
                // Updated warning colors
                messageBox.className = 'mt-4 p-3 rounded-lg text-center font-bold bg-status-warn/60 text-dark-sage';
                return;
            }

            const isCorrect = selected.value === current.answer;
            
            // Provide visual feedback
            const radios = form.querySelectorAll('.quiz-radio');
            radios.forEach(radio => {
                radio.disabled = true;
                const label = radio.closest('label').querySelector('span');
                if (radio.value === current.answer) {
                    // Updated success colors
                    label.classList.add('bg-status-success/50', 'p-1', 'rounded');
                } else if (radio.checked) {
                    // Updated error colors
                    label.classList.add('bg-status-error/50', 'p-1', 'rounded', 'line-through');
                }
            });

            // Update state and message
            if (isCorrect) {
                gameState.points += POINTS_PER_QUIZ_CORRECT;
                gameState.quiz.correctCount++;
                messageBox.textContent = `Correct! +${POINTS_PER_QUIZ_CORRECT} Points!`;
                // Updated success message colors
                messageBox.className = 'mt-4 p-3 rounded-lg text-center font-bold bg-status-success/60 text-dark-sage';
            } else {
                messageBox.textContent = `Incorrect. The answer was: ${current.answer}`;
                // Updated error message colors
                messageBox.className = 'mt-4 p-3 rounded-lg text-center font-bold bg-status-error/60 text-dark-sage';
            }
            
            updateStatsDisplay();
            messageBox.classList.remove('hidden');
            
            nextButton.textContent = 'Next Question';
            nextButton.onclick = nextQuestion;
        }

        /** Moves to the next quiz question */
        function nextQuestion() {
            gameState.quiz.currentQuestionIndex++;
            renderQuiz();
        }


        // --- 3. Weather Matching Game ---

        /** Initializes the matching game board */
        function initMatchingGame() {
            const pairs = mockMatchingPairs.flatMap(p => [
                { id: crypto.randomUUID(), type: 'value', content: p.value, matchId: p.value, isFlipped: false, isMatched: false },
                { id: crypto.randomUUID(), type: 'icon', content: p.icon, matchId: p.value, isFlipped: false, isMatched: false }
            ]);
            gameState.matching.cards = shuffle(pairs);
            gameState.matching.matchesFound = 0;
            renderMatchingGame();
        }

        /** Renders the matching game board */
        function renderMatchingGame() {
            const container = document.getElementById('matching-game-container');
            container.innerHTML = '';
            
            gameState.matching.cards.forEach((card, index) => {
                const cardElement = document.createElement('div');
                cardElement.id = card.id;
                // Card background is base color
                cardElement.className = `matching-card flex items-center justify-center p-3 rounded-xl shadow-md cursor-pointer text-2xl font-bold bg-base hover:bg-light-sage ${card.isFlipped ? 'flipped' : ''} ${card.isMatched ? 'matched' : ''}`;
                cardElement.textContent = (card.isFlipped || card.isMatched) ? card.content : '❓'; // Changed default to soft emoji
                cardElement.onclick = () => flipCard(card.id);
                container.appendChild(cardElement);
            });
            
            // Check for game completion
            const messageBox = document.getElementById('matching-message');
            if (gameState.matching.matchesFound === mockMatchingPairs.length) {
                messageBox.textContent = "Game Complete! You matched all pairs!";
                // Updated success message colors
                messageBox.className = 'mt-4 p-3 rounded-lg text-center font-bold bg-status-success/60 text-dark-sage';
                messageBox.classList.remove('hidden');
            } else {
                messageBox.classList.add('hidden');
            }
        }

        /** Handles a card flip event */
        function flipCard(cardId) {
            const cardIndex = gameState.matching.cards.findIndex(c => c.id === cardId);
            const card = gameState.matching.cards[cardIndex];

            if (gameState.matching.lockBoard || card.isFlipped || card.isMatched) return;

            // 1. Flip the card
            card.isFlipped = true;
            gameState.matching.flippedCards.push(card);
            renderMatchingGame();

            // 2. Handle 0, 1, or 2 cards flipped
            if (gameState.matching.flippedCards.length === 2) {
                checkForMatch();
            }
        }

        /** Logic to check if the two flipped cards match */
        function checkForMatch() {
            gameState.matching.lockBoard = true;
            const [firstCard, secondCard] = gameState.matching.flippedCards;
            const isMatch = firstCard.matchId === secondCard.matchId;

            if (isMatch) {
                disableCards(firstCard.id, secondCard.id);
                gameState.points += POINTS_PER_MATCH;
                gameState.matching.matchesFound++;
                updateStatsDisplay();
            } else {
                unflipCards();
            }
        }

        /** Marks cards as matched (permanent flip) */
        function disableCards(id1, id2) {
            gameState.matching.cards = gameState.matching.cards.map(c => {
                if (c.id === id1 || c.id === id2) {
                    return { ...c, isMatched: true, isFlipped: true };
                }
                return c;
            });
            
            gameState.matching.flippedCards = [];
            gameState.matching.lockBoard = false;
            renderMatchingGame();
        }

        /** Flips non-matching cards back over */
        function unflipCards() {
            setTimeout(() => {
                gameState.matching.cards = gameState.matching.cards.map(c => {
                    // Only unflip if it was one of the two just flipped
                    if (gameState.matching.flippedCards.some(f => f.id === c.id)) {
                        return { ...c, isFlipped: false };
                    }
                    return c;
                });
                
                gameState.matching.flippedCards = [];
                gameState.matching.lockBoard = false;
                renderMatchingGame();
            }, 1000); // Wait 1 second before flipping back
        }


        // --- 4. Journal Logging ---

        /** Saves the journal entry to server */
        async function saveJournal() {
            const input = document.getElementById('journal-input');
            const message = document.getElementById('journal-message');

            if (input.value.trim() === '') {
                message.textContent = 'Journal cannot be empty.';
                // Updated error message colors
                message.className = 'text-sm mt-3 text-status-error font-semibold';
                return;
            }

            try {
                const response = await fetch('../api/save_journal.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ entry: input.value.trim() })
                });
                const result = await response.json();
                if (result.success) {
                    message.textContent = `Entry saved successfully! (${new Date().toLocaleTimeString()})`;
                    // Updated success message colors
                    message.className = 'text-sm mt-3 text-status-success font-semibold';
                    input.value = ''; // Clear for new entry

                    // Optional: Increment streak upon journal log
                    if (gameState.streak < 7) { // Cap streak for demo
                        gameState.streak++;
                        updateStatsDisplay();
                    }
                } else {
                    throw new Error(result.error);
                }
            } catch (e) {
                console.error('Failed to save journal:', e);
                message.textContent = 'Failed to save journal.';
                message.className = 'text-sm mt-3 text-status-error font-semibold';
            }
        }


        // --- Initialization on Load ---
        window.onload = async function() {
            // Load points from localStorage
            gameState.points = parseInt(localStorage.getItem('dashboard_points')) || 0;
            gameState.streak = parseInt(localStorage.getItem('dashboard_streak')) || 1;
            updateStatsDisplay();
            renderForecast();
            renderQuiz();
            initMatchingGame();
        };

    </script>
</body>
</html>